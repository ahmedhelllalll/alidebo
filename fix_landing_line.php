<?php
$f = 'lang/tr/landing.php';
$lines = file($f);
foreach($lines as $i => $line) {
    if (strpos($line, 'hero_unified_headline_2') !== false) {
        $lines[$i] = "    'hero_unified_headline_2' => 've İşinizi Büyütmek İçin En İyi Platform',\n";
    }
}
file_put_contents($f, implode("", $lines));
echo "Fixed landing.php line\n";
