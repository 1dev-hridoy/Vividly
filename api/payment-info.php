<?php

require_once '../server/dbcon.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT * FROM payment_info ORDER BY updated_at DESC LIMIT 1");
    $stmt->execute();
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($payment) {
  
        foreach ($payment as $key => $value) {
            $payment[$key] = $value === null ? "null" : $value;
        }

        echo json_encode($payment, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode([
            "id" => "null",
            "bkash_number" => "null",
            "bkash_note" => "null",
            "nagad_number" => "null",
            "nagad_note" => "null",
            "rocket_number" => "null",
            "rocket_note" => "null",
            "updated_at" => "null"
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to fetch payment information"
    ]);
}
