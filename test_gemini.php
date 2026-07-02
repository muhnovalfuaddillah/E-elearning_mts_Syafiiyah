<?php

$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "No .env file found!\n";
    exit(1);
}

$apiKey = null;
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2 && trim($parts[0]) === 'GEMINI_API_KEY') {
        $apiKey = trim($parts[1]);
        $apiKey = trim($apiKey, '"\'');
        break;
    }
}

if (empty($apiKey)) {
    echo "GEMINI_API_KEY not found in .env!\n";
    exit(1);
}

echo "Testing Gemini models with API Key ending in: ..." . substr($apiKey, -5) . "\n\n";

$models = [
    ['version' => 'v1', 'name' => 'gemini-1.5-flash'],
    ['version' => 'v1beta', 'name' => 'gemini-1.5-flash'],
    ['version' => 'v1', 'name' => 'gemini-1.5-pro'],
    ['version' => 'v1beta', 'name' => 'gemini-1.5-pro'],
    ['version' => 'v1', 'name' => 'gemini-2.0-flash'],
    ['version' => 'v1beta', 'name' => 'gemini-2.0-flash'],
    ['version' => 'v1beta', 'name' => 'gemini-2.0-flash-exp'],
    ['version' => 'v1', 'name' => 'gemini-1.5-flash-8b'],
];

foreach ($models as $m) {
    $version = $m['version'];
    $name = $m['name'];
    $url = "https://generativelanguage.googleapis.com/{$version}/models/{$name}:generateContent?key={$apiKey}";
    
    $body = [
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => 'Hi']
                ]
            ]
        ],
        'generationConfig' => [
            'maxOutputTokens' => 10
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Model: {$name} ({$version}) | HTTP Status: {$status}\n";
    if ($status !== 200) {
        $resObj = json_decode($response, true);
        $msg = $resObj['error']['message'] ?? 'Unknown error';
        echo "  Error: {$msg}\n";
    } else {
        echo "  Success!\n";
    }
}
