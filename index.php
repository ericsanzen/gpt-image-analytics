<?php
// Replace with your actual OpenAI API key
$openaiApiKey = '****';

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $imagePath = $_FILES['image']['tmp_name'];

    // Encode image to base64
    $base64Image = base64_encode(file_get_contents($imagePath));

    // Construct API request
    $apiUrl = 'https://api.openai.com/v1/chat/completions';
    $requestData = [
        'model' => 'gpt-4-vision-preview',
        'messages' => [
            [
                'role' => 'user',
                'content' => [
                    ['type' => 'text', 'text' => 'What do you see?'],
                    ['type' => 'image_url', 'image_url' => ['url' => 'data:image/jpeg;base64,' . $base64Image]],
                ],
            ],
        ],
        'max_tokens' => 300,
    ];

    // Make API request
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openaiApiKey,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    $response = curl_exec($ch);
    curl_close($ch);

    // Parse and display GPT-4's response
    $decodedResponse = json_decode($response, true);

    if (isset($decodedResponse['choices'][0]['message']['content'])) {
        echo 'GPT-4 says: ' . $decodedResponse['choices'][0]['message']['content'];
    } else {
        echo 'Error processing image.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPT-4 Image Analysis</title>
</head>
<body>
    <h1>Upload an image</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*">
        <input type="submit" value="Analyze">
    </form>
</body>
</html>
