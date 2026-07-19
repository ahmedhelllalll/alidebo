<?php
$langs = ['en', 'ar', 'de', 'es', 'tr', 'zh'];
$data = [
    'en' => "  'indexed_urls' => 'Indexed URLs',\n",
    'ar' => "  'indexed_urls' => 'الروابط المفهرسة',\n",
    'de' => "  'indexed_urls' => 'Indexierte URLs',\n",
    'es' => "  'indexed_urls' => 'URLs Indexadas',\n",
    'tr' => "  'indexed_urls' => 'İndekslenen URL\\'ler',\n",
    'zh' => "  'indexed_urls' => '已索引的网址',\n"
];

foreach ($langs as $l) {
    $f = __DIR__ . '/lang/' . $l . '/admin.php';
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    if (strpos($c, "'indexed_urls'") === false) {
        $c = preg_replace('/];\s*$/', $data[$l] . "];\n", $c);
        file_put_contents($f, $c);
        echo "Updated $l\n";
    } else {
        echo "Already present in $l\n";
    }
}
