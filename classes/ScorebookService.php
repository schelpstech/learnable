<?php

final class ScorebookService extends TeachingService
{
    public function config() {
        $row=$this->one('SELECT * FROM lhpresultconfig WHERE term=? LIMIT 1',array($this->activeTerm()));
        if(!$row) throw new RuntimeException('Result settings have not been configured for this term.');
        return $row;
    }
    public static function version($record) {
        if (!$record) return 'new';
        $values = array();
        foreach (array('id','score','examscore','totalscore') as $field) $values[$field] = (string)($record[$field] ?? '');
        return hash('sha256',json_encode($values));
    }
    public function sheet($actor,$class,$subject,$week=0) {
        $this->allocation($actor,$class,$subject); $week=self::integer($week,'Week',0,13);
        $learners=$this->rows('SELECT uname,fname FROM lhpuser WHERE classid=? AND status=1 ORDER BY fname,uname',array($class));
        $records=$week ? $this->rows('SELECT * FROM lhpweekrecord WHERE classid=? AND subjid=? AND term=? AND week=? ORDER BY id',array($class,$subject,$this->activeTerm(),'Week '.$week)) : $this->rows('SELECT * FROM lhpresultrecord WHERE classid=? AND subjid=? AND term=? ORDER BY id',array($class,$subject,$this->activeTerm()));
        $map=array();foreach($records as $row) { if(isset($map[$row['lid']])) throw new RuntimeException('Duplicate legacy score rows need administrator review before this sheet can be edited.'); $map[$row['lid']]=$row; }
        foreach($learners as &$learner) { $learner['record']=$map[$learner['uname']] ?? null;$learner['version']=self::version($learner['record']); }unset($learner);
        return $learners;
    }
    public function save($actor,$class,$subject,$week,array $changes) {
        $week=self::integer($week,'Week',0,13);$this->allocation($actor,$class,$subject);
        if(count($changes)>500) throw new InvalidArgumentException('Save no more than 500 learner records at a time.');
        return $this->locked('scores:'.$this->activeTerm().':'.$subject,function() use($actor,$class,$subject,$week,$changes) {
            $term=$this->activeTerm();
            // A named lock also works with deployed MyISAM tables; no legacy schema change is needed.
            $config=$this->one('SELECT * FROM lhpresultconfig WHERE term=? LIMIT 1 FOR UPDATE',array($term));
            if(!$config || (int)$config[$week?'midterm':'status']===1) throw new RuntimeException('Score entry is locked for this term. Please contact the administrator.');
            $roster=array_column($this->rows('SELECT uname FROM lhpuser WHERE classid=? AND status=1',array($class)),'uname');
            $existingRows=$week ? $this->rows('SELECT * FROM lhpweekrecord WHERE subjid=? AND term=? AND week=? FOR UPDATE',array($subject,$term,'Week '.$week)) : $this->rows('SELECT * FROM lhpresultrecord WHERE subjid=? AND term=? FOR UPDATE',array($subject,$term));
            $recordsByLearner=array();
            foreach($existingRows as $existingRow) $recordsByLearner[$existingRow['lid']][]=$existingRow;
            $pending=array();$seen=array();
            foreach($changes as $change) {
                if(!is_array($change)) throw new InvalidArgumentException('Invalid score row.');
                $learner=self::text($change['learner'] ?? '',64);
                if(!in_array($learner,$roster,true) || isset($seen[$learner])) throw new RuntimeException('A learner is outside this class or appears more than once.');
                $seen[$learner]=true;
                $rows=$recordsByLearner[$learner] ?? array();
                if(count($rows)>1) throw new RuntimeException('Duplicate legacy scores require administrator review.');
                $old=$rows ? $rows[0] : null;
                if(($change['version'] ?? '')!==self::version($old)) throw new RuntimeException('Scores changed in another window for '.$learner.'. Reload the sheet before saving.');
                $values=array();
                foreach($week?array('score'=>10):array('score'=>(int)$config['ca_score'],'examscore'=>(int)$config['exam_score']) as $column=>$max) {
                    $input=$change[$column] ?? '';
                    $values[$column]=$input==='' ? ($old[$column] ?? null) : self::integer($input,'Score for '.$learner,0,$max);
                }
                if(!$old && count(array_filter($values,function($v){return $v!==null;}))===0) continue;
                $pending[]=array($learner,$old,$values);
            }
            $parameters=array();$tuples=array();
            foreach($pending as $entry) {
                list($learner,$old,$values)=$entry;
                $score=(int)($values['score'] ?? 0);$exam=(int)($values['examscore'] ?? 0);
                if($week) {
                    $tuples[]='(?,?,?,?,?,?,?)';
                    $parameters=array_merge($parameters,array($old['id'] ?? null,$term,'Week '.$week,$class,$subject,$learner,$score));
                } else {
                    $tuples[]='(?,?,?,?,?,?,?,?)';
                    $parameters=array_merge($parameters,array($old['id'] ?? null,$term,$class,$subject,$learner,$score,$exam,$score+$exam));
                }
            }
            if($tuples) {
                $batch=bin2hex(random_bytes(16));
                $this->audit('scores',$batch,$actor,'save_requested',array('term'=>$term,'class'=>$class,'subject'=>$subject,'week'=>$week),$pending);
                // Validate every row above, then save the complete batch in one statement.
                $sql=$week ? 'INSERT INTO lhpweekrecord (id,term,week,classid,subjid,lid,score) VALUES '.implode(',',$tuples).' ON DUPLICATE KEY UPDATE score=VALUES(score)' : 'INSERT INTO lhpresultrecord (id,term,classid,subjid,lid,score,examscore,totalscore) VALUES '.implode(',',$tuples).' ON DUPLICATE KEY UPDATE score=VALUES(score),examscore=VALUES(examscore),totalscore=VALUES(totalscore)';
                $this->execute($sql,$parameters);
                $this->audit('scores',$batch,$actor,'saved',null,array('count'=>count($pending)));
            }
            return count($pending);
        });
    }
}
