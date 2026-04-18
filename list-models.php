<?php
require_once __DIR__ . '/config/config.php';

echo "🔍 Checking available Gemini models...\n\n";

$url = 'https://generativelanguage.googleapis.com/v1/models?key=' . AI_API_KEY;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_error($ch)) {
    echo "❌ cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "HTTP Status: $httpCode\n\n";

    if ($httpCode === 200) {
        $data = json_decode($response, true);

        if (isset($data['models'])) {
            echo "✅ Available models:\n";
            foreach ($data['models'] as $model) {
                echo "  - " . $model['name'] . "\n";
                if (isset($model['supportedGenerationMethods'])) {
                    echo "    Methods: " . implode(', ', $model['supportedGenerationMethods']) . "\n";
                }
                echo "\n";
            }
        } else {
            echo "❌ No models found in response\n";
            echo "Response: " . $response . "\n";
        }
    } else {
        echo "❌ API Error ($httpCode): " . $response . "\n";
    }
}

curl_close($ch);
?>