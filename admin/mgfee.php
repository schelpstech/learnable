<?php
require __DIR__ . '/workflow_bootstrap.php';

$service = new FeeService($workflowDb);
$status = is_string($_GET['status'] ?? null) ? $_GET['status'] : 'active';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        CbtSecurity::requireCsrf($_POST['csrf_token'] ?? '', 'admin');
        $action = $_POST['action'] ?? 'save';
        if (!in_array($action, array('save', 'archive'), true)) {
            throw new InvalidArgumentException('Unknown fee action.');
        }
        if ($action === 'archive') {
            $affected = $service->archive(
                $_POST['id'] ?? 0,
                $_POST['version'] ?? '',
                $_SESSION['unamed'],
                ($_POST['confirm'] ?? '') === 'yes'
            );
            workflow_redirect(
                'fees',
                'Fee archived. ' . number_format($affected) . ' active assigned charge' . ($affected === 1 ? ' was' : 's were') . ' deactivated; payment records were not deleted.'
            );
        }
        $id = $service->save($_POST, $_SESSION['unamed']);
        workflow_redirect('fees', !empty($_POST['id']) ? 'Fee details saved.' : 'Fee created in the active-session catalogue and ready to assign.', $id);
    } catch (Throwable $e) {
        $workflowError = workflow_error($e);
    }
}

$edit = null;
$definitions = array();
$classes = array();
$session = array('sessionid' => 0, 'session' => 'Not configured');
try {
    $session = $service->activeSession();
    $classes = $service->classes();
    if (!empty($_GET['id'])) {
        $edit = $service->definition($_GET['id']);
        if ((int)$edit['session'] !== (int)$session['sessionid']) {
            throw new InvalidArgumentException('Only fees from the active academic session can be managed here.');
        }
    }
    $definitions = $service->definitions($session['sessionid'], $status);
} catch (Throwable $e) {
    $workflowError = workflow_error($e);
}

$form = $workflowError && $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : ($edit ?: array());
$activeDefinitions = array_filter($definitions, function ($row) { return (int)$row['status'] === 1; });
$activeAssignments = array_sum(array_map(function ($row) { return (int)$row['active_assignments']; }, $activeDefinitions));
$catalogueValue = array_sum(array_map(function ($row) { return (int)$row['amount']; }, $activeDefinitions));
$mode = $edit ? (ctype_digit((string)$edit['classid']) ? 'class' : 'school') : ($form['association_mode'] ?? 'class');

$workflowTitle = 'Fee setup';
$workflowIntro = 'Maintain one reusable fee catalogue for the active academic session, then assign charges term by term.';
$workflowScripts = array('../assets/js/fees.js?v=2');
require __DIR__ . '/workflow_header.php';
?>
<nav class="workspace-subnav no-print" aria-label="Fee management">
    <a class="active" href="index.php?route=fees">Fee setup</a>
    <a href="index.php?route=fee-assignments">Assign fees</a>
    <a href="index.php?route=discounts">Discounts</a>
    <a href="index.php?route=payments">Payments</a>
</nav>

<div class="workspace-stats">
    <div><span>Active fees · <?php echo wh($session['session']); ?></span><strong><?php echo count($activeDefinitions); ?></strong></div>
    <div><span>Active learner assignments</span><strong><?php echo number_format($activeAssignments); ?></strong></div>
    <div><span>Catalogue value · not revenue</span><strong>₦<?php echo number_format($catalogueValue); ?></strong></div>
</div>

<div class="workspace-grid fee-workspace">
    <div>
        <section class="workspace-card">
            <p class="workspace-eyebrow"><?php echo $edit ? 'Editing fee #' . (int)$edit['feeid'] : 'Active-session catalogue'; ?></p>
            <h2><?php echo $edit ? wh($edit['feename']) : 'Create a reusable fee'; ?></h2>
            <p class="workspace-muted">
                <?php echo $edit
                    ? 'Session and fee group stay fixed so existing term assignments keep a reliable reference.'
                    : 'Create this fee once for ' . wh($session['session']) . '. It can then be assigned in each term of the session.'; ?>
            </p>

            <?php if (!$edit || (int)$edit['status'] === 1): ?>
            <form method="post" data-fee-create>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>">
                <input type="hidden" name="id" value="<?php echo (int)($edit['feeid'] ?? 0); ?>">
                <input type="hidden" name="version" value="<?php echo wh($edit['version'] ?? ''); ?>">
                <div class="workspace-fields">
                    <label>Academic session<input value="<?php echo wh($session['session']); ?>" disabled></label>
                    <?php if ($edit): ?>
                        <label>Available for<input value="<?php echo wh($edit['classname'] ?: $edit['classid']); ?>" disabled></label>
                    <?php else: ?>
                        <label>Fee availability
                            <select name="association_mode" data-fee-mode required>
                                <option value="class" <?php echo $mode === 'class' ? 'selected' : ''; ?>>One class</option>
                                <option value="school" <?php echo $mode === 'school' ? 'selected' : ''; ?>>School-wide / reusable group</option>
                            </select>
                        </label>
                        <label data-fee-class>Class
                            <select name="class_id">
                                <option value="">Choose class</option>
                                <?php foreach ($classes as $row): ?>
                                    <option value="<?php echo (int)$row['classid']; ?>" <?php echo (string)($form['class_id'] ?? '') === (string)$row['classid'] ? 'selected' : ''; ?>><?php echo wh($row['classname']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small>Class-specific fees can only be assigned to this class.</small>
                        </label>
                        <label data-fee-group>Fee group
                            <input name="fee_group" maxlength="64" value="<?php echo wh($form['fee_group'] ?? 'SCHOOL-WIDE'); ?>" placeholder="e.g. SCHOOL-WIDE or TRANSPORTATION">
                            <small>A descriptive group keeps reusable fees easy to find.</small>
                        </label>
                    <?php endif; ?>
                    <label class="workspace-wide">Fee name<input name="feename" maxlength="254" required value="<?php echo wh($form['feename'] ?? ''); ?>" placeholder="e.g. Tuition fee"></label>
                    <label>Amount (₦)<input type="number" name="amount" min="1" max="1000000000" step="1" required value="<?php echo wh($form['amount'] ?? ''); ?>"></label>
                    <?php if ($edit): ?>
                        <label class="workspace-check workspace-wide">
                            <input type="checkbox" name="propagate" value="yes" <?php echo ($form['propagate'] ?? '') === 'yes' ? 'checked' : ''; ?>>
                            Update active term assignments that still use this fee
                            <small>Leave this unticked to change future assignments only.</small>
                        </label>
                    <?php endif; ?>
                </div>
                <div class="workspace-actions">
                    <button class="workspace-button"><?php echo $edit ? 'Save fee changes' : 'Create reusable fee'; ?></button>
                    <?php if ($edit): ?><a class="workspace-button secondary" href="index.php?route=fees">Create another</a><?php endif; ?>
                </div>
            </form>
            <?php else: ?>
                <div class="workspace-notice">This fee is archived and cannot be edited. Its historical term assignments remain available in the assigned-fee register.</div>
            <?php endif; ?>
        </section>

        <?php if ($edit && (int)$edit['status'] === 1): ?>
        <section class="workspace-card fee-danger-zone">
            <h2>Archive fee</h2>
            <p class="workspace-muted">This also deactivates <?php echo number_format((int)$edit['active_assignments']); ?> active assigned charge<?php echo (int)$edit['active_assignments'] === 1 ? '' : 's'; ?> across the session. It does not delete payments or history.</p>
            <form method="post" data-confirm="Archive this fee and deactivate its active assigned charges across the session?">
                <input type="hidden" name="action" value="archive">
                <input type="hidden" name="csrf_token" value="<?php echo wh($workflowCsrf); ?>">
                <input type="hidden" name="id" value="<?php echo (int)$edit['feeid']; ?>">
                <input type="hidden" name="version" value="<?php echo wh($edit['version']); ?>">
                <label class="workspace-check"><input type="checkbox" name="confirm" value="yes" required> I have reviewed the affected term assignments.</label>
                <button class="workspace-button danger">Archive fee</button>
            </form>
        </section>
        <?php endif; ?>
    </div>

    <section class="workspace-card">
        <div class="fee-section-heading">
            <div><h2>Session fee catalogue</h2><p class="workspace-muted"><?php echo wh($session['session']); ?> · reused across its terms</p></div>
            <a class="workspace-button" href="index.php?route=fee-assignments">Assign for a term</a>
        </div>
        <form method="get" class="workspace-toolbar">
            <input type="hidden" name="route" value="fees">
            <label>Status
                <select name="status">
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="archived" <?php echo $status === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All</option>
                </select>
            </label>
            <button class="workspace-button secondary">Apply</button>
        </form>
        <label class="no-print">Find a fee<input type="search" data-table-search="#fee-table" placeholder="Name, class or group"></label>
        <div class="workspace-table-wrap">
            <table class="workspace-table" id="fee-table">
                <thead><tr><th>Fee</th><th>Available for</th><th>Amount</th><th>Term assignments</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($definitions as $row): ?>
                    <tr>
                        <td><strong><?php echo wh($row['feename']); ?></strong><small><?php echo wh($row['session_name'] ?: $session['session']); ?></small></td>
                        <td><?php echo wh($row['classname'] ?: $row['classid']); ?></td>
                        <td class="money">₦<?php echo number_format((int)$row['amount']); ?></td>
                        <td><?php echo number_format((int)$row['active_assignments']); ?> active</td>
                        <td><span class="workspace-badge <?php echo (int)$row['status'] === 1 ? '' : 'muted'; ?>"><?php echo (int)$row['status'] === 1 ? 'Active' : 'Archived'; ?></span></td>
                        <td><a href="index.php?route=fees&amp;id=<?php echo (int)$row['feeid']; ?>">View / edit</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (!$definitions): ?><div class="workspace-empty"><h3>No fees found</h3><p>Create the first reusable fee for this session, or change the register filter.</p></div><?php endif; ?>
    </section>
</div>
<?php require __DIR__ . '/workflow_footer.php'; ?>
