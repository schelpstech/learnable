<?php

final class CbtSecurity
{
    public static function isHttpsRequest(array $server = null)
    {
        $server = $server === null ? $_SERVER : $server;
        if (isset($server['HTTPS']) && strtolower((string)$server['HTTPS']) !== 'off' && (string)$server['HTTPS'] !== '') {
            return true;
        }
        if (isset($server['REQUEST_SCHEME']) && strtolower((string)$server['REQUEST_SCHEME']) === 'https') {
            return true;
        }
        if (isset($server['HTTP_X_FORWARDED_PROTO'])) {
            $forwarded = strtolower(trim(explode(',', (string)$server['HTTP_X_FORWARDED_PROTO'])[0]));
            if ($forwarded === 'https') return true;
        }
        if (isset($server['HTTP_X_FORWARDED_SSL']) && strtolower((string)$server['HTTP_X_FORWARDED_SSL']) === 'on') {
            return true;
        }
        return isset($server['SERVER_PORT']) && (int)$server['SERVER_PORT'] === 443;
    }

    public static function attemptCookiePath(array $server = null, $configuredUrl = null)
    {
        $server = $server === null ? $_SERVER : $server;
        $script = isset($server['SCRIPT_NAME']) ? str_replace('\\', '/', (string)$server['SCRIPT_NAME']) : '';
        $marker = '/learn/';
        $position = strpos($script, $marker);
        if ($position !== false) {
            $path = substr($script, 0, $position) . '/learn';
            return $path === '' ? '/learn' : $path;
        }

        if ($configuredUrl === null && function_exists('app_env')) {
            $configuredUrl = (string)app_env('APP_URL', '');
        }
        $configuredPath = $configuredUrl ? parse_url((string)$configuredUrl, PHP_URL_PATH) : '';
        $configuredPath = is_string($configuredPath) ? '/' . trim($configuredPath, '/') : '';
        return ($configuredPath === '' ? '' : rtrim($configuredPath, '/')) . '/learn';
    }

    public static function csrfToken($scope)
    {
        $key = $scope === 'admin' ? 'admin_csrf' : 'portal_csrf';
        if (empty($_SESSION[$key]) || !is_string($_SESSION[$key])) {
            $_SESSION[$key] = bin2hex(random_bytes(32));
        }
        return $_SESSION[$key];
    }

    public static function requireCsrf($submitted, $scope)
    {
        $expected = self::csrfToken($scope);
        if (!is_string($submitted) || !hash_equals($expected, $submitted)) {
            throw new RuntimeException('Your session token expired. Refresh the page and try again.');
        }
    }

    public static function requirePortalRole(array $roles)
    {
        if (!isset($_SESSION['active'], $_SESSION['user_type'])
            || !in_array($_SESSION['user_type'], $roles, true)) {
            throw new RuntimeException('This action is not available for your account.');
        }
        return (string) $_SESSION['active'];
    }

    public static function requestIpHash()
    {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'cli';
        return self::privacyHash($ip);
    }

    public static function fingerprintHash($fingerprint)
    {
        $fingerprint = is_string($fingerprint) ? trim($fingerprint) : '';
        return $fingerprint === '' ? null : self::privacyHash($fingerprint);
    }

    public static function privacyHash($value)
    {
        $salt = function_exists('app_env')
            ? (string) app_env('CBT_INTEGRITY_SALT', 'learnable-local-integrity-salt')
            : 'learnable-local-integrity-salt';
        return hash('sha256', $salt . '|' . (string) $value);
    }

    public static function cleanText($value, $maxLength, $allowEmpty)
    {
        $value = is_string($value) || is_numeric($value) ? trim((string) $value) : '';
        if (!$allowEmpty && $value === '') {
            throw new InvalidArgumentException('Please complete all required fields.');
        }
        if (mb_strlen($value) > $maxLength) {
            throw new InvalidArgumentException('One of the submitted fields is too long.');
        }
        return $value;
    }

    public static function positiveInt($value, $label, $minimum, $maximum)
    {
        $number = filter_var($value, FILTER_VALIDATE_INT);
        if ($number === false || $number < $minimum || $number > $maximum) {
            throw new InvalidArgumentException($label . ' is invalid.');
        }
        return (int) $number;
    }

    public static function decimal($value, $label, $minimum, $maximum)
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException($label . ' is invalid.');
        }
        $number = round((float) $value, 2);
        if ($number < $minimum || $number > $maximum) {
            throw new InvalidArgumentException($label . ' is outside the allowed range.');
        }
        return $number;
    }

    public static function safeHtml($value, $maxLength)
    {
        $value = self::cleanText($value, $maxLength, false);
        $value = strip_tags($value, '<p><br><strong><b><em><i><u><ol><ul><li><table><thead><tbody><tr><th><td><sub><sup><span>');
        $value = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $value);
        $value = preg_replace('/(?:javascript|data)\s*:/i', '', $value);
        return $value;
    }
}
