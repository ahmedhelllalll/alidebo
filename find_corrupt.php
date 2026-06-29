<?php
$dir = 'lang/tr';
$files = glob($dir . '/*.php');
$words = [];
foreach($files as $f) {
    $c = file_get_contents($f);
    // clean up punctuation to get clean words
    $c = str_replace(["'", '"', '<', '>', '=', '{', '}', '(', ')', '[', ']', ',', '.', ';', ':', '!', '?', '-', "\n", "\r"], ' ', $c);
    $parts = explode(' ', $c);
    foreach($parts as $w) {
        if (strpos($w, "\xEF\xBF\xBD") !== false) {
            $words[$w] = 1;
        }
    }
}
$keys = array_keys($words);
echo "<?php\n\$replacements = [\n";
foreach($keys as $k) {
    echo "    '$k' => '$k',\n";
}
echo "];\n";
