<?php
require_once(__DIR__ . '/../../config.php');
require_login();

header('Content-Type: application/json');

define('API_KEY', 'AIzaSyANlqseLkT0iS5zutGOfqQIROw9H0WAxKE'); // Replace with your actual Gemini API Key

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['title'])) {
    $title = $_GET['title'];

    // Correct API URL
    $api_url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . API_KEY;

    // Request a **3-line summary** with a **50-word limit**
    $data = json_encode([
        "contents" => [
            [
                "parts" => [
                    ["text" => "Generate a 3-line summary (max 50 words) for: '$title'. Avoid formatting like ** and bullet points."]
                ]
            ]
        ]
    ]);

    // Send the API request
    $options = [
        "http" => [
            "header"  => "Content-Type: application/json",
            "method"  => "POST",
            "content" => $data
        ]
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($api_url, false, $context);

    if ($result === FALSE) {
        echo json_encode(["error" => "Failed to fetch summary from Gemini API"]);
        exit;
    }

    // Decode the API response
    $response = json_decode($result, true);

    // Extract the summary text safely
    if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
        $summary = $response['candidates'][0]['content']['parts'][0]['text'];

        // **Process Text:**
        // 1. Remove bold (`**`) and bullet points (`*`)
        $summary = preg_replace('/\*\*|\*/', '', $summary);

        // 2. Limit the summary to **3 lines** (split by `. ` for natural breaks)
        $sentences = explode('. ', $summary);
        $summary = implode('. ', array_slice($sentences, 0, 3)) . '.';

        // 3. Ensure **50-word limit**
        $words = explode(' ', $summary);
        if (count($words) > 50) {
            $summary = implode(' ', array_slice($words, 0, 50)) . '...';
        }

        echo json_encode(["summary" => $summary]);
    } else {
        echo json_encode(["error" => "Invalid API response"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
