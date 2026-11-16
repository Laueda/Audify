<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$endpoint = $_GET['endpoint'] ?? '';

if ($endpoint === '') {
    echo json_encode(['error' => 'Endpoint manquant']);
    exit();
}

$url = 'https://api.deezer.com/' . $endpoint;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode(['error' => 'Impossible de contacter Deezer']);
    exit();
}

echo $response;
?>