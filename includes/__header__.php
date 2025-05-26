<?php
require_once 'server/dbcon.php';

try {
    $stmt = $pdo->query("SELECT site_name, site_description, site_logo FROM site_settings ORDER BY updated_at DESC LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $log_file = 'storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Failed to fetch site settings: " . $e->getMessage() . "\n", FILE_APPEND);
    $settings = [
        'site_name' => 'Minimal Shop',
        'site_description' => '',
        'site_logo' => ''
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_name']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($settings['site_description']); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($settings['site_name']); ?>">

    <link rel="stylesheet" href="assets/css/ecom/main.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="#">
            <?php if (!empty($settings['site_logo'])): ?>
                <a href="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']); ?>">
  <img src="<?php echo htmlspecialchars('storage/' . $settings['site_logo']); ?>" alt="Logo" height="40">
</a>

            <?php else: ?>
                <?php echo htmlspecialchars($settings['site_name']); ?>
            <?php endif; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>
            </ul>
            <div class="d-flex ms-3">
                <a href="track-order.php" class="nav-link me-3">
                <i class="fa-solid fa-truck"></i>
                </a>
                <a href="cart.php" class="nav-link position-relative">
                    <i class="fas fa-shopping-bag"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>
</nav>