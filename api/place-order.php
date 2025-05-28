<?php

require_once '../server/dbcon.php';
header('Content-Type: application/json; charset=utf-8');

ob_start();
ob_clean();

try {
 
    $input = file_get_contents('php://input');
    if (empty($input)) {
        http_response_code(400);
        echo json_encode(['error' => 'Empty request body']);
        exit;
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON: ' . json_last_error_msg()]);
        exit;
    }

   
    $required = [
        'name', 'email', 'mobile',
        'shipping_address', 'shipping_district', 'shipping_zipcode',
        'billing_address', 'billing_district', 'billing_zipcode',
        'payment_method', 'product_id', 'price', 'quantity',
        'color_name', 'size_name'
    ];

    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            http_response_code(400);
            echo json_encode(['error' => "Missing or empty field: $field"]);
            exit;
        }
    }


    $valid_payment_methods = ['bkash', 'nagad', 'rocket', 'card', 'paypal', 'apple_pay'];
    if (!in_array($data['payment_method'], $valid_payment_methods)) {
        http_response_code(400);
        echo json_encode(['error' => "Invalid payment method: {$data['payment_method']}"]);
        exit;
    }

    if (in_array($data['payment_method'], ['bkash', 'nagad', 'rocket'])) {
        if (empty($data['mobile_number']) || empty($data['transaction_id'])) {
            http_response_code(400);
            echo json_encode(['error' => "Mobile number and transaction ID are required for mobile payments."]);
            exit;
        }
    }


    if (!is_numeric($data['price']) || $data['price'] <= 0) {
        http_response_code(400);
        echo json_encode(['error' => "Price must be a positive number"]);
        exit;
    }
    if (!is_numeric($data['quantity']) || !is_int(+$data['quantity']) || $data['quantity'] <= 0) {
        http_response_code(400);
        echo json_encode(['error' => "Quantity must be a positive integer"]);
        exit;
    }
    if (!is_numeric($data['product_id']) || !is_int(+$data['product_id']) || $data['product_id'] <= 0) {
        http_response_code(400);
        echo json_encode(['error' => "Product ID must be a positive integer"]);
        exit;
    }


    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id FROM colors WHERE name = :color_name");
    $stmt->execute([':color_name' => $data['color_name']]);
    $color_id = $stmt->fetchColumn();
    if ($color_id === false) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => "Invalid color name: {$data['color_name']}"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM sizes WHERE label = :size_name");
    $stmt->execute([':size_name' => $data['size_name']]);
    $size_id = $stmt->fetchColumn();
    if ($size_id === false) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => "Invalid size name: {$data['size_name']}"]);
        exit;
    }


    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_colors WHERE product_id = :product_id AND color_id = :color_id");
    $stmt->execute([':product_id' => $data['product_id'], ':color_id' => $color_id]);
    if ($stmt->fetchColumn() == 0) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => "Color {$data['color_name']} is not available for this product."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_sizes WHERE product_id = :product_id AND size_id = :size_id");
    $stmt->execute([':product_id' => $data['product_id'], ':size_id' => $size_id]);
    if ($stmt->fetchColumn() == 0) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => "Size {$data['size_name']} is not available for this product."]);
        exit;
    }

    $name_parts = explode(' ', trim($data['name']), 2);
    $first_name = $name_parts[0];
    $last_name = isset($name_parts[1]) ? $name_parts[1] : '';

    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, phone)
        VALUES (:first_name, :last_name, :email, :phone)
        ON DUPLICATE KEY UPDATE first_name = :first_name, last_name = :last_name, phone = :phone
    ");
    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $data['email'],
        ':phone' => $data['mobile']
    ]);
    $user_id = $pdo->lastInsertId() ?: $pdo->query("SELECT id FROM users WHERE email = " . $pdo->quote($data['email']))->fetchColumn();


    $stmt = $pdo->prepare("
        INSERT INTO addresses (
            user_id, address_type, street_address, division_name, 
            district_name, city, postal_code, save_for_future
        ) VALUES (
            :user_id, 'shipping', :street_address, :division_name, 
            :district_name, :city, :postal_code, :save_for_future
        )
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':street_address' => $data['shipping_address'],
        ':division_name' => $data['shipping_district'],
        ':district_name' => $data['shipping_district'],
        ':city' => $data['shipping_district'], 
        ':postal_code' => $data['shipping_zipcode'],
        ':save_for_future' => isset($data['save_address']) ? 1 : 0
    ]);
    $shipping_address_id = $pdo->lastInsertId();


    $billing_address_id = $shipping_address_id;
    if ($data['billing_address'] !== $data['shipping_address'] ||
        $data['billing_district'] !== $data['shipping_district'] ||
        $data['billing_zipcode'] !== $data['shipping_zipcode']) {
        $stmt = $pdo->prepare("
            INSERT INTO addresses (
                user_id, address_type, street_address, division_name, 
                district_name, city, postal_code, save_for_future
            ) VALUES (
                :user_id, 'billing', :street_address, :division_name, 
                :district_name, :city, :postal_code, :save_for_future
            )
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':street_address' => $data['billing_address'],
            ':division_name' => $data['billing_district'],
            ':district_name' => $data['billing_district'],
            ':city' => $data['billing_district'],
            ':postal_code' => $data['billing_zipcode'],
            ':save_for_future' => isset($data['save_address']) ? 1 : 0
        ]);
        $billing_address_id = $pdo->lastInsertId();
    }


    $date = date('Ymd');
    $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    $custom_order_id = "ORD-$date-$random";


    $subtotal = floatval($data['price']) * intval($data['quantity']);
    $tax_rate = 0.08;
    $tax = $subtotal * $tax_rate;
    $shipping_cost = 0;
    $discount = isset($data['discount']) ? floatval($data['discount']) : 0;
    $discount_amount = $subtotal * $discount;
    $total = $subtotal + $tax + $shipping_cost - $discount_amount;


    $orderQuery = "
        INSERT INTO orders (
            custom_order_id, user_id, shipping_address_id, billing_address_id,
            subtotal, shipping_cost, tax, discount, total, payment_method, status
        ) VALUES (
            :custom_order_id, :user_id, :shipping_address_id, :billing_address_id,
            :subtotal, :shipping_cost, :tax, :discount, :total, :payment_method, 'pending'
        )
    ";
    $stmt = $pdo->prepare($orderQuery);
    $stmt->execute([
        ':custom_order_id' => $custom_order_id,
        ':user_id' => $user_id,
        ':shipping_address_id' => $shipping_address_id,
        ':billing_address_id' => $billing_address_id,
        ':subtotal' => $subtotal,
        ':shipping_cost' => $shipping_cost,
        ':tax' => $tax,
        ':discount' => $discount_amount,
        ':total' => $total,
        ':payment_method' => $data['payment_method']
    ]);
    $order_id = $pdo->lastInsertId();

 
    $itemQuery = "
        INSERT INTO order_items (
            order_id, product_id, color_id, size_id, 
            quantity, unit_price, total_price
        ) VALUES (
            :order_id, :product_id, :color_id, :size_id, 
            :quantity, :unit_price, :total_price
        )
    ";
    $stmt = $pdo->prepare($itemQuery);
    $stmt->execute([
        ':order_id' => $order_id,
        ':product_id' => $data['product_id'],
        ':color_id' => $color_id,
        ':size_id' => $size_id,
        ':quantity' => intval($data['quantity']),
        ':unit_price' => floatval($data['price']),
        ':total_price' => $subtotal
    ]);


    $paymentQuery = "
        INSERT INTO payments (
            order_id, payment_method, transaction_id, mobile_number, amount, status
        ) VALUES (
            :order_id, :payment_method, :transaction_id, :mobile_number, :amount, 'pending'
        )
    ";
    $stmt = $pdo->prepare($paymentQuery);
    $stmt->execute([
        ':order_id' => $order_id,
        ':payment_method' => $data['payment_method'],
        ':transaction_id' => $data['transaction_id'] ?? null,
        ':mobile_number' => $data['mobile_number'] ?? null,
        ':amount' => $total
    ]);

    $pdo->commit();

  
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully.',
        'order_id' => $custom_order_id
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Failed to place order: ' . $e->getMessage()]);
}