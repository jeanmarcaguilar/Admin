<?php
$url = 'https://finance.microfinancial-1.com/api/manage_proposals.php';

function test_json($name, $params)
{
    global $url;
    echo "--- VARIANT: $name (POST JSON) ---\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $res = curl_exec($ch);
    echo "RES: $res\n\n";
    curl_close($ch);
}

test_json("J1", ['action' => 'approve', 'reference_id' => 'PROP-189', 'status' => 'Approved']);
test_json("J2", ['action' => 'reject', 'reference_id' => 'PROP-189', 'status' => 'Rejected']);
test_json("J3", ['action' => 'update', 'reference_id' => 'PROP-189', 'status' => 'Approved']);
test_json("J4", ['reference_id' => 'PROP-189', 'status' => 'Approved']);
?>