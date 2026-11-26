<?php
require __DIR__ . '/workflow_bootstrap.php';

$ledger = new PaymentLedgerService($workflowDb);
$feeService = new FeeService($workflowDb);
$edit = null;
$payments = [];
$form = [];
$term = '';
$selectedStatus = is_string($_GET['status'] ?? null) ? $_GET['status'] : 'all';
if (!in_array($selectedStatus, ['all', '0', '1', '2'], true)) $selectedStatus = 'all';
$termOptions = $feeService->terms();
$classes = $feeService->classes();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', 'admin');
        $id = $ledger->save($_POST);
        $payment = $ledger->payment($id);
        $_SESSION['workflow_notice'] = 'Payment record saved.';
        header('Location: index.php?' . http_build_query(['route' => 'payments', 'term' => $payment['term']]), true, 303);
        exit;
    } catch (Throwable $error) {
        $workflowError = workflow_error($error);
        $form = array_filter($_POST, 'is_scalar');
    }
}

try {
    if (isset($_GET['id'])) $edit = $ledger->payment($_GET['id']);
    $term = $ledger->term($edit['term'] ?? $_GET['term'] ?? null);
    $payments = $ledger->payments($term);
} catch (Throwable $error) {
    $workflowError = workflow_error($error);
}
$form = $form ?: ($edit ?? []);
$totals = [0 => 0, 1 => 0, 2 => 0];
foreach ($payments as $payment) {
    $status = (int)$payment['status'];
    if (isset($totals[$status])) $totals[$status] += (int)$payment['amount'];
}
$visible = array_filter($payments, function ($payment) use ($selectedStatus) {
    return $selectedStatus === 'all' || $selectedStatus === (string)$payment['status'];
});
$termQuery = rawurlencode($term);
$workflowTitle = 'Payment records';
$workflowIntro = 'Review receipts by term, record a payment, or update its confirmation status.';
$workflowScripts = ['../assets/js/payment-register.js?v=1'];
require __DIR__ . '/workflow_header.php';
?>
<nav class="workspace-subnav no-print" aria-label="Payment management">
    <a class="active" href="index.php?route=payments&amp;term=<?php echo $termQuery; ?>">Payment records</a>
    <a href="index.php?route=payment-reports&amp;term=<?php echo $termQuery; ?>">School report</a>
    <a href="index.php?route=payment-view&amp;term=<?php echo $termQuery; ?>">Class details</a>
</nav>
<form method="get" action="index.php" class="workspace-toolbar no-print">
    <input type="hidden" name="route" value="payments">
    <label>Academic term<select name="term" required>
        <?php foreach ($termOptions as $option): ?>
            <option value="<?php echo wh($option['term']); ?>" <?php echo $term === $option['term'] ? 'selected' : ''; ?>><?php echo wh($option['term']); ?></option>
        <?php endforeach; ?>
    </select></label>
    <label>Payment status<select name="status">
        <option value="all">All statuses</option>
        <?php foreach (PaymentLedgerService::STATUSES as $status => $label): ?>
            <option value="<?php echo $status; ?>" <?php echo $selectedStatus === (string)$status ? 'selected' : ''; ?>><?php echo wh($label); ?></option>
        <?php endforeach; ?>
    </select></label>
    <button class="workspace-button secondary">Apply filters</button>
</form>
<?php if ($term !== ''): ?>
<div class="workspace-stats">
    <?php foreach (PaymentLedgerService::STATUSES as $status => $label): ?>
    <div><span><?php echo wh($label); ?> · whole term</span><strong>₦<?php echo number_format($totals[$status]); ?></strong></div>
    <?php endforeach; ?>
</div>
<section class="workspace-card no-print">
    <h2><?php echo $edit ? 'Edit payment #' . (int)$edit['transid'] : 'Record a payment'; ?></h2>
    <p class="workspace-muted"><?php echo wh($term); ?> · Only confirmed payments reduce the learner’s balance.</p>
    <form method="post" action="index.php?route=payments&amp;term=<?php echo $termQuery; ?><?php if ($edit) echo '&amp;id=' . (int)$edit['transid']; ?>"
        <?php if (!$edit): ?>data-payment-entry data-selected-learner="<?php echo wh($form['student'] ?? ''); ?>"<?php endif; ?>>
        <input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>">
        <input type="hidden" name="term" value="<?php echo wh($term); ?>">
        <?php if ($edit): ?>
            <input type="hidden" name="id" value="<?php echo (int)$edit['transid']; ?>">
            <input type="hidden" name="previous_amount" value="<?php echo wh($form['previous_amount'] ?? $edit['amount']); ?>">
            <input type="hidden" name="previous_status" value="<?php echo wh($form['previous_status'] ?? $edit['status']); ?>">
            <p><?php echo wh($edit['stdid'] . ' · ' . $edit['reference'] . ' · ' . $edit['paydate']); ?></p>
        <?php endif; ?>
        <div class="workspace-fields">
        <?php if (!$edit): ?>
            <label>Recorded class<select name="class_id" required>
                <option value="">Choose a class</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?php echo (int)$class['classid']; ?>" <?php echo (string)$class['classid'] === ($form['class_id'] ?? '') ? 'selected' : ''; ?>><?php echo wh($class['classname']); ?></option>
                <?php endforeach; ?>
            </select></label>
            <label>Learner<select name="student" required><option value="">Choose a class first</option></select>
                <small data-payment-balance role="status"></small>
            </label>
            <label>Payment date<input type="date" name="paydate" value="<?php echo wh($form['paydate'] ?? date('Y-m-d')); ?>" required></label>
            <label>Reference<input name="reference" maxlength="254" value="<?php echo wh($form['reference'] ?? ''); ?>" required></label>
            <label>Method<select name="mode" required>
                <option value="">Choose a method</option>
                <?php foreach (PaymentLedgerService::MODES as $value => $label): ?>
                    <option value="<?php echo wh($value); ?>" <?php echo ($form['mode'] ?? '') === $value ? 'selected' : ''; ?>><?php echo wh($label); ?></option>
                <?php endforeach; ?>
            </select></label>
        <?php endif; ?>
            <label>Amount (₦)<input type="number" name="amount" min="1" max="2147483647" step="1" value="<?php echo wh($form['amount'] ?? ''); ?>" required></label>
            <label>Payment status<select name="status" required>
                <option value="">Choose a status</option>
                <?php foreach (PaymentLedgerService::STATUSES as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo (string)($form['status'] ?? '') === (string)$value ? 'selected' : ''; ?>><?php echo wh($label); ?></option>
                <?php endforeach; ?>
            </select></label>
        </div>
        <div class="workspace-actions">
            <button class="workspace-button"><?php echo $edit ? 'Save changes' : 'Record payment'; ?></button>
            <?php if ($edit): ?><a class="workspace-button secondary" href="index.php?route=payments&amp;term=<?php echo $termQuery; ?>">Cancel</a><?php endif; ?>
        </div>
    </form>
</section>
<section class="workspace-card">
    <div class="fee-section-heading"><h2>Payment register</h2><button type="button" data-print class="workspace-button secondary no-print">Print</button></div>
    <label class="no-print">Find a payment<input type="search" data-table-search="#payment-table" placeholder="Learner, class or reference"></label>
    <div class="workspace-table-wrap">
        <table class="workspace-table" id="payment-table">
            <thead><tr><th>Date</th><th>Learner / recorded class</th><th>Reference</th><th>Method</th><th>Amount</th><th>Status</th><th class="no-print">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($visible as $payment): ?>
                <tr>
                    <td><?php echo wh($payment['paydate']); ?></td>
                    <td><strong><?php echo wh($payment['learner_name'] ?? $payment['stdid']); ?></strong><small><?php echo wh($payment['stdid'] . ' · ' . ($payment['classname'] ?? $payment['classid'])); ?></small></td>
                    <td><?php echo wh($payment['reference']); ?></td>
                    <td><?php echo wh(PaymentLedgerService::MODES[$payment['mode']] ?? $payment['mode']); ?></td>
                    <td class="money">₦<?php echo number_format((int)$payment['amount']); ?></td>
                    <td><?php echo wh(PaymentLedgerService::STATUSES[(int)$payment['status']] ?? 'Unknown'); ?></td>
                    <td class="no-print">
                        <a href="index.php?route=payments&amp;term=<?php echo $termQuery; ?>&amp;id=<?php echo (int)$payment['transid']; ?>">Edit</a>
                        <?php if ((int)$payment['status'] === 1 && (string)$payment['stdid'] !== ''): ?>
                            · <a href="receipt.php?term=<?php echo $termQuery; ?>&amp;lid=<?php echo rawurlencode((string)$payment['stdid']); ?>&amp;classid=<?php echo rawurlencode((string)$payment['classid']); ?>">Term receipt</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$visible): ?><div class="workspace-empty">No payments match this term and status.</div><?php endif; ?>
</section>
<?php endif; ?>
<?php require __DIR__ . '/workflow_footer.php'; ?>
