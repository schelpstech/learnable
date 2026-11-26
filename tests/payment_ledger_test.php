<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../classes/autoload.php';
require __DIR__ . '/../config/database.php';

$db = database_pdo();
// Connection-local fixtures shadow the real tables for this test only.
$schema = [
    'lpterm' => 'tid INT PRIMARY KEY, term VARCHAR(64), status INT',
    'lhpclass' => 'classid INT PRIMARY KEY, classname VARCHAR(16)',
    'lhpstaff' => 'sname VARCHAR(64), staffname VARCHAR(64), role CHAR(1), status INT',
    'lhpuser' => 'id INT PRIMARY KEY, uname VARCHAR(64), fname VARCHAR(64), classid VARCHAR(64), status INT',
    'lhpassignedfee' => 'stdid VARCHAR(64), term VARCHAR(64), classid VARCHAR(64), amount INT, discount INT, status INT',
    'lhptransaction' => 'transid INT PRIMARY KEY AUTO_INCREMENT, stdid VARCHAR(64), term VARCHAR(64), classid VARCHAR(64), amount INT, status INT, mode VARCHAR(64), paydate DATE, reference VARCHAR(254)',
];
foreach ($schema as $table => $columns) {
    $db->exec("CREATE TEMPORARY TABLE `$table` ($columns)");
}
$db->exec("INSERT INTO lpterm VALUES (1,'Old term',0),(2,'Current term',1)");
$db->exec("INSERT INTO lhpclass VALUES (1,'Class 1'),(2,'Class 2')");
$db->exec("INSERT INTO lhpstaff VALUES ('qa-bursar','Test Bursar','b',1)");
$db->exec("INSERT INTO lhpuser VALUES (1,'moved','Moved learner',2,1),(2,'moved','Duplicate name row',2,0),(3,'owing','Owing learner',1,1)");
$db->exec("INSERT INTO lhpassignedfee VALUES
    ('moved','Current term','1',100,25,1),('moved','Current term','1',50,5,1),
    ('moved','Current term','1',1000,0,0),('moved','Old term','1',500,0,1),
    ('owing','Current term','1',150,0,1),('split','Current term','1',35,0,1)");
$db->exec("INSERT INTO lhptransaction (stdid,term,classid,amount,status,mode,paydate,reference) VALUES
    ('moved','Current term','1',70,1,'cash','2026-11-25','receipt-A'),
    ('moved','Current term','1',50,1,'cash','2026-11-25','receipt-B'),
    ('moved','Current term','1',800,2,'cash','2026-11-25','pending'),
    ('moved','Current term','1',900,0,'cash','2026-11-25','failed'),
    ('moved','Old term','1',20,1,'cash','2026-11-25','prior'),
    ('payment-only','Current term','99',40,1,'cash','2026-11-25','unmatched'),
    ('split','Current term','2',35,1,'cash','2026-11-25','other-class')");

function checkLedger($condition, $message)
{
    if (!$condition) throw new RuntimeException($message);
    echo "PASS $message\n";
}

$service = new PaymentLedgerService($db);
$access = new StaffAccess($db, ['auth_account_type' => 'staff', 'auth_username' => 'qa-bursar']);
foreach (['fees','fee-assignments','discounts','payments','payment-reports','payment-view'] as $route) {
    checkLedger($access->financial() && $access->canRoute($route), 'Bursary role can access ' . $route . '.');
}
$term = $service->term();
$report = $service->report($term);
$school = $report['totals'];
$classOne = $report['classes'][1];
checkLedger($school === ['gross'=>335,'discount'=>30,'net'=>305,'paid'=>195,'balance'=>110,'outstanding'=>150,'credit'=>40], 'School figures reconcile by learner and selected term.');
$classTotals = PaymentLedgerService::totals(array_column($report['classes'], 'totals'));
checkLedger($classTotals['gross'] === $school['gross'] && $classTotals['paid'] === $school['paid']
    && $classTotals['balance'] === $school['balance'] && $classTotals['outstanding'] === 185 && $classTotals['credit'] === 75,
    'Class balances retain recorded class and explain cross-class differences.');
$moved = array_column($classOne['accounts'], null, 'student')['moved'];
checkLedger($moved['outstanding'] === 0 && $moved['credit'] === 0 && $moved['gross'] === 150,
    'Discounted payment settles the learner without multiplying rows or moving classes.');
checkLedger($service->learnerBalance($term, 'moved')['balance'] === 0 && $service->learnerBalance('Old term', 'moved')['balance'] === 480,
    'Payment preview includes discounts and confirmed payments from the chosen term only.');
$receipts = $service->confirmedReceipts($term, 'moved', '1');
checkLedger(count($service->payments($term)) === 6 && count($receipts) === 2,
    'The register retains every status and receipts include only confirmed payments.');
checkLedger($service->receiptBalance($term, 'moved', '1', $receipts)['balance'] === 0,
    'The term receipt applies discounts and reconciles with its listed confirmed payments.');
checkLedger(in_array('moved', array_column($service->learners($term, 1), 'uname'), true), 'The recorded class can receive a payment for a moved learner.');

$payment = ['student'=>'moved','term'=>$term,'class_id'=>1,'amount'=>10,'status'=>2,
    'mode'=>'cash','paydate'=>'2026-11-25','reference'=>'new-reference'];
$id = $service->save($payment);
checkLedger($service->learnerBalance($term, 'moved')['balance'] === 0, 'Pending payment leaves the balance intact.');
try {
    $service->save($payment);
    throw new RuntimeException('Duplicate reference was accepted.');
} catch (InvalidArgumentException $expected) {
    checkLedger(true, 'A repeated payment reference for this learner and term is refused.');
}
$service->save(['id'=>$id,'amount'=>10,'status'=>1,'previous_amount'=>10,'previous_status'=>2]);
checkLedger($service->learnerBalance($term, 'moved')['credit'] === 10, 'Confirming the receipt updates the credit.');
try {
    $service->save(['id'=>$id,'amount'=>20,'status'=>1,'previous_amount'=>10,'previous_status'=>2]);
    throw new RuntimeException('Stale edit was accepted.');
} catch (RuntimeException $expected) {
    checkLedger($expected->getMessage() !== 'Stale edit was accepted.', 'A stale payment edit is refused.');
}
