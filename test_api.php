<?php
// Simple API test script
require_once 'includes/koneksi.php';

// Test API endpoint
$url = 'http://localhost/inventory-QoryBusana-store/api/barang';
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "=== API TEST RESULTS ===\n";
echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n\n";

// Test with authentication
echo "=== TESTING WITH AUTH ===\n";
session_start();
$_SESSION['user_id'] = 1; // Simulate login

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n";
?>