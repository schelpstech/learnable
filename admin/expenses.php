<?php
require __DIR__ . '/workflow_bootstrap.php';
$service = new ExpenseService($workflowDb);
$from = is_string($_GET['from'] ?? null) ? $_GET['from'] : date('Y-m-01');
$to = is_string($_GET['to'] ?? null) ? $_GET['to'] : date('Y-m-t');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', 'admin');
        if (!in_array($_POST['action'] ?? 'save',array('save','void'),true)) throw new InvalidArgumentException('Unknown expense action.');
        if (($_POST['action'] ?? '') === 'void') {
            $service->void($_POST['id'] ?? 0,$_POST['version'] ?? 0,$_POST['reason'] ?? '',$_SESSION['unamed'],($_POST['confirm'] ?? '') === 'yes');
            workflow_redirect('expenses','Expense voided. Its history is retained and it is excluded from totals.');
        }
        $id = $service->save($_POST,$_SESSION['unamed']);
        workflow_redirect('expenses','Expense saved.', $id);
    } catch (Throwable $e) { $workflowError = workflow_error($e); }
}
$edit = null; $records = array();
try {
    if (!empty($_GET['id'])) $edit = $service->get($_GET['id']);
    if ($from > $to) throw new InvalidArgumentException('The start date must be before the end date.');
    $records = $service->records($from,$to);
} catch (Throwable $e) { $workflowError = workflow_error($e); }
$form = $workflowError && $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : ($edit ?: array());
$live = array_filter($records,function($r){return $r['status']==='recorded';});
$workflowTitle = 'Expenses'; $workflowIntro = 'A clear record of what the school spends. Keep the payee, purpose and payment reference together.';
require __DIR__ . '/workflow_header.php';
?>
<div class="workspace-stats"><div><span>Recorded in selected period</span><strong>₦<?php echo number_format(array_sum(array_column($live,'amount')),2); ?></strong></div><div><span>Expense entries</span><strong><?php echo count($live); ?></strong></div><div><span>Voided entries · excluded from totals</span><strong><?php echo count($records)-count($live); ?></strong></div></div>
<div class="workspace-grid"><section class="workspace-card"><h2><?php echo $edit ? 'Expense #'.(int)$edit['id'] : 'Record an expense'; ?></h2>
<?php if (!$edit || $edit['status'] === 'recorded'): ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>"><input type="hidden" name="id" value="<?php echo (int)($edit['id'] ?? 0); ?>"><input type="hidden" name="version" value="<?php echo (int)($form['version'] ?? 0); ?>"><input type="hidden" name="request_key" value="<?php echo wh($form['request_key'] ?? bin2hex(random_bytes(16))); ?>">
<div class="workspace-fields"><label>Date<input type="date" name="expense_date" required value="<?php echo wh($form['expense_date'] ?? date('Y-m-d')); ?>"></label><label>Amount (₦)<input type="number" name="amount" required min="0.01" max="9999999999.99" step="0.01" value="<?php echo wh($form['amount'] ?? ''); ?>"></label>
<label>Category<input name="category" list="expense-categories" maxlength="80" required value="<?php echo wh($form['category'] ?? ''); ?>"></label><datalist id="expense-categories"><option>Teaching materials</option><option>Utilities</option><option>Maintenance</option><option>Transport</option><option>Staff costs</option><option>Other</option></datalist>
<label>Payment method<select name="method"><?php foreach(array('Cash','Bank transfer','Card','Other') as $method): ?><option <?php echo ($form['method'] ?? '')===$method?'selected':''; ?>><?php echo wh($method); ?></option><?php endforeach; ?></select></label>
<label class="workspace-wide">Paid to<input name="payee" maxlength="160" required value="<?php echo wh($form['payee'] ?? ''); ?>"></label><label class="workspace-wide">Purpose<textarea name="description" rows="3" maxlength="500" required><?php echo wh($form['description'] ?? ''); ?></textarea></label><label class="workspace-wide">Receipt / payment reference (optional)<input name="reference" maxlength="100" value="<?php echo wh($form['reference'] ?? ''); ?>"></label></div>
<div class="workspace-actions"><button class="workspace-button">Save expense</button><?php if ($edit): ?><a class="workspace-button secondary" href="index.php?route=expenses">New expense</a><?php endif; ?></div></form>
<?php if ($edit): ?><hr><details><summary>Void this expense</summary><p class="workspace-muted">Use this for an entry recorded in error. It will remain in the audit history.</p><form method="post" data-confirm="Void this expense and exclude it from totals?"><input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>"><input type="hidden" name="id" value="<?php echo (int)$edit['id']; ?>"><input type="hidden" name="version" value="<?php echo (int)$edit['version']; ?>"><input type="hidden" name="action" value="void"><label>Reason<textarea name="reason" required maxlength="500"></textarea></label><label class="workspace-check"><input type="checkbox" name="confirm" value="yes" required> I confirm this expense should be voided.</label><button class="workspace-button danger">Void expense</button></form></details><?php endif; ?>
<?php else: ?><p class="workspace-error">Voided: <?php echo wh($edit['void_reason']); ?></p><a class="workspace-button secondary" href="index.php?route=expenses">Record another expense</a><?php endif; ?></section>
<section class="workspace-card"><h2>Expense register</h2><form method="get" class="workspace-toolbar"><input type="hidden" name="route" value="expenses"><label>From<input type="date" name="from" value="<?php echo wh($from); ?>" required></label><label>To<input type="date" name="to" value="<?php echo wh($to); ?>" required></label><button class="workspace-button secondary">Apply dates</button><button class="workspace-button secondary" type="button" data-print>Print</button></form><label class="no-print">Search register<input type="search" data-table-search="#expense-table" placeholder="Payee, category or reference"></label>
<div class="workspace-table-wrap"><table class="workspace-table" id="expense-table"><thead><tr><th>Date / reference</th><th>Paid to / purpose</th><th>Category</th><th>Amount</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($records as $r): ?><tr><td><?php echo wh($r['expense_date']); ?><small><?php echo wh($r['reference']); ?></small></td><td><strong><?php echo wh($r['payee']); ?></strong><small><?php echo wh($r['description']); ?></small></td><td><?php echo wh($r['category']); ?><small><?php echo wh($r['method']); ?></small></td><td class="money">₦<?php echo number_format($r['amount'],2); ?></td><td><span class="workspace-badge <?php echo $r['status']==='void'?'muted':''; ?>"><?php echo wh($r['status']); ?></span></td><td><a href="index.php?route=expenses&amp;id=<?php echo (int)$r['id']; ?>">View / edit</a></td></tr><?php endforeach; ?></tbody></table></div><?php if (!$records): ?><div class="workspace-empty">No expenses in this date range.</div><?php endif; ?></section></div>
<?php require __DIR__ . '/workflow_footer.php'; ?>
