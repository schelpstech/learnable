<?php

if (!function_exists('load_environment')) {
    function load_environment($path)
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }
        $loaded = true;

        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $values = parse_ini_file($path, false, INI_SCANNER_RAW);
        if (!is_array($values)) {
            throw new RuntimeException('Unable to read the application environment file.');
        }

        foreach ($values as $key => $value) {
            if (!array_key_exists($key, $_ENV) && getenv($key) === false) {
                $_ENV[$key] = $value;
            }
        }
    }
}

if (!function_exists('app_env')) {
    function app_env($key, $default = null)
    {
        load_environment(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

        $value = array_key_exists($key, $_ENV) ? $_ENV[$key] : getenv($key);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        $normalized = strtolower((string) $value);
        if ($normalized === 'true') {
            return true;
        }
        if ($normalized === 'false') {
            return false;
        }
        if ($normalized === 'null') {
            return null;
        }

        return $value;
    }
}

