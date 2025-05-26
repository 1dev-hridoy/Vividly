<?php

require_once '../server/dbcon.php';

header('Content-Type: application/json');

$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$baseUrl .= str_replace('/api', '', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($baseUrl, '/') . '/storage/';

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.title,
            p.price,
            i.image_path
        FROM 
            products p
        LEFT JOIN 
            product_images i ON p.id = i.product_id AND i.image_type = 'main'
        WHERE 
            p.category_id IS NOT NULL
        ORDER BY 
            p.created_at DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [];

    foreach ($products as $product) {
        $output[] = [
            'id' => $product['id'],
            'title' => $product['title'],
            'price' => $product['price'],
            'image_url' => $product['image_path'] 
                ? $baseUrl . ltrim($product['image_path'], '/')
                : null
        ];
    }

    echo json_encode($output);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch products']);
}
