<?php
$langs = ['en', 'ar', 'de', 'es', 'tr', 'zh'];
$data = [
    'en' => "  'geographical_distribution' => 'Geographical Distribution',\n",
    'ar' => "  'geographical_distribution' => 'التوزيع الجغرافي',\n",
    'de' => "  'geographical_distribution' => 'Geografische Verteilung',\n",
    'es' => "  'geographical_distribution' => 'Distribución Geográfica',\n",
    'tr' => "  'geographical_distribution' => 'Coğrafi Dağılım',\n",
    'zh' => "  'geographical_distribution' => '地理分布',\n"
];

foreach ($langs as $l) {
    $f = __DIR__ . '/lang/' . $l . '/admin.php';
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    if (strpos($c, "'geographical_distribution'") === false) {
        $c = preg_replace('/];\s*$/', $data[$l] . "];\n", $c);
        file_put_contents($f, $c);
        echo "Updated $l\n";
    } else {
        echo "Already present in $l\n";
    }
}
