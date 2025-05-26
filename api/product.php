<?php

require_once '../server/dbcon.php';
header('Content-Type: application/json');

$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$baseUrl .= str_replace('/api', '', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($baseUrl, '/') . '/storage/';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

$productId = intval($_GET['id']);

try {

    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.title,
            p.short_description,
            p.long_description,
            p.price,
            p.stock,
            c.name AS category_name,
            i.image_path AS main_image
        FROM products p
        LEFT JOIN category c ON p.category_id = c.id
        LEFT JOIN product_images i 
            ON p.id = i.product_id AND i.image_type = 'main'
        WHERE p.id = ?
    ");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }


    $stmt = $pdo->prepare("
        SELECT image_path 
        FROM product_images 
        WHERE product_id = ? AND image_type = 'additional'
    ");
    $stmt->execute([$productId]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    
    $stmt = $pdo->prepare("
        SELECT s.label 
        FROM product_sizes ps
        JOIN sizes s ON ps.size_id = s.id
        WHERE ps.product_id = ?
    ");
    $stmt->execute([$productId]);
    $sizes = $stmt->fetchAll(PDO::FETCH_COLUMN);

 
    $stmt = $pdo->prepare("
        SELECT c.name 
        FROM product_colors pc
        JOIN colors c ON pc.color_id = c.id
        WHERE pc.product_id = ?
    ");
    $stmt->execute([$productId]);
    $colors = $stmt->fetchAll(PDO::FETCH_COLUMN);


    $response = [
        'id' => $product['id'],
        'title' => $product['title'],
        'short_description' => $product['short_description'],
        'long_description' => $product['long_description'],
        'price' => $product['price'],
        'stock' => $product['stock'],
        'category' => $product['category_name'],
        'main_image' => $product['main_image'] ? $baseUrl . ltrim($product['main_image'], '/') : null,
    ];

   
    $imageCount = 1;
    foreach ($images as $img) {
        $response["image_$imageCount"] = $baseUrl . ltrim($img, '/');
        $imageCount++;
    }

    
    if (!empty($sizes)) {
        $response['sizes'] = implode(', ', $sizes);
    }

    if (!empty($colors)) {
        $response['colors'] = implode(', ', $colors);
    }

    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
