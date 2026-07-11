<?php
$states = json_decode(file_get_contents('states.json'), true);
foreach ($states as $st) {
    if (strpos($st['country_name'], 'Egypt') !== false) {
        echo "Egypt country_code in states.json: " . $st['country_code'] . "\n";
        break;
    }
}
$count = 0;
foreach ($states as $st) {
    if ($st['country_code'] === 'EG') $count++;
}
echo "Number of states with EG: $count\n";
