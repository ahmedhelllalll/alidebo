<?php
$langs = ['en', 'ar', 'de', 'es', 'tr', 'zh'];
$data = [
    'en' => "  // Search Insights Missing Keys\n  'back_to_seo' => 'Back to SEO',\n  'last_7_days' => 'Last 7 Days',\n  'last_30_days' => 'Last 30 Days',\n  'last_3_months' => 'Last 3 Months',\n  'total_clicks' => 'Total Clicks',\n  'total_impressions' => 'Total Impressions',\n  'top_performing_keywords' => 'Top Performing Keywords',\n  'keyword_query' => 'Keyword / Query',\n  'position' => 'Position',\n  'no_search_data' => 'No search data available for this period.',\n",
    'ar' => "  // Search Insights Missing Keys\n  'back_to_seo' => 'العودة إلى السيو',\n  'last_7_days' => 'آخر 7 أيام',\n  'last_30_days' => 'آخر 30 يوماً',\n  'last_3_months' => 'آخر 3 أشهر',\n  'total_clicks' => 'إجمالي النقرات',\n  'total_impressions' => 'إجمالي مرات الظهور',\n  'top_performing_keywords' => 'الكلمات الرئيسية الأفضل أداءً',\n  'keyword_query' => 'الكلمة الرئيسية / استعلام',\n  'position' => 'المركز',\n  'no_search_data' => 'لا توجد بيانات بحث متاحة لهذه الفترة.',\n",
    'de' => "  // Search Insights Missing Keys\n  'back_to_seo' => 'Zurück zu SEO',\n  'last_7_days' => 'Letzte 7 Tage',\n  'last_30_days' => 'Letzte 30 Tage',\n  'last_3_months' => 'Letzte 3 Monate',\n  'total_clicks' => 'Klicks insgesamt',\n  'total_impressions' => 'Impressionen insgesamt',\n  'top_performing_keywords' => 'Leistungsstärkste Keywords',\n  'keyword_query' => 'Keyword / Suchanfrage',\n  'position' => 'Position',\n  'no_search_data' => 'Keine Suchdaten für diesen Zeitraum verfügbar.',\n",
    'es' => "  // Search Insights Missing Keys\n  'back_to_seo' => 'Volver a SEO',\n  'last_7_days' => 'Últimos 7 días',\n  'last_30_days' => 'Últimos 30 días',\n  'last_3_months' => 'Últimos 3 meses',\n  'total_clicks' => 'Clics Totales',\n  'total_impressions' => 'Impresiones Totales',\n  'top_performing_keywords' => 'Palabras Clave de Mejor Rendimiento',\n  'keyword_query' => 'Palabra Clave / Consulta',\n  'position' => 'Posición',\n  'no_search_data' => 'No hay datos de búsqueda disponibles para este período.',\n",
    'tr' => "  // Search Insights Missing Keys\n  'back_to_seo' => 'SEO\\'ya Dön',\n  'last_7_days' => 'Son 7 Gün',\n  'last_30_days' => 'Son 30 Gün',\n  'last_3_months' => 'Son 3 Ay',\n  'total_clicks' => 'Toplam Tıklama',\n  'total_impressions' => 'Toplam Gösterim',\n  'top_performing_keywords' => 'En İyi Performans Gösteren Anahtar Kelimeler',\n  'keyword_query' => 'Anahtar Kelime / Sorgu',\n  'position' => 'Konum',\n  'no_search_data' => 'Bu dönem için arama verisi bulunmamaktadır.',\n",
    'zh' => "  // Search Insights Missing Keys\n  'back_to_seo' => '返回 SEO',\n  'last_7_days' => '过去 7 天',\n  'last_30_days' => '过去 30 天',\n  'last_3_months' => '过去 3 个月',\n  'total_clicks' => '总点击次数',\n  'total_impressions' => '总展示次数',\n  'top_performing_keywords' => '表现最佳的关键词',\n  'keyword_query' => '关键词 / 查询',\n  'position' => '排名',\n  'no_search_data' => '此期间没有可用的搜索数据。',\n"
];

foreach ($langs as $l) {
    $f = __DIR__ . '/lang/' . $l . '/admin.php';
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    if (strpos($c, "'total_clicks'") === false) {
        $c = preg_replace('/];\s*$/', $data[$l] . "];\n", $c);
        file_put_contents($f, $c);
        echo "Updated $l\n";
    } else {
        echo "Already present in $l\n";
    }
}
