<?php
$f = 'lang/tr/landing.php';
$c = file_get_contents($f);
$target = "ve \xC4\xB0\xC5\x9F\x69\x6E\x69\x7A\x69 B\xEF\xBF\xBDy\xEF\xBF\xBDtmek \xC4\xB0\xEF\xBF\xBDin En \xC4\xB0yi Platform";
$replace = "ve \xC4\xB0\xC5\x9F\x69\x6E\x69\x7A\x69 B\xC3\xBCy\xC3\xBCtmek \xC4\xB0\xC3\xA7in En \xC4\xB0yi Platform";

var_dump(strpos($c, $target));
$c = str_replace($target, $replace, $c);

file_put_contents($f, $c);
