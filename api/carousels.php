<?php

require_once '../server/dbcon.php'; 

header('Content-Type: application/json');

$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$baseUrl .= str_replace('/api', '', dirname($_SERVER['SCRIPT_NAME'])); 
$baseUrl = rtrim($baseUrl, '/') . '/storage/';

try {
    $stmt = $pdo->query("SELECT id, image_path FROM carousels ORDER BY created_at DESC");
    $carousels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $results = [];

    foreach ($carousels as $carousel) {
        $results[] = [
            'image_url' => $baseUrl . ltrim($carousel['image_path'], '/')
        ];
    }

    echo json_encode($results);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error.']);
}
