<?php
$langs = ['en', 'ar', 'de', 'es', 'tr', 'zh'];
$data = [
    'en' => "  'google_search_insights' => 'Google Search Insights',\n",
    'ar' => "  'google_search_insights' => 'إحصاءات بحث Google',\n",
    'de' => "  'google_search_insights' => 'Google Search Insights',\n",
    'es' => "  'google_search_insights' => 'Google Search Insights',\n",
    'tr' => "  'google_search_insights' => 'Google Arama Analizleri',\n",
    'zh' => "  'google_search_insights' => 'Google 搜索洞察',\n"
];

foreach ($langs as $l) {
    $f = __DIR__ . '/lang/' . $l . '/admin.php';
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    if (strpos($c, "'google_search_insights'") === false) {
        $c = preg_replace('/];\s*$/', $data[$l] . "];\n", $c);
        file_put_contents($f, $c);
        echo "Updated $l\n";
    } else {
        echo "Already present in $l\n";
    }
}
