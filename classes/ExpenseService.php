<?php

final class ExpenseService extends SchoolService
{
    public function records($from, $to) {
        return $this->rows('SELECT * FROM school_expenses WHERE expense_date BETWEEN ? AND ? ORDER BY expense_date DESC,id DESC', array(self::date($from),self::date($to)));
    }
    public function get($id) { return $this->one('SELECT * FROM school_expenses WHERE id=?', array(self::integer($id,'Expense',1))); }
    public static function date($value) {
        $date = is_string($value) ? DateTimeImmutable::createFromFormat('!Y-m-d',$value) : false;
        if (!$date || $date->format('Y-m-d') !== $value) throw new InvalidArgumentException('Enter a valid date.');
        return $value;
    }
    public function save(array $data, $actor) {
        $values = array(self::date($data['expense_date'] ?? ''), self::text($data['category'] ?? '',80),
            self::text($data['payee'] ?? '',160), self::text($data['description'] ?? '',500), self::money($data['amount'] ?? ''),
            self::text($data['method'] ?? '',24), self::text($data['reference'] ?? '',100,true));
        if ((float)$values[4] <= 0 || !in_array($values[5],array('Cash','Bank transfer','Card','Other'),true)) throw new InvalidArgumentException('Enter a positive amount and select a payment method.');
        return $this->transaction(function () use ($data,$actor,$values) {
            $id = self::integer($data['id'] ?? 0,'Expense'); $before = null;
            if ($id) {
                $before = $this->one('SELECT * FROM school_expenses WHERE id=? FOR UPDATE',array($id));
                if (!$before || $before['status'] !== 'recorded') throw new RuntimeException('Only recorded expenses can be edited.');
                if ((int)$before['version'] !== self::integer($data['version'] ?? 0,'Version')) throw new RuntimeException('This expense changed. Reload it before saving.');
                $this->execute('UPDATE school_expenses SET expense_date=?,category=?,payee=?,description=?,amount=?,method=?,reference=?,version=version+1 WHERE id=?', array_merge($values,array($id)));
            } else {
                $key = self::key($data['request_key'] ?? '');
                $existing = $this->one('SELECT id FROM school_expenses WHERE request_key=?',array($key));
                if ($existing) return (int)$existing['id'];
                $this->execute('INSERT INTO school_expenses (expense_date,category,payee,description,amount,method,reference,request_key,created_by) VALUES (?,?,?,?,?,?,?,?,?)',array_merge($values,array($key,$actor)));
                $id = (int)$this->db->lastInsertId();
            }
            $this->audit('expense',$id,$actor,$before ? 'edit' : 'create',$before,$this->get($id)); return $id;
        });
    }
    public function void($id, $version, $reason, $actor, $confirmed) {
        if (!$confirmed) throw new InvalidArgumentException('Please confirm the expense cancellation.');
        $reason = self::text($reason,500);
        return $this->transaction(function () use ($id,$version,$reason,$actor) {
            $row = $this->one('SELECT * FROM school_expenses WHERE id=? FOR UPDATE',array(self::integer($id,'Expense',1)));
            if (!$row || $row['status'] !== 'recorded' || (int)$row['version'] !== (int)$version) throw new RuntimeException('The expense changed. Reload and try again.');
            $this->execute("UPDATE school_expenses SET status='void',void_reason=?,version=version+1 WHERE id=?",array($reason,$id));
            $this->audit('expense',$id,$actor,'void',$row,array('reason'=>$reason));
        });
    }
}
