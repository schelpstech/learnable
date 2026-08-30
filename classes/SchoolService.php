<?php

/** Shared persistence primitives. Controllers own authentication and CSRF; services own data rules. */
abstract class SchoolService
{
    protected $db;
    public function __construct(PDO $db) { $this->db = $db; }
    protected function rows($sql, array $params = array()) {
        $q = $this->db->prepare($sql); $q->execute($params); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    protected function one($sql, array $params = array()) {
        $rows = $this->rows($sql, $params); return $rows ? $rows[0] : null;
    }
    protected function execute($sql, array $params) {
        $q = $this->db->prepare($sql); $q->execute($params); return $q->rowCount();
    }
    protected function transaction(callable $work) {
        $owned = !$this->db->inTransaction();
        if ($owned) $this->db->beginTransaction();
        try { $value = $work(); if ($owned) $this->db->commit(); return $value; }
        catch (Throwable $e) { if ($owned && $this->db->inTransaction()) $this->db->rollBack(); throw $e; }
    }
    public function locked($name, callable $work) {
        $key='learnable:'.substr(hash('sha256',$name),0,48);
        $row=$this->one('SELECT GET_LOCK(?, 10) AS acquired',array($key));
        if(!$row || (int)$row['acquired']!==1) throw new RuntimeException('Another save is in progress. Please try again.');
        try{return $work();}finally{$this->one('SELECT RELEASE_LOCK(?) AS released',array($key));}
    }
    protected function audit($module, $id, $actor, $action, $before, $after) {
        $this->execute('INSERT INTO school_workflow_audit (module,record_id,actor,action,before_json,after_json) VALUES (?,?,?,?,?,?)',
            array($module, (string)$id, $actor, $action, json_encode($before), json_encode($after)));
    }
    public function activeTerm() {
        $row = $this->one('SELECT term FROM lpterm WHERE status = 1 ORDER BY tid DESC LIMIT 1');
        if (!$row) throw new RuntimeException('Set an active academic term before continuing.');
        return $row['term'];
    }
    public static function text($value, $max, $empty = false) { return CbtSecurity::cleanText($value, $max, $empty); }
    public static function integer($value, $label, $min = 0, $max = 1000000000) { return CbtSecurity::positiveInt($value, $label, $min, $max); }
    public static function money($value) {
        if (!is_scalar($value) || !preg_match('/^\d{1,10}(\.\d{1,2})?$/D', (string)$value)) throw new InvalidArgumentException('Enter a valid amount with no more than two decimal places.');
        return number_format((float)$value, 2, '.', '');
    }
    public static function key($key) {
        if (!is_string($key) || !preg_match('/^[a-f0-9]{32}$/D', $key)) throw new InvalidArgumentException('Refresh this form before saving.');
        return $key;
    }
}
