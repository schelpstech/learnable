<?php

final class PaymentLedgerService extends SchoolService
{
    public const STATUSES = [1 => 'Confirmed', 2 => 'Pending', 0 => 'Unsuccessful'];
    public const MODES = ['transfer' => 'Bank transfer', 'deposit' => 'Bank deposit', 'epayment' => 'Online payment', 'cash' => 'Cash'];

    public function term($value = null)
    {
        if ($value === null || $value === '') return $this->activeTerm();
        $row = $this->one('SELECT term FROM lpterm WHERE term=? LIMIT 1', [self::text($value, 64)]);
        if (!$row) throw new InvalidArgumentException('Choose a valid academic term.');
        return $row['term'];
    }

    public static function totals(array $accounts)
    {
        $totals = self::balance(0, 0, 0);
        foreach ($accounts as $account) {
            foreach ($totals as $field => $value) $totals[$field] += $account[$field];
        }
        return $totals;
    }

    private static function balance($gross, $discount, $paid)
    {
        $net = $gross - $discount;
        $balance = $net - $paid;
        return ['gross' => $gross, 'discount' => $discount, 'net' => $net, 'paid' => $paid,
            'balance' => $balance, 'outstanding' => max(0, $balance), 'credit' => max(0, -$balance)];
    }

    public function report($term)
    {
        $rows = $this->ledgerRows($this->term($term));
        $names = $this->learnerNames(array_column($rows, 'stdid'));
        $classes = [];
        $learners = [];
        foreach ($rows as $row) {
            $classId = $row['classid'];
            $student = $row['stdid'];
            $learnerKey = $row['learner_key'];
            $className = $row['class_name'] ?? ($classId === '' ? 'No class recorded' : 'Class ' . $classId);
            $classes[$classId]['name'] = $className;
            $classes[$classId]['accounts'][] = ['student' => $student, 'name' => $names[$learnerKey] ?? $student]
                + self::balance((int)$row['gross'], (int)$row['discount'], (int)$row['paid']);
            foreach (['gross', 'discount', 'paid'] as $field) {
                $learners[$learnerKey][$field] = ($learners[$learnerKey][$field] ?? 0) + (int)$row[$field];
            }
        }
        foreach ($classes as &$class) $class['totals'] = self::totals($class['accounts']);
        unset($class);
        $termAccounts = array_map(function ($row) { return self::balance($row['gross'], $row['discount'], $row['paid']); }, $learners);
        return ['classes' => $classes, 'totals' => self::totals($termAccounts)];
    }

    public function learnerBalance($term, $student)
    {
        $student = self::text($student, 64);
        $rows = $this->ledgerRows($this->term($term), $student);
        return self::balance((int)array_sum(array_column($rows, 'gross')),
            (int)array_sum(array_column($rows, 'discount')), (int)array_sum(array_column($rows, 'paid')));
    }

    public function learners($term, $classId)
    {
        $term = $this->term($term);
        $classId = (string)self::integer($classId, 'Class', 1);
        $history = $this->rows("SELECT CONVERT(stdid USING utf8mb4) COLLATE utf8mb4_unicode_ci AS uname
            FROM lhpassignedfee WHERE classid=? AND term=? AND status=1 AND stdid<>''
            UNION SELECT CONVERT(stdid USING utf8mb4) COLLATE utf8mb4_unicode_ci
            FROM lhptransaction WHERE classid=? AND term=? AND stdid<>''", [$classId, $term, $classId, $term]);
        $byId = [];
        foreach ($history as $row) {
            $byId[mb_strtolower($row['uname'])] = $row + ['learner_name' => $row['uname']];
        }

        $params = [$classId];
        $where = '(classid=? AND status=1)';
        if ($history) {
            $where .= ' OR uname IN (' . implode(',', array_fill(0, count($history), '?')) . ')';
            $params = array_merge($params, array_column($history, 'uname'));
        }
        foreach ($this->rows("SELECT uname,COALESCE(NULLIF(fname,''),uname) AS learner_name
            FROM lhpuser WHERE $where ORDER BY id", $params) as $row) {
            $byId[mb_strtolower($row['uname'])] = $row;
        }
        $learners = array_values($byId);
        usort($learners, function ($first, $second) {
            return strnatcasecmp($first['learner_name'], $second['learner_name']);
        });
        return $learners;
    }

    private function ledgerRows($term, $student = null)
    {
        $where = ' WHERE term=? AND status=1';
        $params = [$term];
        if ($student !== null) {
            $where .= ' AND stdid=?';
            $params[] = $student;
        }
        // Aggregate facts before looking up labels so data from either table is counted exactly once.
        return $this->rows("SELECT a.*, LOWER(a.stdid) AS learner_key, c.classname AS class_name
            FROM (SELECT classid,stdid,SUM(gross) AS gross,SUM(discount) AS discount,SUM(paid) AS paid
                FROM (SELECT CAST(COALESCE(classid,'') AS BINARY) AS classid,
                    CONVERT(COALESCE(stdid,'') USING utf8mb4) COLLATE utf8mb4_unicode_ci AS stdid,
                    amount AS gross,COALESCE(discount,0) AS discount,0 AS paid
                    FROM lhpassignedfee $where
                    UNION ALL SELECT CAST(COALESCE(classid,'') AS BINARY),
                        CONVERT(COALESCE(stdid,'') USING utf8mb4) COLLATE utf8mb4_unicode_ci,0,0,amount FROM lhptransaction $where) ledger
                GROUP BY classid,stdid) a
            LEFT JOIN lhpclass c ON CAST(c.classid AS BINARY)=a.classid
            ORDER BY c.classname,a.classid,a.stdid", array_merge($params, $params));
    }

    private function learnerNames(array $ids)
    {
        $names = [];
        $ids = array_values(array_unique(array_filter(array_map('strval', $ids), 'strlen')));
        foreach (array_chunk($ids, 500) as $batch) {
            $placeholders = implode(',', array_fill(0, count($batch), '?'));
            foreach ($this->rows("SELECT uname,COALESCE(NULLIF(fname,''),uname) AS learner_name
                FROM lhpuser WHERE uname IN ($placeholders) ORDER BY id", $batch) as $row) {
                $names[mb_strtolower($row['uname'])] = $row['learner_name'];
            }
        }
        return $names;
    }

    public function payments($term)
    {
        $rows = $this->rows("SELECT t.*,c.classname
            FROM lhptransaction t LEFT JOIN lhpclass c ON CAST(c.classid AS BINARY)=CAST(t.classid AS BINARY)
            WHERE t.term=? ORDER BY t.paydate DESC,t.transid DESC", [$this->term($term)]);
        $names = $this->learnerNames(array_column($rows, 'stdid'));
        foreach ($rows as &$row) {
            $row['learner_name'] = $names[mb_strtolower((string)$row['stdid'])] ?? $row['stdid'];
        }
        unset($row);
        return $rows;
    }

    public function confirmedReceipts($term, $student, $classId)
    {
        return $this->rows("SELECT transid,paydate,reference,mode,amount FROM lhptransaction
            WHERE term=? AND stdid=? AND COALESCE(classid,'')=? AND status=1 ORDER BY paydate,transid",
            [$this->term($term), self::text($student, 64), self::text($classId, 64, true)]);
    }

    public function receiptBalance($term, $student, $classId, array $receipts)
    {
        $charges = $this->one("SELECT COALESCE(SUM(amount),0) AS gross, COALESCE(SUM(discount),0) AS discount
            FROM lhpassignedfee WHERE term=? AND stdid=? AND COALESCE(classid,'')=? AND status=1",
            [$this->term($term), self::text($student, 64), self::text($classId, 64, true)]);
        return self::balance((int)$charges['gross'], (int)$charges['discount'], (int)array_sum(array_column($receipts, 'amount')));
    }

    public function payment($id)
    {
        $row = $this->one('SELECT * FROM lhptransaction WHERE transid=?', [self::integer($id, 'Payment record', 1)]);
        if (!$row) throw new InvalidArgumentException('Payment record could not be found.');
        return $row;
    }

    public function save(array $data)
    {
        $amount = self::integer($data['amount'] ?? '', 'Payment amount', 1, 2147483647);
        $status = self::integer($data['status'] ?? '', 'Payment status', 0, 2);
        $id = self::integer($data['id'] ?? 0, 'Payment record');
        if ($id) {
            $previousAmount = self::integer($data['previous_amount'] ?? '', 'Previous amount', -2147483648, 2147483647);
            $previousStatus = self::integer($data['previous_status'] ?? '', 'Previous status', -2147483648, 2147483647);
            $current = $this->payment($id);
            if ((int)$current['amount'] !== $previousAmount || (int)$current['status'] !== $previousStatus) {
                throw new RuntimeException('This payment changed. Refresh the page before editing it.');
            }
            if ($amount === $previousAmount && $status === $previousStatus) return $id;
            $updated = $this->execute('UPDATE lhptransaction SET amount=?,status=? WHERE transid=? AND amount=? AND status=?',
                [$amount, $status, $id, $previousAmount, $previousStatus]);
            if (!$updated) throw new RuntimeException('This payment changed. Refresh the page before editing it.');
            return $id;
        }

        $term = $this->term(self::text($data['term'] ?? '', 64));
        $student = self::text($data['student'] ?? '', 64);
        $classId = self::integer($data['class_id'] ?? '', 'Class', 1);
        $reference = self::text($data['reference'] ?? '', 254);
        $mode = $data['mode'] ?? '';
        $payDate = self::text($data['paydate'] ?? '', 10);
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $payDate);
        if (!$date || $date->format('Y-m-d') !== $payDate) throw new InvalidArgumentException('Choose a valid payment date.');
        if (!is_string($mode) || !isset(self::MODES[$mode])) throw new InvalidArgumentException('Choose a payment method.');
        if (!$this->one('SELECT classid FROM lhpclass WHERE classid=?', [$classId])) throw new InvalidArgumentException('Choose a valid class.');
        // A historical payment may belong to the learner's recorded class before a promotion.
        $learner = $this->one('SELECT 1 FROM lhpuser WHERE uname=? AND classid=? AND status=1 LIMIT 1', [$student, (string)$classId]);
        $account = $this->one('SELECT 1 FROM lhpassignedfee WHERE stdid=? AND term=? AND classid=? AND status=1
            UNION ALL SELECT 1 FROM lhptransaction WHERE stdid=? AND term=? AND classid=? LIMIT 1',
            [$student, $term, (string)$classId, $student, $term, (string)$classId]);
        if (!$learner && !$account) throw new InvalidArgumentException('Choose a learner associated with this class.');

        return $this->locked('payment:' . $term . ':' . mb_strtolower($student), function () use ($term, $student, $classId, $reference, $mode, $payDate, $amount, $status) {
            if ($this->one('SELECT transid FROM lhptransaction WHERE term=? AND stdid=? AND TRIM(reference)=? LIMIT 1', [$term, $student, $reference])) {
                throw new InvalidArgumentException('This reference already exists for the learner in this term. Find the entry in the payment register to edit or confirm it.');
            }
            $this->execute('INSERT INTO lhptransaction (term,classid,stdid,reference,mode,paydate,amount,status) VALUES (?,?,?,?,?,?,?,?)',
                [$term, (string)$classId, $student, $reference, $mode, $payDate, $amount, $status]);
            return (int)$this->db->lastInsertId();
        });
    }
}
