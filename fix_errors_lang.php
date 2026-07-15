<?php
$locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];

$keysToRemove = [
    '401.title', '401.desc',
    '403.title', '403.desc',
    '404.title', '404.desc',
    '405.title', '405.desc',
    '419.title', '419.desc',
    '429.title', '429.desc',
    '500.title', '500.desc',
    '503.title', '503.desc'
];

foreach ($locales as $loc) {
    $path = __DIR__ . '/lang/' . $loc . '/errors.php';
    if (file_exists($path)) {
        $content = file_get_contents($path);
        foreach ($keysToRemove as $key) {
            $content = preg_replace("/\s*'{$key}'\s*=>\s*'[^']+',/", "", $content);
        }
        file_put_contents($path, $content);
        echo "Fixed $loc/errors.php\n";
    }
}
