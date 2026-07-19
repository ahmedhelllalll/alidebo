<?php
$langs = ['en', 'ar', 'de', 'es', 'tr', 'zh'];
$data = [
    'en' => "  'device_breakdown' => 'Device Breakdown',\n  'device_desktop' => 'Desktop',\n  'device_mobile' => 'Mobile',\n  'device_tablet' => 'Tablet',\n",
    'ar' => "  'device_breakdown' => 'تحليل الأجهزة',\n  'device_desktop' => 'كمبيوتر مكتبي',\n  'device_mobile' => 'جوال',\n  'device_tablet' => 'جهاز لوحي',\n",
    'de' => "  'device_breakdown' => 'Geräteaufschlüsselung',\n  'device_desktop' => 'Desktop',\n  'device_mobile' => 'Mobil',\n  'device_tablet' => 'Tablet',\n",
    'es' => "  'device_breakdown' => 'Desglose de Dispositivos',\n  'device_desktop' => 'Escritorio',\n  'device_mobile' => 'Móvil',\n  'device_tablet' => 'Tableta',\n",
    'tr' => "  'device_breakdown' => 'Cihaz Dağılımı',\n  'device_desktop' => 'Masaüstü',\n  'device_mobile' => 'Mobil',\n  'device_tablet' => 'Tablet',\n",
    'zh' => "  'device_breakdown' => '设备细分',\n  'device_desktop' => '桌面',\n  'device_mobile' => '移动的',\n  'device_tablet' => '平板电脑',\n"
];

foreach ($langs as $l) {
    $f = __DIR__ . '/lang/' . $l . '/admin.php';
    if (!file_exists($f)) continue;
    $c = file_get_contents($f);
    if (strpos($c, "'device_breakdown'") === false) {
        $c = preg_replace('/];\s*$/', $data[$l] . "];\n", $c);
        file_put_contents($f, $c);
        echo "Updated $l\n";
    } else {
        echo "Already present in $l\n";
    }
}
