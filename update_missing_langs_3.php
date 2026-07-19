<?php
$langs = ['en', 'ar', 'de', 'es', 'tr', 'zh'];
$data = [
    'en' => "  'top_performing_pages' => 'Top Performing Pages',\n  'page_url' => 'Page URL',\n",
    'ar' => "  'top_performing_pages' => 'الصفحات الأفضل أداءً',\n  'page_url' => 'رابط الصفحة',\n",
    'de' => "  'top_performing_pages' => 'Leistungsstärkste Seiten',\n  'page_url' => 'Seiten-URL',\n",
    'es' => "  'top_performing_pages' => 'Páginas con Mejor Rendimiento',\n  'page_url' => 'URL de la Página',\n",
    'tr' => "  'top_performing_pages' => 'En İyi Performans Gösteren Sayfalar',\n  'page_url' => 'Sayfa URL\'si',\n",
    'zh' => "  'top_performing_pages' => '表现最好的页面',\n  'page_url' => '页面 URL',\n"
];

foreach ($langs as $l) {
    $f = __DIR__ . '/lang/' . $l . '/admin.php';
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    if (strpos($c, "'top_performing_pages'") === false) {
        $c = preg_replace('/];\s*$/', $data[$l] . "];\n", $c);
        file_put_contents($f, $c);
        echo "Updated $l\n";
    } else {
        echo "Already present in $l\n";
    }
}
