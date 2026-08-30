<?php
require __DIR__ . '/conf.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(409);
    exit('Please reopen Discounts and save using the current form. No record was changed.');
}
header('Location: index.php?route=discounts');
exit;
