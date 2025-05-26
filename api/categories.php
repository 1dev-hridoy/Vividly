<?php

require_once '../server/dbcon.php'; 

header('Content-Type: application/json');

$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$baseUrl .= str_replace('/api', '', dirname($_SERVER['SCRIPT_NAME'])); 
$baseUrl = rtrim($baseUrl, '/') . '/storage/';

try {
    $stmt = $pdo->query("SELECT id, name, image FROM category ORDER BY created_at DESC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $results = [];

    foreach ($categories as $cat) {
        $results[] = [
            'id' => $cat['id'],
            'name' => $cat['name'],
            'image_url' => $baseUrl . ltrim($cat['image'], '/')
        ];
    }

    echo json_encode($results);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
