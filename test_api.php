<?php
// Test script to verify the financial API endpoint
echo "Testing Financial API Endpoint...\n";
echo "================================\n\n";

$url = 'https://finance.microfinancial-1.com/api/manage_proposals.php';

echo "API URL: $url\n\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: PHP Test Script'
        ],
        'timeout' => 10
    ]
]);

echo "Making GET request...\n";

try {
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        echo "❌ ERROR: Failed to fetch data from API\n";
        echo "Possible issues:\n";
        echo "- API endpoint is down\n";
        echo "- Network connectivity issues\n";
        echo "- CORS restrictions\n";
        exit(1);
    }

    echo "✅ SUCCESS: Received response from API\n\n";

    // Try to decode JSON
    $data = json_decode($response, true);

    if ($data === null) {
        echo "❌ ERROR: Response is not valid JSON\n";
        echo "Raw response:\n";
        echo substr($response, 0, 500) . (strlen($response) > 500 ? '...' : '') . "\n";
        exit(1);
    }

    echo "✅ SUCCESS: Response is valid JSON\n\n";

    // Check response structure
    echo "Response Analysis:\n";
    echo "- Response type: " . gettype($data) . "\n";

    if (is_array($data)) {
        echo "- Is array: Yes\n";
        echo "- Array length: " . count($data) . "\n";

        if (isset($data['success'])) {
            echo "- Has 'success' key: Yes (" . $data['success'] . ")\n";
        } else {
            echo "- Has 'success' key: No\n";
        }

        if (isset($data['data'])) {
            echo "- Has 'data' key: Yes\n";
            if (is_array($data['data'])) {
                echo "- Data is array: Yes\n";
                echo "- Data array length: " . count($data['data']) . "\n";

                if (count($data['data']) > 0) {
                    echo "\nFirst item structure:\n";
                    $firstItem = $data['data'][0];
                    foreach ($firstItem as $key => $value) {
                        echo "- $key: " . (is_string($value) ? "\"$value\"" : $value) . "\n";
                    }
                }
            } else {
                echo "- Data is array: No (type: " . gettype($data['data']) . ")\n";
            }
        } else {
            echo "- Has 'data' key: No\n";
        }

        if (isset($data['count'])) {
            echo "- Has 'count' key: Yes (" . $data['count'] . ")\n";
        } else {
            echo "- Has 'count' key: No\n";
        }

    } else {
        echo "- Is array: No (type: " . gettype($data) . ")\n";
    }

    echo "\nFull Response Structure:\n";
    print_r($data);

} catch (Exception $e) {
    echo "❌ ERROR: Exception occurred\n";
    echo "Message: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "API Test Complete\n";
?>
