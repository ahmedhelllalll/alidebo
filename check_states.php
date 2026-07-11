<?php
$data = json_decode(file_get_contents('states.json'), true);
if (isset($data[0])) {
    print_r(array_keys($data[0]['translations']));
    echo "\nSample AR: " . $data[0]['translations']['ar'] . "\n";
    echo "\nSample DE: " . $data[0]['translations']['de'] . "\n";
} else {
    echo "Failed to load states.json";
}
