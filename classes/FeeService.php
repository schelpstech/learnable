<?php

final class FeeService extends SchoolService
{
    public static function version(array $row)
    {
        return hash('sha256', json_encode(array(
            'feeid' => (string)($row['feeid'] ?? ''),
            'feename' => (string)($row['feename'] ?? ''),
            'term' => (string)($row['term'] ?? ''),
            'session' => (string)($row['session'] ?? ''),
            'classid' => (string)($row['classid'] ?? ''),
            'amount' => (string)($row['amount'] ?? ''),
            'status' => (string)($row['status'] ?? ''),
        )));
    }

    public function activeTerm()
    {
        return parent::activeTerm();
    }

    public function activeSession()
    {
        $row = $this->one('SELECT sessionid,session,status FROM lhpsession WHERE status=1 ORDER BY sessionid DESC LIMIT 1');
        if (!$row) throw new RuntimeException('No active academic session is configured.');
        return $row;
    }

    public function terms()
    {
        return $this->rows('SELECT tid,term,status FROM lpterm ORDER BY tid DESC');
    }

    public function classes()
    {
        return $this->rows('SELECT classid,classname FROM lhpclass ORDER BY classname,classid');
    }

    public function learners($classId)
    {
        $classId = self::integer($classId, 'Class', 1);
        return $this->rows(
            'SELECT uname,COALESCE(NULLIF(TRIM(fname),""),uname) AS learner_name FROM lhpuser WHERE classid=? AND status=1 ORDER BY learner_name,uname',
            array($classId)
        );
    }

    public function definitions($sessionId, $status = 'active')
    {
        $sessionId = self::integer($sessionId, 'Academic session', 1);
        if (!$this->one('SELECT sessionid FROM lhpsession WHERE sessionid=? LIMIT 1', array($sessionId))) {
            throw new InvalidArgumentException('Choose a valid academic session.');
        }
        if (!in_array($status, array('active','archived','all'), true)) {
            throw new InvalidArgumentException('Choose a valid fee status.');
        }
        $where = $status === 'all' ? '' : ' AND f.status=' . ($status === 'active' ? '1' : '0');
        return $this->rows(
            'SELECT f.*,c.classname,s.session AS session_name,COALESCE(a.active_assignments,0) AS active_assignments
             FROM lhpfeelist f
             LEFT JOIN lhpclass c ON CAST(c.classid AS CHAR)=f.classid
             LEFT JOIN lhpsession s ON s.sessionid=f.session
             LEFT JOIN (SELECT feeid,COUNT(*) AS active_assignments FROM lhpassignedfee WHERE status=1 GROUP BY feeid) a ON a.feeid=CAST(f.feeid AS CHAR)
             WHERE f.session=?' . $where . ' ORDER BY f.status DESC,f.rectime DESC,f.feeid DESC',
            array($sessionId)
        );
    }

    public function definitionsForTerm($term, $status = 'active')
    {
        $term = $this->term($term);
        return $this->definitions($this->sessionForTerm($term), $status);
    }

    public function definition($id)
    {
        $id = self::integer($id, 'Fee', 1);
        $row = $this->one(
            'SELECT f.*,c.classname,s.session AS session_name,
                (SELECT COUNT(*) FROM lhpassignedfee a WHERE a.feeid=CAST(f.feeid AS CHAR) AND a.status=1) AS active_assignments
             FROM lhpfeelist f LEFT JOIN lhpclass c ON CAST(c.classid AS CHAR)=f.classid
             LEFT JOIN lhpsession s ON s.sessionid=f.session WHERE f.feeid=? LIMIT 1',
            array($id)
        );
        if (!$row) throw new InvalidArgumentException('This fee definition was not found.');
        $row['version'] = self::version($row);
        return $row;
    }

    public function save(array $data, $actor)
    {
        $id = self::integer($data['id'] ?? 0, 'Fee', 0);
        $name = strtoupper(self::text($data['feename'] ?? '', 254));
        $amount = self::integer($data['amount'] ?? '', 'Fee amount', 1, 1000000000);
        $propagate = ($data['propagate'] ?? '') === 'yes';
        if ($id) {
            $expected = (string)($data['version'] ?? '');
            return $this->locked('fee-definition:' . $id, function () use ($id,$name,$amount,$propagate,$expected,$actor) {
                $old = $this->definition($id);
                if ((int)$old['status'] !== 1) throw new RuntimeException('Archived fees cannot be edited.');
                if (!hash_equals($old['version'], $expected)) throw new RuntimeException('This fee changed in another window. Reload it before saving.');
                $duplicate = $this->one('SELECT feeid FROM lhpfeelist WHERE session=? AND classid=? AND UPPER(feename)=? AND feeid<>? LIMIT 1', array($old['session'],$old['classid'],$name,$id));
                if ($duplicate) throw new InvalidArgumentException('A fee with this name already exists for the same session and group.');
                if ($propagate) {
                    $invalid = $this->one('SELECT assid FROM lhpassignedfee WHERE feeid=? AND status=1 AND discount>? LIMIT 1', array((string)$id,$amount));
                    if ($invalid) throw new InvalidArgumentException('The new amount is lower than an existing discount. Review discounts before updating assigned charges.');
                }
                $this->audit('fee_definition',$id,$actor,'save_requested',$old,array('feename'=>$name,'amount'=>$amount,'propagate'=>$propagate));
                $this->execute('UPDATE lhpfeelist SET feename=?,amount=? WHERE feeid=? AND status=1', array($name,$amount,$id));
                if ($propagate) $this->execute('UPDATE lhpassignedfee SET amount=? WHERE feeid=? AND status=1', array($amount,(string)$id));
                $this->audit('fee_definition',$id,$actor,'saved',array('amount'=>$old['amount']),array('amount'=>$amount,'active_assignments_updated'=>$propagate));
                return $id;
            });
        }

        $mode = (string)($data['association_mode'] ?? '');
        if ($mode === 'class') {
            $association = (string)self::integer($data['class_id'] ?? 0, 'Class', 1);
            if (!$this->one('SELECT classid FROM lhpclass WHERE classid=?', array($association))) throw new InvalidArgumentException('Choose a valid class.');
        } elseif ($mode === 'school') {
            $association = strtoupper(self::text($data['fee_group'] ?? 'SCHOOL-WIDE', 64));
            if (ctype_digit($association)) throw new InvalidArgumentException('Use a descriptive school-wide fee group.');
        } else {
            throw new InvalidArgumentException('Choose whether this fee is class-specific or available school-wide.');
        }
        $session = $this->activeSession();
        $sessionId = (int)$session['sessionid'];
        return $this->locked('fee-definition:' . $sessionId . ':' . $association . ':' . $name, function () use ($name,$association,$sessionId,$amount,$actor) {
            if ($this->one('SELECT feeid FROM lhpfeelist WHERE session=? AND classid=? AND UPPER(feename)=? LIMIT 1', array($sessionId,$association,$name))) {
                throw new InvalidArgumentException('A fee with this name already exists for the same session and group. Open it from the register instead.');
            }
            $this->audit('fee_definition','new',$actor,'create_requested',null,array('feename'=>$name,'session'=>$sessionId,'classid'=>$association,'amount'=>$amount));
            $this->execute('INSERT INTO lhpfeelist (feename,session,classid,amount,status) VALUES (?,?,?,?,1)', array($name,$sessionId,$association,$amount));
            $id = (int)$this->db->lastInsertId();
            $this->audit('fee_definition',$id,$actor,'created',null,array('feename'=>$name,'session'=>$sessionId,'classid'=>$association,'amount'=>$amount));
            return $id;
        });
    }

    public function archive($id, $version, $actor, $confirmed)
    {
        $id = self::integer($id, 'Fee', 1);
        if (!$confirmed) throw new InvalidArgumentException('Confirm that you want to archive this fee and deactivate its outstanding assignments.');
        return $this->locked('fee-definition:' . $id, function () use ($id,$version,$actor) {
            $old = $this->definition($id);
            if (!hash_equals($old['version'], (string)$version)) throw new RuntimeException('This fee changed in another window. Reload it before archiving.');
            if ((int)$old['status'] !== 1) return 0;
            $this->audit('fee_definition',$id,$actor,'archive_requested',$old,null);
            $this->execute('UPDATE lhpfeelist SET status=0 WHERE feeid=? AND status=1', array($id));
            $affected = $this->execute('UPDATE lhpassignedfee SET status=0 WHERE feeid=? AND status=1', array((string)$id));
            $this->audit('fee_definition',$id,$actor,'archived',array('active_assignments'=>$affected),null);
            return $affected;
        });
    }

    public function assign(array $data, $actor)
    {
        $term = $this->term($data['term'] ?? '');
        $audience = (string)($data['audience'] ?? '');
        if (!in_array($audience, array('school','class','learner'), true)) throw new InvalidArgumentException('Choose who should receive this fee.');
        $due = (string)($data['due'] ?? '');
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $due);
        if (!$date || $date->format('Y-m-d') !== $due) throw new InvalidArgumentException('Choose a valid payment due date.');
        $feeChoice = (string)($data['fee_id'] ?? '');
        $previous = $feeChoice === 'PreviousBalance';
        if ($previous) {
            $feeId = 'PreviousBalance';
            $amount = self::integer($data['custom_amount'] ?? '', 'Outstanding balance', 1, 1000000000);
            $fee = array('classid'=>'SCHOOL-WIDE','feename'=>'PREVIOUS TERM BALANCE');
        } else {
            $feeId = (string)self::integer($feeChoice, 'Fee', 1);
            $sessionId = $this->sessionForTerm($term);
            $fee = $this->one('SELECT * FROM lhpfeelist WHERE feeid=? AND session=? AND status=1 LIMIT 1', array($feeId,$sessionId));
            if (!$fee) throw new InvalidArgumentException('Choose an active fee from the academic session that contains the selected term.');
            $amount = (int)$fee['amount'];
        }
        $classId = 0; $learner = '';
        if ($audience !== 'school') {
            $classId = self::integer($data['class_id'] ?? 0, 'Class', 1);
            if (!$this->one('SELECT classid FROM lhpclass WHERE classid=?', array($classId))) throw new InvalidArgumentException('Choose a valid class.');
            if (!$previous && ctype_digit((string)$fee['classid']) && (int)$fee['classid'] !== $classId) {
                throw new InvalidArgumentException('This class-specific fee can only be assigned to its own class.');
            }
        } elseif (!$previous && ctype_digit((string)$fee['classid'])) {
            throw new InvalidArgumentException('A class-specific fee cannot be assigned to the whole school. Choose its class instead.');
        }
        if ($audience === 'learner') {
            $learner = self::text($data['learner_id'] ?? '', 64);
            if (!$this->one('SELECT uname FROM lhpuser WHERE uname=? AND classid=? AND status=1 LIMIT 1', array($learner,$classId))) {
                throw new InvalidArgumentException('Choose an active learner from the selected class.');
            }
        }

        return $this->locked('fee-assignment:' . $term . ':' . $feeId, function () use ($term,$audience,$due,$feeId,$amount,$classId,$learner,$actor) {
            $type = array('school'=>'School-Based','class'=>'Class-Based','learner'=>'Learner-Based')[$audience];
            if ($audience === 'learner') {
                $roster=$this->rows('SELECT uname,classid FROM lhpuser WHERE uname=? AND classid=? AND status=1',array($learner,$classId));
            } elseif ($audience === 'class') {
                $roster=$this->rows('SELECT uname,classid FROM lhpuser WHERE classid=? AND status=1 ORDER BY uname',array($classId));
            } else {
                $roster=$this->rows('SELECT uname,classid FROM lhpuser WHERE status=1 AND classid IS NOT NULL AND classid<>"" ORDER BY classid,uname');
            }
            $eligible=count($roster);
            if ($eligible < 1) throw new RuntimeException('No active learners matched this selection.');
            $active=$this->rows('SELECT stdid,classid FROM lhpassignedfee WHERE term=? AND feeid=? AND status=1',array($term,$feeId));
            $activeMap=array();foreach($active as $row)$activeMap[(string)$row['classid'].'|'.(string)$row['stdid']]=true;
            $tuples=array();$params=array();
            foreach($roster as $row){
                $key=(string)$row['classid'].'|'.(string)$row['uname'];if(isset($activeMap[$key]))continue;
                $tuples[]='(?,?,?,?,?,?,?,0,1)';$params=array_merge($params,array($feeId,$row['classid'],$row['uname'],$term,$type,$due,$amount));
            }
            $this->audit('fee_assignment',$term . ':' . $feeId,$actor,'assign_requested',null,array('audience'=>$audience,'class_id'=>$classId,'learner_id'=>$learner,'eligible'=>$eligible));
            $created=0;
            if($tuples)$created=$this->execute('INSERT INTO lhpassignedfee (feeid,classid,stdid,term,type,due,amount,discount,status) VALUES '.implode(',',$tuples),$params);
            $this->audit('fee_assignment',$term . ':' . $feeId,$actor,'assigned',null,array('created'=>$created,'skipped'=>$eligible-$created));
            return array('created'=>$created,'skipped'=>max(0,$eligible-$created),'eligible'=>$eligible);
        });
    }

    public function assignmentSummary($term)
    {
        $term = $this->term($term);
        return $this->one('SELECT COUNT(*) AS records,
            SUM(CASE WHEN status=1 THEN 1 ELSE 0 END) AS active_records,
            COALESCE(SUM(CASE WHEN status=1 THEN amount-COALESCE(discount,0) ELSE 0 END),0) AS active_payable,
            SUM(CASE WHEN status=0 THEN 1 ELSE 0 END) AS inactive_records
            FROM lhpassignedfee WHERE term=?', array($term));
    }

    public function assignments($term, $classId = 0, $status = 'active', $limit = 250)
    {
        $term = $this->term($term);
        if (!in_array($status,array('active','inactive','all'),true)) throw new InvalidArgumentException('Choose a valid assignment status.');
        $params = array($term); $where = 'a.term=?';
        if ($classId) { $classId=self::integer($classId,'Class',1); $where.=' AND a.classid=?'; $params[]=$classId; }
        if ($status !== 'all') { $where.=' AND a.status=?'; $params[]=$status==='active'?1:0; }
        $limit = self::integer($limit,'Limit',1,500);
        return $this->rows('SELECT a.*,COALESCE(NULLIF(TRIM(u.fname),""),a.stdid) AS learner_name,
            COALESCE(c.classname,a.classid) AS class_name,
            CASE WHEN a.feeid="PreviousBalance" THEN "Previous term balance" ELSE COALESCE(f.feename,CONCAT("Fee #",a.feeid)) END AS fee_name
            FROM lhpassignedfee a LEFT JOIN lhpuser u ON u.uname=a.stdid
            LEFT JOIN lhpclass c ON CAST(c.classid AS CHAR)=a.classid
            LEFT JOIN lhpfeelist f ON CAST(f.feeid AS CHAR)=a.feeid
            WHERE '.$where.' ORDER BY a.status DESC,a.rectime DESC,a.assid DESC LIMIT '.$limit, $params);
    }

    public function changeAssignmentStatus($id, $status, $actor, $confirmed = false)
    {
        $id = self::integer($id,'Assigned fee',1); $status=self::integer($status,'Status',0,1);
        if ($status===0 && !$confirmed) throw new InvalidArgumentException('Confirm that you want to deactivate this assigned fee.');
        return $this->locked('assigned-fee:' . $id, function () use ($id,$status,$actor) {
            $old=$this->one('SELECT * FROM lhpassignedfee WHERE assid=? LIMIT 1',array($id));
            if(!$old) throw new InvalidArgumentException('This assigned fee was not found.');
            if((int)$old['status']===$status) return 0;
            if($status===1 && $this->one('SELECT assid FROM lhpassignedfee WHERE term=? AND classid=? AND stdid=? AND feeid=? AND status=1 AND assid<>? LIMIT 1',array($old['term'],$old['classid'],$old['stdid'],$old['feeid'],$id))) {
                throw new RuntimeException('Another active copy of this fee already exists for the learner. Keep this older record inactive.');
            }
            $this->audit('fee_assignment',$id,$actor,'status_requested',array('status'=>$old['status']),array('status'=>$status));
            $this->execute('UPDATE lhpassignedfee SET status=? WHERE assid=?',array($status,$id));
            $this->audit('fee_assignment',$id,$actor,$status?'activated':'deactivated',array('status'=>$old['status']),array('status'=>$status));
            return 1;
        });
    }

    private function term($term)
    {
        $term = self::text($term,64);
        if (!$this->one('SELECT tid FROM lpterm WHERE term=? LIMIT 1',array($term))) throw new InvalidArgumentException('Choose a valid academic term.');
        return $term;
    }

    private function sessionForTerm($term)
    {
        $sessionName = '';
        if (preg_match('/(20\d{2}\s*\/\s*20\d{2})/', $term, $matches)) $sessionName=preg_replace('/\s+/','',$matches[1]);
        $row = $sessionName ? $this->one(
            'SELECT s.sessionid
             FROM lhpsession s
             LEFT JOIN (SELECT session,COUNT(*) AS fee_count FROM lhpfeelist GROUP BY session) f ON f.session=s.sessionid
             WHERE s.session=?
             ORDER BY s.status DESC,COALESCE(f.fee_count,0) DESC,s.sessionid DESC
             LIMIT 1',
            array($sessionName)
        ) : null;
        if (!$row) {
            $active = $this->one('SELECT t.status,s.sessionid FROM lpterm t CROSS JOIN lhpsession s WHERE t.term=? AND s.status=1 LIMIT 1',array($term));
            if (!$active || (int)$active['status']!==1) throw new RuntimeException('The academic session for this term could not be resolved. Check Academic terms before creating the fee.');
            $row=$active;
        }
        return (int)$row['sessionid'];
    }
}
