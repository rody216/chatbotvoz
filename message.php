<?php
// Configuración de la API de OpenAI
include 'config.php';

// Obtener el mensaje del usuario a través de AJAX
$getMesg = trim($_POST['text']);

// Primero, intentar obtener la respuesta del archivo JSON
$jsonFilePath = 'chatbot.json';
$response = null;

if (file_exists($jsonFilePath)) {
    $jsonData = file_get_contents($jsonFilePath);
    $data = json_decode($jsonData, true);

    if ($data && isset($data['queries']) && is_array($data['queries'])) {
        foreach ($data['queries'] as $item) {
            if (stripos($getMesg, $item['question']) !== false) {
                $response = $item['replies'];
                break;
            }
        }
    }
}

// Si no se encontró una respuesta en el JSON, usar la API de OpenAI
if ($response === null) {
    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => $getMesg]
        ]
    ];

    // Iniciar cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openai_api_key // Utiliza la variable correcta de configuración
    ]);

    // Ejecutar la solicitud
    $apiResponse = curl_exec($ch);
    if ($apiResponse === false) {
        $response = "Error al conectarse a la API de OpenAI.";
    } else {
        $responseData = json_decode($apiResponse, true);
        if (isset($responseData['choices'][0]['message']['content'])) {
            $response = $responseData['choices'][0]['message']['content'];
        } else {
            $response = "¡Lo siento, no puedo ayudarte con este inconveniente! Favor comunícate con el administrador en el siguiente enlace:</br><a href='#'>Contacto</a>";
        }
    }

    // Cerrar cURL
    curl_close($ch);
}

// Devolver la respuesta
echo $response;
?>
