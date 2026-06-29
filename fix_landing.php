<?php
$f = 'lang/tr/landing.php';
$c = file_get_contents($f);

// Fix the exact string the user mentioned
$c = str_replace(
    "ve İ\xEF\xBF\xBDinizi B\xEF\xBF\xBDy\xEF\xBF\xBDtmek İ\xEF\xBF\xBDin En İ\xEF\xBF\xBDyi Platform",
    "ve İşinizi Büyütmek İçin En İyi Platform",
    $c
);

// Fix the description right below it
$c = str_replace(
    "Projeleriniz i\xEF\xBF\xBDin g\xEF\xBF\xBDvenilir şirketler bulun veya d\xEF\xBF\xBDnya \xEF\xBF\xBDapında yeni m\xEF\xBF\xBDşteriler kazanmak i\xEF\xBF\xBDin işletmenizi ekleyin.",
    "Projeleriniz için güvenilir şirketler bulun veya dünya çapında yeni müşteriler kazanmak için işletmenizi ekleyin.",
    $c
);

// Another common one in CTA
$c = str_replace(
    "İşletmenize hak ettiği gör\xEF\xBF\xBDn\xEF\xBF\xBDrl\xEF\xBF\xBDğ\xEF\xBF\xBD kazandırın",
    "İşletmenize hak ettiği görünürlüğü kazandırın",
    $c
);

// And CTA desc
$c = str_replace(
    "Özel ağımıza bug\xEF\xBF\xBDn katılın. Hizmetlerinizi sergilemek, g\xEF\xBF\xBDven oluşturmak ve sunduğunuz şeyleri aktif olarak arayan m\xEF\xBF\xBDşterilerle sorunsuz bir şekilde bağlantı kurmak i\xEF\xBF\xBDin profesyonel bir profil oluşturun.",
    "Özel ağımıza bugün katılın. Hizmetlerinizi sergilemek, güven oluşturmak ve sunduğunuz şeyleri aktif olarak arayan müşterilerle sorunsuz bir şekilde bağlantı kurmak için profesyonel bir profil oluşturun.",
    $c
);

$c = str_replace("Bizimle B\xEF\xBF\xBDy\xEF\xBF\xBDy\xEF\xBF\xBDn", "Bizimle Büyüyün", $c);

// General simple replacements where meaning is unambiguous
$c = str_replace("i\xEF\xBF\xBDin", "için", $c);
$c = str_replace("İ\xEF\xBF\xBDin", "İçin", $c);
$c = str_replace("d\xEF\xBF\xBDnya", "dünya", $c);
$c = str_replace("D\xEF\xBF\xBDnya", "Dünya", $c);
$c = str_replace("g\xEF\xBF\xBDven", "güven", $c);
$c = str_replace("G\xEF\xBF\xBDven", "Güven", $c);
$c = str_replace("b\xEF\xBF\xBDy\xEF\xBF\xBD", "büyü", $c);
$c = str_replace("\xEF\xBF\xBDapında", "çapında", $c);

file_put_contents($f, $c);
echo "Fixed landing.php\n";
