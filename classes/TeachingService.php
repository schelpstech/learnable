<?php

abstract class TeachingService extends SchoolService
{
    public function allocations($actor) {
        return $this->rows('SELECT DISTINCT a.classid,a.sbjid,s.sbjname,c.classname FROM lhpalloc a JOIN lhpsubject s ON s.sbjid=a.sbjid JOIN lhpclass c ON c.classid=a.classid WHERE a.staffid=? AND a.term=? ORDER BY c.classname,s.sbjname',array($actor,$this->activeTerm()));
    }
    public function allocation($actor,$class,$subject) {
        $row=$this->one('SELECT a.aid,s.sbjname,c.classname FROM lhpalloc a JOIN lhpsubject s ON s.sbjid=a.sbjid JOIN lhpclass c ON c.classid=a.classid WHERE a.staffid=? AND a.term=? AND a.classid=? AND a.sbjid=? LIMIT 1',array($actor,$this->activeTerm(),$class,$subject));
        if(!$row) throw new RuntimeException('This class and subject are not allocated to your account.');
        return $row;
    }
}
