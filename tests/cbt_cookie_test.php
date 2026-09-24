<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../classes/autoload.php';

function cookie_check($condition, $message) {
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
    echo 'PASS: ' . $message . "\n";
}

cookie_check(
    CbtSecurity::attemptCookiePath(array('SCRIPT_NAME'=>'/learnable/learn/app/cbt_action.php'), 'https://wrong.example/other') === '/learnable/learn',
    'attempt cookie follows the actual subdirectory deployment rather than a stale APP_URL path'
);
cookie_check(
    CbtSecurity::attemptCookiePath(array('SCRIPT_NAME'=>'/learn/app/cbt_action.php'), 'https://example.test/learnable') === '/learn',
    'attempt cookie covers a root-domain learn directory'
);
cookie_check(
    CbtSecurity::attemptCookiePath(array('SCRIPT_NAME'=>'/index.php'), 'https://example.test/school') === '/school/learn',
    'configured application path remains a safe fallback'
);
cookie_check(CbtSecurity::isHttpsRequest(array('HTTPS'=>'on')), 'direct HTTPS is detected');
cookie_check(CbtSecurity::isHttpsRequest(array('HTTP_X_FORWARDED_PROTO'=>'https,http')), 'reverse-proxy HTTPS is detected');
cookie_check(CbtSecurity::isHttpsRequest(array('SERVER_PORT'=>443)), 'HTTPS server port is detected');
cookie_check(!CbtSecurity::isHttpsRequest(array('HTTPS'=>'off','SERVER_PORT'=>80)), 'plain HTTP is not marked secure');

echo "CBT production-cookie checks passed.\n";
