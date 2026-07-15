<?php
$locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];
foreach ($locales as $loc) {
    $f = __DIR__.'/lang/'.$loc.'/common.php';
    if(file_exists($f)){
        $c = file_get_contents($f);
        $c = preg_replace('/(\'[^\']+\')\s*(\r?\n)\s*\'support\'/', "$1,$2  'support'", $c);
        file_put_contents($f, $c);
    }
}
