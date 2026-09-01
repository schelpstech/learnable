<?php
require __DIR__.'/conf.php';
require_once dirname(__DIR__).'/classes/autoload.php';
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
$db=database_pdo();$h=function($value){return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');};
try{
    if(isset($_GET['classid'])){
        $class=CbtSecurity::positiveInt($_GET['classid'],'Class',1,1000000000);
        $q=$db->prepare('SELECT uname,COALESCE(NULLIF(TRIM(fname),""),uname) AS learner_name FROM lhpuser WHERE classid=? AND status=1 ORDER BY learner_name,uname');$q->execute(array($class));
        echo '<option value="">Select learner</option>';foreach($q as $row)echo '<option value="'.$h($row['uname']).'">'.$h($row['learner_name']).'</option>';exit;
    }
    $session=(new FeeService($db))->activeSession();
    if(isset($_GET['feetype'])){
        $type=CbtSecurity::cleanText($_GET['feetype'],64);
        $q=$db->prepare('SELECT feeid,feename FROM lhpfeelist WHERE classid=? AND session=? AND status=1 ORDER BY feename');$q->execute(array($type,$session['sessionid']));
        echo '<option value="">Select fee</option><option value="PreviousBalance">Previous term outstanding payment</option>';foreach($q as $row)echo '<option value="'.(int)$row['feeid'].'">'.$h($row['feename']).'</option>';exit;
    }
    if(isset($_GET['feeid'])){
        $id=CbtSecurity::positiveInt($_GET['feeid'],'Fee',1,1000000000);$q=$db->prepare('SELECT amount FROM lhpfeelist WHERE feeid=? AND status=1 LIMIT 1');$q->execute(array($id));$amount=$q->fetchColumn();
        if($amount!==false)echo '<option selected value="'.(int)$amount.'">₦'.number_format((int)$amount).'</option>';exit;
    }
    http_response_code(400);echo '<option value="">Choose a valid filter</option>';
}catch(Throwable $e){http_response_code($e instanceof InvalidArgumentException?422:500);echo '<option value="">Unable to load options</option>';}
