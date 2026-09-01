<?php
require __DIR__.'/conf.php';
if($_SERVER['REQUEST_METHOD']==='POST'){http_response_code(409);exit('Please reopen Assign fees and deactivate from the protected register. No record was changed.');}
header('Location: index.php?route=fee-assignments');exit;
