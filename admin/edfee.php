<?php
require __DIR__.'/conf.php';
if($_SERVER['REQUEST_METHOD']==='POST'){http_response_code(409);exit('Please reopen Fee setup and edit using the current protected form. No record was changed.');}
header('Location: index.php?route=fees');exit;
