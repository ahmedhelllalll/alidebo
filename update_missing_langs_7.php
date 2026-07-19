<?php
$langs = ['en', 'ar', 'de', 'es', 'tr', 'zh'];
$data = [
    'en' => "  'sitemaps_status' => 'Sitemaps Status',\n  'sitemap_path' => 'Sitemap Path',\n  'last_submitted' => 'Last Submitted',\n  'last_downloaded' => 'Last Downloaded',\n  'discovered_urls' => 'Discovered URLs',\n  'errors' => 'Errors',\n  'warnings' => 'Warnings',\n  'success' => 'Success',\n",
    'ar' => "  'sitemaps_status' => 'حالة خرائط الموقع',\n  'sitemap_path' => 'مسار خريطة الموقع',\n  'last_submitted' => 'آخر إرسال',\n  'last_downloaded' => 'آخر تنزيل',\n  'discovered_urls' => 'الروابط المكتشفة',\n  'errors' => 'الأخطاء',\n  'warnings' => 'التحذيرات',\n  'success' => 'نجاح',\n",
    'de' => "  'sitemaps_status' => 'Sitemaps-Status',\n  'sitemap_path' => 'Sitemap-Pfad',\n  'last_submitted' => 'Zuletzt übermittelt',\n  'last_downloaded' => 'Zuletzt heruntergeladen',\n  'discovered_urls' => 'Entdeckte URLs',\n  'errors' => 'Fehler',\n  'warnings' => 'Warnungen',\n  'success' => 'Erfolg',\n",
    'es' => "  'sitemaps_status' => 'Estado de Sitemaps',\n  'sitemap_path' => 'Ruta del Sitemap',\n  'last_submitted' => 'Último Envío',\n  'last_downloaded' => 'Última Descarga',\n  'discovered_urls' => 'URLs Descubiertas',\n  'errors' => 'Errores',\n  'warnings' => 'Advertencias',\n  'success' => 'Éxito',\n",
    'tr' => "  'sitemaps_status' => 'Site Haritaları Durumu',\n  'sitemap_path' => 'Site Haritası Yolu',\n  'last_submitted' => 'Son Gönderim',\n  'last_downloaded' => 'Son İndirme',\n  'discovered_urls' => 'Keşfedilen URL\\'ler',\n  'errors' => 'Hatalar',\n  'warnings' => 'Uyarılar',\n  'success' => 'Başarı',\n",
    'zh' => "  'sitemaps_status' => '站点地图状态',\n  'sitemap_path' => '站点地图路径',\n  'last_submitted' => '上次提交',\n  'last_downloaded' => '上次下载',\n  'discovered_urls' => '发现的网址',\n  'errors' => '错误',\n  'warnings' => '警告',\n  'success' => '成功',\n"
];

foreach ($langs as $l) {
    $f = __DIR__ . '/lang/' . $l . '/admin.php';
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    if (strpos($c, "'sitemaps_status'") === false) {
        $c = preg_replace('/];\s*$/', $data[$l] . "];\n", $c);
        file_put_contents($f, $c);
        echo "Updated $l\n";
    } else {
        echo "Already present in $l\n";
    }
}
