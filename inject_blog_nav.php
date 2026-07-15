<?php

$translations = [
    'en' => "'nav_blog' => 'Blog',",
    'ar' => "'nav_blog' => 'المدونة',",
    'es' => "'nav_blog' => 'Blog',",
    'de' => "'nav_blog' => 'Blog',",
    'zh' => "'nav_blog' => '博客',",
    'tr' => "'nav_blog' => 'Blog',"
];

foreach ($translations as $loc => $line) {
    $path = __DIR__ . "/lang/$loc/landing.php";
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // Append before the closing bracket if not already exists
        if (strpos($content, "'nav_blog'") === false) {
            $insert = "\n    // blog nav\n    " . $line . "\n";
            $content = preg_replace('/];\s*$/', $insert . "];\n", $content);
            file_put_contents($path, $content);
            echo "Updated $loc/landing.php\n";
        } else {
            echo "Already exists in $loc/landing.php\n";
        }
    }
}
