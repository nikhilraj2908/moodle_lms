<?php
require_once('../../config.php');

define('API_KEY', 'YOUR_GOOGLE_GEMINI_API_KEY'); // Replace with your Gemini API Key

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];

    // Prepare API request
    $api_url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . API_KEY;
    $data = json_encode([
        "contents" => [
            [
                "parts" => [
                    ["text" => "Summarize the topic '$title' in one or two sentences."]
                ]
            ]
        ]
    ]);

    $options = [
        "http" => [
            "header"  => "Content-Type: application/json",
            "method"  => "POST",
            "content" => $data
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($api_url, false, $context);
    $response = json_decode($result, true);

    if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
        echo trim($response['candidates'][0]['content']['parts'][0]['text']);
    } else {
        echo "AI Summary generation failed. Try again!";
    }
}
?>
