<?php
$langs = ['en', 'ar', 'de', 'es', 'tr', 'zh'];
$data = [
    'en' => "  'search_appearance' => 'Search Appearance',\n  'appearance_type' => 'Appearance Type',\n",
    'ar' => "  'search_appearance' => 'مظهر البحث',\n  'appearance_type' => 'نوع المظهر',\n",
    'de' => "  'search_appearance' => 'Suchdarstellung',\n  'appearance_type' => 'Darstellungstyp',\n",
    'es' => "  'search_appearance' => 'Apariencia de Búsqueda',\n  'appearance_type' => 'Tipo de Apariencia',\n",
    'tr' => "  'search_appearance' => 'Arama Görünümü',\n  'appearance_type' => 'Görünüm Türü',\n",
    'zh' => "  'search_appearance' => '搜索外观',\n  'appearance_type' => '外观类型',\n"
];

foreach ($langs as $l) {
    $f = __DIR__ . '/lang/' . $l . '/admin.php';
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    if (strpos($c, "'search_appearance'") === false) {
        $c = preg_replace('/];\s*$/', $data[$l] . "];\n", $c);
        file_put_contents($f, $c);
        echo "Updated $l\n";
    } else {
        echo "Already present in $l\n";
    }
}
