<?php

$locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];
$translations = [
    'en' => [
        'blog_empty_title' => 'Great Things Are Coming',
        'blog_empty_msg' => 'We are currently crafting high-quality insights, industry news, and comprehensive guides. Stay tuned for our upcoming publications.',
    ],
    'ar' => [
        'blog_empty_title' => 'أشياء رائعة قادمة',
        'blog_empty_msg' => 'نحن نقوم حاليًا بإعداد رؤى عالية الجودة وأخبار الصناعة وأدلة شاملة. ترقبوا منشوراتنا القادمة.',
    ],
    'es' => [
        'blog_empty_title' => 'Grandes cosas están por venir',
        'blog_empty_msg' => 'Actualmente estamos elaborando conocimientos de alta calidad, noticias de la industria y guías completas. Mantente atento a nuestras próximas publicaciones.',
    ],
    'de' => [
        'blog_empty_title' => 'Große Dinge kommen auf uns zu',
        'blog_empty_msg' => 'Wir erarbeiten derzeit hochwertige Einblicke, Branchennachrichten und umfassende Leitfäden. Bleiben Sie dran für unsere kommenden Veröffentlichungen.',
    ],
    'zh' => [
        'blog_empty_title' => '伟大的事情即将到来',
        'blog_empty_msg' => '我们目前正在精心制作高质量的见解、行业新闻和综合指南。请继续关注我们即将推出的出版物。',
    ],
    'tr' => [
        'blog_empty_title' => 'Harika Şeyler Geliyor',
        'blog_empty_msg' => 'Şu anda yüksek kaliteli içgörüler, sektör haberleri ve kapsamlı rehberler hazırlıyoruz. Gelecek yayınlarımız için takipte kalın.',
    ]
];

foreach ($locales as $locale) {
    $filePath = __DIR__ . "/lang/{$locale}/home.php";
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        $toInsert = "";
        foreach ($translations[$locale] as $key => $value) {
            if (strpos($content, "'{$key}'") === false) {
                $toInsert .= "  '{$key}' => '{$value}',\n";
            }
        }

        if (!empty($toInsert)) {
            // Insert before the last bracket
            $content = preg_replace('/(];\s*)$/', $toInsert . "$1", $content);
            file_put_contents($filePath, $content);
            echo "Updated {$locale}/home.php\n";
        }
    }
}

echo "Done.\n";
