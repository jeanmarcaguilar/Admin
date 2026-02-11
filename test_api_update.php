<?php
$url = 'https://finance.microfinancial-1.com/api/manage_proposals.php';

function test_update($params, $as_json = false)
{
    global $url;
    echo "Testing with params: " . json_encode($params) . ($as_json ? " (JSON)" : " (POST)") . "\n";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    if ($as_json) {
        $data = json_encode($params);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    $response = curl_exec($ch);
    echo "Response: " . $response . "\n\n";
    curl_close($ch);
}

// Try different variations based on the error "Missing 'reference_id' and 'status'"
test_update(['action' => 'approve', 'reference_id' => 'PROP-189', 'status' => 'Approved']);
test_update(['action' => 'update_status', 'reference_id' => 'PROP-189', 'status' => 'Approved']);
test_update(['reference_id' => 'PROP-189', 'status' => 'Approved']);
test_update(['action' => 'approve', 'reference_id' => 'PROP-189', 'status' => 'Approved'], true);
?>