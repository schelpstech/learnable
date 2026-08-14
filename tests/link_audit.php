<?php

$root = realpath(__DIR__ . '/..');
$scanRoots = array(
    $root,
    $root . '/admin',
    $root . '/bursar',
    $root . '/learn/view',
    $root . '/learn/app',
);
$excludedDirectories = array('vendor', 'node_modules', '.git', 'css', 'js', 'asset', 'fonts', 'images', 'img');
$files = array();

foreach ($scanRoots as $scanRoot) {
    if (!is_dir($scanRoot)) {
        continue;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($scanRoot, FilesystemIterator::SKIP_DOTS),
            function ($current) use ($excludedDirectories, $scanRoot) {
                if ($current->isDir() && $current->getPathname() !== $scanRoot) {
                    return !in_array($current->getFilename(), $excludedDirectories, true);
                }
                return true;
            }
        )
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), array('php', 'html'), true)) {
            $files[$file->getRealPath()] = true;
        }
    }
}

$missing = array();
$checked = 0;
foreach (array_keys($files) as $file) {
    $contents = file_get_contents($file);
    if ($contents === false) {
        continue;
    }
    $candidates = array();
    preg_match_all('/\b(?:href|src|action)\s*=\s*(["\'])(.*?)\1/is', $contents, $attributeMatches, PREG_SET_ORDER);
    foreach ($attributeMatches as $match) {
        $candidates[] = array($match[0], $match[2]);
    }
    preg_match_all('/header\s*\(\s*(["\'])Location:\s*([^"\']+)\1/is', $contents, $headerMatches, PREG_SET_ORDER);
    foreach ($headerMatches as $match) {
        $candidates[] = array($match[0], $match[2]);
    }
    preg_match_all('/(?:window\.)?location(?:\.href)?\s*=\s*(["\'])([^"\']+)\1/is', $contents, $locationMatches, PREG_SET_ORDER);
    foreach ($locationMatches as $match) {
        $candidates[] = array($match[0], $match[2]);
    }

    foreach ($candidates as $candidate) {
        $value = html_entity_decode(trim($candidate[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($value === ''
            || strpos($value, '<?') !== false
            || strpos($value, '{{') !== false
            || strpos($value, '$') !== false
            || strpos($value, "' .") !== false
            || strpos($value, '<') !== false
            || strpos($value, '>') !== false
            || preg_match('#^(?:[a-z][a-z0-9+.-]*:|//|\#)#i', $value)) {
            continue;
        }

        $path = parse_url($value, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            continue;
        }
        $path = rawurldecode($path);
        if (strpos($path, '/learnable/') === 0) {
            $target = $root . substr($path, strlen('/learnable'));
        } elseif ($path[0] === '/') {
            continue;
        } else {
            $includeRoot = $root . DIRECTORY_SEPARATOR . 'learn' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'include';
            $baseDirectory = strpos($file, $includeRoot . DIRECTORY_SEPARATOR) === 0 ? $includeRoot : dirname($file);
            $target = $baseDirectory . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        }

        $checked++;
        if (!file_exists($target)) {
            $line = substr_count(substr($contents, 0, strpos($contents, $candidate[0])), "\n") + 1;
            $missing[] = str_replace($root . DIRECTORY_SEPARATOR, '', $file) . ':' . $line . ' -> ' . $value;
        }
    }
}

if ($missing) {
    echo "Broken local links (" . count($missing) . "):\n" . implode("\n", array_unique($missing)) . "\n";
    exit(1);
}

echo 'Static link audit passed: ' . $checked . " local targets checked.\n";
