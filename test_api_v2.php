<?php
$url = 'https://finance.microfinancial-1.com/api/manage_proposals.php';

function test_v($name, $params, $as_json = false)
{
    global $url;
    echo "--- VARIANT: $name ---\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    if ($as_json) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    $res = curl_exec($ch);
    echo "RES: $res\n\n";
    curl_close($ch);
}

test_v("V1_POST_REF_ID", ['action' => 'approve', 'reference_id' => 'PROP-189', 'status' => 'Approved']);
test_v("V2_POST_REF_NO", ['action' => 'approve', 'ref_no' => 'PROP-189', 'status' => 'Approved']);
test_v("V3_POST_UPDATE_STATUS", ['action' => 'update_status', 'reference_id' => 'PROP-189', 'status' => 'Approved']);
?>