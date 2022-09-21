<?php
include '../include/header.php';
include '../include/nav.php';
include '../include/navigator.php';
if ($_SESSION['pageid'] == 'payment' && $_SESSION['instance'] == 'bill') {
    include '../include/pages/bill.php';
} elseif ($_SESSION['pageid'] == 'payment' && $_SESSION['instance'] == 'transaction') {
    include '../include/pages/transaction.php';
}elseif ($_SESSION['pageid'] == 'payment' && $_SESSION['instance'] == 'payment') {
    include '../include/pages/paynow.php';
}
?>

</section>
<?php
include '../include/footer.php';
?>