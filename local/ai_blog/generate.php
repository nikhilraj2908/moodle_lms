<?php
require_once('../../config.php');

define('API_KEY', 'AIzaSyANlqseLkT0iS5zutGOfqQIROw9H0WAxKE');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];

    // Prepare API request
    $api_url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . API_KEY;
    $data = json_encode([
        "contents" => [
            [
                "parts" => [
                    ["text" => "Write a detailed blog on: $title"]
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
        $blog_content = $response['candidates'][0]['content']['parts'][0]['text'];

        // Save generated blog to database
        $DB->insert_record('ai_blog', [
            'title' => $title,
            'content' => $blog_content,
            'userid' => $USER->id,
            'timecreated' => time()
        ]);

        echo "<h2>Generated Blog</h2>";
        echo "<h3>$title</h3>";
        echo "<p>$blog_content</p>";
    } else {
        echo "<p>Error generating content. Try again!</p>";
    }
}
?>
