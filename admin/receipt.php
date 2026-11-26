<?php
require __DIR__ . '/workflow_bootstrap.php';

$ledger = new PaymentLedgerService($workflowDb);
$receiptRows = [];
$term = '';
try {
    $term = $ledger->term($_GET['term'] ?? null);
    $student = PaymentLedgerService::text($_GET['lid'] ?? '', 64);
    $classId = PaymentLedgerService::text($_GET['classid'] ?? '', 64, true);
    $receiptRows = $ledger->confirmedReceipts($term, $student, $classId);
    if (!$receiptRows) throw new InvalidArgumentException('No confirmed payments were found for this learner, class and term.');
    $balance = $ledger->receiptBalance($term, $student, $classId, $receiptRows);

    $query = $workflowDb->prepare(
        'SELECT (SELECT fname FROM lhpuser WHERE uname=? ORDER BY id DESC LIMIT 1) AS learner_name,
                (SELECT classname FROM lhpclass WHERE CAST(classid AS CHAR)=? LIMIT 1) AS class_name'
    );
    $query->execute([$student, $classId]);
    $labels = $query->fetch();
    $school = $workflowDb->query('SELECT schname,address,phone,email FROM lhpschool LIMIT 1')->fetch() ?: [];
} catch (Throwable $error) {
    $workflowError = workflow_error($error);
}
$workflowTitle = 'Term payment receipt';
$workflowIntro = $term;
require __DIR__ . '/workflow_header.php';
?>
<nav class="workspace-subnav no-print" aria-label="Receipt navigation">
    <a href="index.php?route=payments&amp;term=<?php echo rawurlencode($term); ?>">Back to payment records</a>
</nav>
<?php if (!$workflowError): ?>
<section class="workspace-card">
    <div class="fee-section-heading">
        <div>
            <h2><?php echo wh($school['schname'] ?? 'School'); ?></h2>
            <p><?php echo wh($school['address'] ?? ''); ?></p>
            <p><?php echo wh(trim(($school['phone'] ?? '') . ' ' . ($school['email'] ?? ''))); ?></p>
        </div>
        <button type="button" data-print class="workspace-button secondary no-print">Print receipt</button>
    </div>
    <p><strong><?php echo wh($labels['learner_name'] ?? $student); ?></strong>
        · <?php echo wh($student); ?> · <?php echo wh($labels['class_name'] ?? ($classId !== '' ? 'Class ' . $classId : 'No class recorded')); ?>
        · <?php echo wh($term); ?></p>
    <div class="workspace-table-wrap">
        <table class="workspace-table">
            <thead><tr><th>Date</th><th>Reference</th><th>Method</th><th>Confirmed payment</th></tr></thead>
            <tbody>
                <?php foreach ($receiptRows as $receipt): ?>
                <tr>
                    <td><?php echo wh($receipt['paydate']); ?></td>
                    <td><?php echo wh($receipt['reference']); ?></td>
                    <td><?php echo wh(PaymentLedgerService::MODES[$receipt['mode']] ?? $receipt['mode']); ?></td>
                    <td class="money">₦<?php echo number_format((int)$receipt['amount']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot><tr><th colspan="3">Total confirmed</th><th class="money">₦<?php echo number_format($balance['paid']); ?></th></tr></tfoot>
        </table>
    </div>
    <div class="workspace-stats">
        <div><span>Gross billed</span><strong>₦<?php echo number_format($balance['gross']); ?></strong></div>
        <div><span>Discounts</span><strong>₦<?php echo number_format($balance['discount']); ?></strong></div>
        <div><span>Net billed</span><strong>₦<?php echo number_format($balance['net']); ?></strong></div>
        <div><span>Outstanding</span><strong>₦<?php echo number_format($balance['outstanding']); ?></strong></div>
        <div><span>Credit</span><strong>₦<?php echo number_format($balance['credit']); ?></strong></div>
    </div>
    <p>School bursar signature ____________________</p>
</section>
<?php endif; ?>
<?php require __DIR__ . '/workflow_footer.php'; ?>
