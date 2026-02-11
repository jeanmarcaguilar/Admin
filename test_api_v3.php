<?php
$url = 'https://finance.microfinancial-1.com/api/manage_proposals.php';

function test_v($name, $params, $method = 'POST')
{
    global $url;
    echo "--- VARIANT: $name ($method) ---\n";

    $query = http_build_query($params);
    $target = $url;
    if ($method === 'GET') {
        $target .= '?' . $query;
    }

    $ch = curl_init($target);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    }

    $res = curl_exec($ch);
    echo "RES: $res\n\n";
    curl_close($ch);
}

test_v("V1_POST", ['action' => 'approve', 'reference_id' => 'PROP-189', 'status' => 'Approved']);
test_v("V2_GET", ['action' => 'approve', 'reference_id' => 'PROP-189', 'status' => 'Approved'], 'GET');
test_v("V3_POST_NO_ACTION", ['reference_id' => 'PROP-189', 'status' => 'Approved']);
test_v("V4_GET_NO_ACTION", ['reference_id' => 'PROP-189', 'status' => 'Approved'], 'GET');
test_v("V5_POST_LOWER", ['action' => 'approve', 'reference_id' => 'PROP-189', 'status' => 'approved']);
?>