<?php
$langs = ['en', 'ar', 'de', 'es', 'tr', 'zh'];
$data = [
    'en' => "  'search_performance' => 'Search Performance',\n",
    'ar' => "  'search_performance' => 'أداء البحث',\n",
    'de' => "  'search_performance' => 'Suchleistung',\n",
    'es' => "  'search_performance' => 'Rendimiento de Búsqueda',\n",
    'tr' => "  'search_performance' => 'Arama Performansı',\n",
    'zh' => "  'search_performance' => '搜索性能',\n"
];

foreach ($langs as $l) {
    $f = __DIR__ . '/lang/' . $l . '/admin.php';
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    if (strpos($c, "'search_performance'") === false) {
        $c = preg_replace('/];\s*$/', $data[$l] . "];\n", $c);
        file_put_contents($f, $c);
        echo "Updated $l\n";
    } else {
        echo "Already present in $l\n";
    }
}
