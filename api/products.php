<?php

require_once '../server/dbcon.php';
header('Content-Type: application/json');

$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$baseUrl .= str_replace('/api', '', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($baseUrl, '/') . '/storage/';

try {
    $query = "
        SELECT 
            p.id,
            p.title,
            p.price,
            i.image_path
        FROM 
            products p
        LEFT JOIN 
            category c ON p.category_id = c.id
        LEFT JOIN 
            product_images i ON p.id = i.product_id AND i.image_type = 'main'
        WHERE 
            p.category_id IS NOT NULL
    ";

    $params = [];


    if (!empty($_GET['category'])) {
        $query .= " AND c.name = ?";
        $params[] = $_GET['category'];
    }


    if (!empty($_GET['search'])) {
        $query .= " AND p.title LIKE ?";
        $params[] = '%' . $_GET['search'] . '%';
    }

    $query .= " ORDER BY p.created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

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

    echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch products']);
}
