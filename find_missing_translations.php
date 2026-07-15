<?php

$locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];
$paths = [__DIR__.'/resources/views', __DIR__.'/app'];
$keysFound = [];

function scanDirRecursive($dir, &$keysFound) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php'])) {
            $content = file_get_contents($file->getPathname());
            preg_match_all("/(?:__|@lang)\(['\"]([^'\"]+)['\"]\)/", $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $key) {
                    $keysFound[$key] = true;
                }
            }
        }
    }
}

foreach ($paths as $path) {
    scanDirRecursive($path, $keysFound);
}

$missing = [];
foreach (array_keys($keysFound) as $fullKey) {
    $parts = explode('.', $fullKey, 2);
    if (count($parts) < 2) continue; // Ignore top-level generic strings
    $file = $parts[0];
    $key = $parts[1];

    foreach ($locales as $locale) {
        $langFilePath = __DIR__.'/lang/'.$locale.'/'.$file.'.php';
        if (!file_exists($langFilePath)) {
            $missing[$locale][$file][] = $key;
            continue;
        }
        $translations = include $langFilePath;
        if (!is_array($translations)) $translations = [];
        
        $keyParts = explode('.', $key);
        $current = $translations;
        $found = true;
        foreach ($keyParts as $part) {
            if (is_array($current) && array_key_exists($part, $current)) {
                $current = $current[$part];
            } else {
                $found = false;
                break;
            }
        }
        
        if (!$found) {
            $missing[$locale][$file][] = $key;
        }
    }
}

file_put_contents(__DIR__.'/missing_translations.json', json_encode($missing, JSON_PRETTY_PRINT));
echo "Done.\n";
