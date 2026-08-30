<?php
require __DIR__ . '/workflow_bootstrap.php';
$service = new DiscountService($workflowDb);
$term = is_string($_GET['term'] ?? null) ? $_GET['term'] : $service->activeTerm();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', 'admin');
        $remove = ($_POST['action'] ?? '') === 'remove';
        $service->change($_POST['id'] ?? 0,$remove ? 0 : ($_POST['discount'] ?? ''),$_POST['expected'] ?? 0,$_SESSION['unamed'],$remove,($_POST['confirm'] ?? '') === 'yes');
        workflow_redirect('discounts',$remove ? 'Discount removed. The fee and payment history are unchanged.' : 'Discount saved. The payable balance has been updated.');
    } catch (Throwable $e) { $workflowError = workflow_error($e); }
}
$fees=$service->fees($term);$selected=null;
foreach($fees as $f) if((string)$f['assid']===(string)($_GET['id'] ?? $_POST['id'] ?? '')) $selected=$f;
$discounts=array_filter($fees,function($f){return (int)$f['discount']>0;});
$workflowTitle='Discounts';$workflowIntro='Manage fee concessions with care. Removing a discount restores the fee balance; it never deletes the fee.';
require __DIR__ . '/workflow_header.php';
?>
<div class="workspace-grid"><section class="workspace-card"><h2><?php echo $selected?'Edit discount':'Award a discount'; ?></h2>
<p class="workspace-muted"><?php echo wh($term); ?> · Amounts are recorded in whole naira.</p>
<label>Find an assigned fee<input type="search" id="discount-fee-search" placeholder="Type a learner, class or fee name"></label>
<form method="post"><input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>"><input type="hidden" name="action" value="save">
<div class="workspace-fields"><label class="workspace-wide">Learner and assigned fee<select name="id" required id="discount-fee"><option value="">Choose an assigned fee</option>
<?php foreach($selected?array($selected):$fees as $f): ?><option value="<?php echo (int)$f['assid']; ?>" data-current="<?php echo (int)$f['discount']; ?>" data-maximum="<?php echo (int)$f['amount']; ?>" <?php echo $selected?'selected':''; ?>><?php echo wh($f['learner_name'].' · '.$f['class_name'].' · '.$f['fee_name'].' · ₦'.number_format($f['amount'])); ?></option><?php endforeach; ?></select></label>
<input type="hidden" name="expected" id="discount-expected" value="<?php echo (int)($selected['discount'] ?? 0); ?>">
<label class="workspace-wide">Discount amount (₦)<input type="number" name="discount" id="discount-amount" min="0" step="1" required value="<?php echo wh($_POST['discount'] ?? $selected['discount'] ?? ''); ?>" <?php if($selected): ?>max="<?php echo (int)$selected['amount']; ?>"<?php endif; ?>></label></div>
<div class="workspace-actions"><button class="workspace-button">Save discount</button><?php if($selected): ?><a class="workspace-button secondary" href="index.php?route=discounts">Cancel</a><?php endif; ?></div></form>
<?php if($selected && (int)$selected['discount']>0): ?><hr><form method="post" data-confirm="Remove this discount? The fee remains and the payable balance will increase.">
<input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>"><input type="hidden" name="id" value="<?php echo (int)$selected['assid']; ?>"><input type="hidden" name="expected" value="<?php echo (int)$selected['discount']; ?>"><input type="hidden" name="action" value="remove">
<label class="workspace-check"><input type="checkbox" name="confirm" value="yes" required> I confirm removal of this ₦<?php echo number_format($selected['discount']); ?> discount.</label><button class="workspace-button danger">Delete discount</button></form><?php endif; ?></section>
<section class="workspace-card"><h2>Awarded discounts</h2><p class="workspace-muted"><?php echo count($discounts); ?> discounts · ₦<?php echo number_format(array_sum(array_column($discounts,'discount'))); ?> total</p>
<form method="get" class="workspace-toolbar"><input type="hidden" name="route" value="discounts"><label>Academic term<select name="term"><?php foreach($workflowDb->query('SELECT term FROM lpterm ORDER BY tid DESC') as $t): ?><option <?php echo $t['term']===$term?'selected':''; ?>><?php echo wh($t['term']); ?></option><?php endforeach; ?></select></label><button class="workspace-button secondary">View term</button></form>
<label>Find a learner or fee<input type="search" data-table-search="#discount-table" placeholder="Search discounts"></label>
<div class="workspace-table-wrap"><table class="workspace-table" id="discount-table"><thead><tr><th>Learner / class</th><th>Fee</th><th>Discount</th><th>Payable</th><th>Actions</th></tr></thead><tbody>
<?php foreach($discounts as $f): ?><tr><td><strong><?php echo wh($f['learner_name']); ?></strong><small><?php echo wh($f['class_name']); ?></small></td><td><?php echo wh($f['fee_name']); ?><small>₦<?php echo number_format($f['amount']); ?></small></td><td class="money">₦<?php echo number_format($f['discount']); ?></td><td class="money">₦<?php echo number_format($f['amount']-$f['discount']); ?></td><td><a href="index.php?route=discounts&amp;term=<?php echo rawurlencode($term); ?>&amp;id=<?php echo (int)$f['assid']; ?>">Edit / delete</a></td></tr><?php endforeach; ?>
</tbody></table></div><?php if(!$discounts): ?><div class="workspace-empty">No discounts for this term. Choose a learner’s assigned fee to add one.</div><?php endif; ?></section></div>
<script src="../assets/js/discounts.js?v=2"></script>
<?php require __DIR__ . '/workflow_footer.php'; ?>
