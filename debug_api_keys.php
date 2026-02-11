<?php
$url = 'https://finance.microfinancial-1.com/api/manage_proposals.php';
$response = file_get_contents($url);
$data = json_decode($response, true);
if (isset($data['data'][0])) {
    echo "KEYS: " . implode(", ", array_keys($data['data'][0])) . "\n";
    print_r($data['data'][0]);
} else {
    echo "No data found\n";
}
?>