<?php
$f = 'lang/tr/landing.php';
$c = file_get_contents($f);

$target = "ve İşinizi B\xEF\xBF\xBDy\xEF\xBF\xBDtmek İ\xEF\xBF\xBDin En İyi Platform";
$replace = "ve İşinizi Büyütmek İçin En İyi Platform";
$c = str_replace($target, $replace, $c);

// Also fix the desc
$target2 = "Projeleriniz i\xEF\xBF\xBDin g\xEF\xBF\xBDvenilir şirketler bulun veya d\xEF\xBF\xBDnya \xEF\xBF\xBDapında yeni m\xEF\xBF\xBDşteriler kazanmak i\xEF\xBF\xBDin işletmenizi ekleyin.";
$replace2 = "Projeleriniz için güvenilir şirketler bulun veya dünya çapında yeni müşteriler kazanmak için işletmenizi ekleyin.";
$c = str_replace($target2, $replace2, $c);

file_put_contents($f, $c);
echo "Fixed landing.php exact string\n";
