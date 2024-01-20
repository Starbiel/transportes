<?php 
$curl = curl_init();
$minhaChaveAPI = $_SERVER['MINHA_CHAVE_API'];

$origem = $_POST['origemInput'];
$destiny = $_POST['destinyInput'];
$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . urlencode($origem) . "&destinations=" . urlencode($destiny) . "&units=metric&key=$minhaChaveAPI";
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($curl);
curl_close($curl);
header('Content-Type: application/json');
echo ($response);
?>