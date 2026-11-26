<?php
require __DIR__ . '/workflow_bootstrap.php';

$ledger = new PaymentLedgerService($workflowDb);
$fees = new FeeService($workflowDb);
$termOptions = $fees->terms();
$term = '';
$classView = $classView ?? false;
$route = $classView ? 'payment-view' : 'payment-reports';
$selectedClass = is_string($_GET['class_id'] ?? null) ? $_GET['class_id'] : null;
$report = ['classes' => [], 'totals' => PaymentLedgerService::totals([])];
try {
    $term = $ledger->term($_GET['term'] ?? null);
    $report = $ledger->report($term);
} catch (Throwable $error) {
    $workflowError = workflow_error($error);
}
$classes = $report['classes'];
$class = $classView && $selectedClass !== null ? ($classes[$selectedClass] ?? null) : null;
$columns = ['gross' => 'Gross billed', 'discount' => 'Discounts', 'net' => 'Net billed',
    'paid' => 'Confirmed payments', 'outstanding' => 'Outstanding', 'credit' => 'Credit', 'balance' => 'Net balance'];
$totals = $class['totals'] ?? $report['totals'];
$classTotals = PaymentLedgerService::totals(array_column($classes, 'totals'));
$workflowTitle = $classView ? 'Class payment details' : 'Payment reports';
$workflowIntro = 'Reconcile active charges and confirmed payments for the selected academic term.';
require __DIR__ . '/workflow_header.php';
?>
<nav class="workspace-subnav no-print" aria-label="Payment management">
    <a href="index.php?route=payments&amp;term=<?php echo rawurlencode($term); ?>">Payment records</a>
    <a href="index.php?route=payment-reports&amp;term=<?php echo rawurlencode($term); ?>">School report</a>
    <a href="index.php?route=payment-view&amp;term=<?php echo rawurlencode($term); ?>">Class details</a>
</nav>

<form method="get" action="index.php" class="workspace-toolbar no-print">
    <input type="hidden" name="route" value="<?php echo wh($route); ?>">
    <label>Academic term
        <select name="term" required>
            <?php foreach ($termOptions as $option): ?>
                <option value="<?php echo wh($option['term']); ?>" <?php echo $term === $option['term'] ? 'selected' : ''; ?>><?php echo wh($option['term']); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <?php if ($classView): ?>
        <label>Class
            <select name="class_id">
                <option value="*">All classes</option>
                <?php foreach ($classes as $id => $entry): ?>
                    <option value="<?php echo wh($id); ?>" <?php echo (string)$id === $selectedClass ? 'selected' : ''; ?>><?php echo wh($entry['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    <?php endif; ?>
    <button class="workspace-button secondary">View report</button>
</form>

<?php if (!$workflowError): ?>
<section class="workspace-card">
    <div class="fee-section-heading">
        <div>
            <p class="workspace-eyebrow"><?php echo wh($term); ?></p>
            <h2><?php echo wh($class['name'] ?? 'School totals'); ?></h2>
        </div>
        <button type="button" data-print class="workspace-button secondary no-print">Print</button>
    </div>
    <div class="workspace-table-wrap">
        <table class="workspace-table">
            <thead><tr><?php foreach ($columns as $label): ?><th scope="col"><?php echo wh($label); ?></th><?php endforeach; ?></tr></thead>
            <tbody><tr><?php foreach ($columns as $key => $label): ?><td class="money">₦<?php echo number_format($totals[$key]); ?></td><?php endforeach; ?></tr></tbody>
        </table>
    </div>
    <p class="workspace-muted">Net balance equals net billed less confirmed payments. Outstanding and credit are shown separately.</p>
</section>

<section class="workspace-card">
    <h2><?php echo $class ? 'Learner balances' : 'Balances by recorded class'; ?></h2>
    <?php if ($classView && !$class && $selectedClass !== null && $selectedClass !== '*'): ?>
        <p class="workspace-muted">This class has no ledger activity in the selected term.</p>
    <?php endif; ?>
    <?php if (!$class && $classTotals['outstanding'] !== $totals['outstanding']): ?>
        <p class="workspace-muted">Some learners have activity in more than one recorded class. The school totals combine all entries for each learner, so class outstanding and credit can differ from the school figures. Net balance reconciles in both views.</p>
    <?php endif; ?>
    <?php if (!$class): ?><p class="workspace-muted">Select a class to see the learners and entries that contribute to its totals.</p><?php endif; ?>
    <div class="workspace-table-wrap">
        <table class="workspace-table">
            <thead><tr>
                <th scope="col"><?php echo $class ? 'Learner' : 'Recorded class'; ?></th>
                <?php foreach ($columns as $label): ?><th scope="col"><?php echo wh($label); ?></th><?php endforeach; ?>
                <?php if ($class): ?><th scope="col">Status</th><?php endif; ?>
            </tr></thead>
            <tbody>
            <?php if ($class): ?>
                <?php foreach ($class['accounts'] as $account): ?>
                    <tr><td><strong><?php echo wh($account['name']); ?></strong><small><?php echo wh($account['student']); ?></small></td>
                    <?php foreach ($columns as $key => $label): ?><td class="money">₦<?php echo number_format($account[$key]); ?></td><?php endforeach; ?>
                    <td><?php echo $account['outstanding'] > 0 ? 'Owing' : ($account['credit'] > 0 ? 'Credit' : 'Settled'); ?></td></tr>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($classes as $id => $entry): ?>
                    <tr><td><a href="index.php?route=payment-view&amp;term=<?php echo rawurlencode($term); ?>&amp;class_id=<?php echo rawurlencode((string)$id); ?>"><?php echo wh($entry['name']); ?></a></td>
                    <?php foreach ($columns as $key => $label): ?><td class="money">₦<?php echo number_format($entry['totals'][$key]); ?></td><?php endforeach; ?></tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$classes): ?><div class="workspace-empty">No active charges or confirmed payments in this term.</div><?php endif; ?>
</section>
<?php endif; ?>
<?php require __DIR__ . '/workflow_footer.php'; ?>
