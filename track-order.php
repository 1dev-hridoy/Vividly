<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - Minimal Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/ecom/track-order.css">
</head>
<body>
    <?php
    require_once 'server/dbcon.php';

    global $pdo;
    $order_id = htmlspecialchars($_GET['order_id'] ?? '', ENT_QUOTES, 'UTF-8');

    $order = null;
    $order_items = [];
    $shipping_address = null;
    $error_message = null;

    if ($order_id) {
        try {
         
            $stmt = $pdo->prepare("
                SELECT o.id, o.custom_order_id, o.total, o.created_at, o.status, o.payment_method,
                       u.first_name, u.last_name, u.email
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.custom_order_id = ?
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {

                $stmt = $pdo->prepare("
                    SELECT street_address, city, division_name, district_name, upzila_name, union_name, postal_code
                    FROM addresses
                    WHERE id = (SELECT shipping_address_id FROM orders WHERE custom_order_id = ?)
                ");
                $stmt->execute([$order_id]);
                $shipping_address = $stmt->fetch(PDO::FETCH_ASSOC);

                $stmt = $pdo->prepare("
                    SELECT oi.quantity, oi.unit_price, oi.total_price,
                           p.title, pi.image_path, c.name AS color, s.label AS size
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.image_type = 'main'
                    LEFT JOIN colors c ON oi.color_id = c.id
                    LEFT JOIN sizes s ON oi.size_id = s.id
                    WHERE oi.order_id = ?
                ");
                $stmt->execute([$order['id']]);
                $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $error_message = 'Error fetching order: ' . htmlspecialchars($e->getMessage());
            $log_file = 'storage/error_log.txt';
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($log_file, "[$timestamp] Order tracking error: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }

    $tracking_statuses = [
        'pending' => [
            'icon' => 'fas fa-clock',
            'title' => 'Order Placed',
            'description' => 'Your order has been received and is being processed'
        ],
        'processing' => [
            'icon' => 'fas fa-cogs',
            'title' => 'Processing',
            'description' => 'Your order is being prepared for shipment'
        ],
        'shipped' => [
            'icon' => 'fas fa-truck',
            'title' => 'Shipped',
            'description' => 'Your order has been shipped and is on its way'
        ],
        'delivered' => [
            'icon' => 'fas fa-check-circle',
            'title' => 'Delivered',
            'description' => 'Your order has been successfully delivered'
        ],
        'cancelled' => [
            'icon' => 'fas fa-times-circle',
            'title' => 'Cancelled',
            'description' => 'Your order has been cancelled'
        ]
    ];

    $status_order = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    $current_status = $order['status'] ?? 'pending';
    $current_index = array_search($current_status, $status_order);
    ?>

    <div class="tracking-container" id="tracking-container">
        <!-- Header -->
        <div class="tracking-header">
            <h1 class="tracking-title">
                <i class="fas fa-shipping-fast me-2"></i>
                Track Your Order
            </h1>
            <p class="tracking-subtitle">Enter your order ID to track your package</p>
        </div>

        <!-- Content -->
        <div class="tracking-content">
            <!-- Order Search Form -->
            <div class="order-search" id="order-search">
                <form class="search-form" method="GET">
                    <input 
                        type="text" 
                        class="form-control" 
                        name="order_id" 
                        placeholder="Enter Order ID (e.g., ORD-20250525-4029)" 
                        value="<?php echo htmlspecialchars($order_id); ?>"
                        required
                    >
                    <button type="submit" class="btn-track">
                        <i class="fas fa-search me-2"></i>Track
                    </button>
                </form>
            </div>

            <!-- Loading Spinner -->
            <div class="loading-spinner" id="loading-spinner">
                <div class="spinner"></div>
                <p>Searching for your order...</p>
            </div>

            <?php if ($order_id && !$order && !$error_message): ?>
                <!-- Order Not Found -->
                <div class="error-state" id="error-state">
                    <div class="error-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="error-title">Order Not Found</h3>
                    <p class="error-message">
                        We couldn't find an order with ID: <strong><?php echo htmlspecialchars($order_id); ?></strong><br>
                        Please check your order ID and try again.
                    </p>
                    <a href="track-order.php" class="back-button">
                        <i class="fas fa-arrow-left me-2"></i>Try Again
                    </a>
                </div>

            <?php elseif ($error_message): ?>
                <!-- Error State -->
                <div class="error-state" id="error-state">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="error-title">Something Went Wrong</h3>
                    <p class="error-message"><?php echo $error_message; ?></p>
                    <a href="track-order.php" class="back-button">
                        <i class="fas fa-refresh me-2"></i>Try Again
                    </a>
                </div>

            <?php elseif ($order): ?>
                <!-- Order Found - Show Tracking -->
                <div class="tracking-results" id="tracking-results">
                    <!-- Order Information -->
                    <div class="order-info">
                        <div class="order-details">
                            <div class="detail-item">
                                <div class="detail-label">Order ID</div>
                                <div class="detail-value">#<?php echo htmlspecialchars($order['custom_order_id']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Customer</div>
                                <div class="detail-value"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Order Date</div>
                                <div class="detail-value"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Total Amount</div>
                                <div class="detail-value">৳<?php echo number_format($order['total'], 2); ?></div>
                            </div>
                        </div>
                        <?php if ($shipping_address): ?>
                            <div class="detail-item">
                                <div class="detail-label">Shipping Address</div>
                                <div class="detail-value">
                                    <?php echo htmlspecialchars(
                                        $shipping_address['street_address'] . ', ' .
                                        $shipping_address['city'] . ', ' .
                                        $shipping_address['upzila_name'] . ', ' .
                                        $shipping_address['district_name'] . ', ' .
                                        $shipping_address['division_name'] . ' ' .
                                        $shipping_address['postal_code']
                                    ); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Order Items -->
                    <?php if ($order_items): ?>
                        <div class="order-items">
                            <h4>Order Items</h4>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars('storage/' . $item['image_path'] ?? 'assets/images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($item['title']); ?>
                                                <?php if ($item['color']): ?> (<?php echo htmlspecialchars($item['color']); ?>)<?php endif; ?>
                                                <?php if ($item['size']): ?> (<?php echo htmlspecialchars($item['size']); ?>)<?php endif; ?>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>৳<?php echo number_format($item['unit_price'], 2); ?></td>
                                            <td>৳<?php echo number_format($item['total_price'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- Tracking Timeline -->
                    <div class="tracking-timeline">
                        <?php foreach ($status_order as $index => $status): ?>
                            <?php 
                            $is_completed = $index < $current_index || ($status === 'cancelled' && $current_status === 'cancelled');
                            $is_active = $index == $current_index && $current_status !== 'cancelled';
                            $is_pending = $index > $current_index && $current_status !== 'cancelled';
                            
                            $icon_class = '';
                            if ($is_completed) $icon_class = 'completed';
                            elseif ($is_active) $icon_class = 'active';
                            else $icon_class = 'pending';
                            
                            $status_info = $tracking_statuses[$status];
                            ?>
                            <div class="timeline-item" data-status="<?php echo $status; ?>">
                                <div class="timeline-icon <?php echo $icon_class; ?>">
                                    <i class="<?php echo $status_info['icon']; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title"><?php echo $status_info['title']; ?></div>
                                    <div class="timeline-description"><?php echo $status_info['description']; ?></div>
                                    <?php if ($is_completed || $is_active): ?>
                                        <div class="timeline-date">
                                            <?php 
                                            if ($status === 'pending') {
                                                echo date('M j, Y g:i A', strtotime($order['created_at']));
                                            } elseif ($status === 'cancelled') {
                                                echo date('M j, Y g:i A', strtotime($order['created_at'] . ' +1 day'));
                                            } else {
                                                $days_offset = $index;
                                                echo date('M j, Y g:i A', strtotime($order['created_at'] . " +{$days_offset} days"));
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($index < count($status_order) - 1): ?>
                                    <div class="timeline-line"></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Estimated Delivery -->
                    <?php if ($current_status !== 'delivered' && $current_status !== 'cancelled'): ?>
                        <div class="estimated-delivery">
                            <div class="delivery-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="delivery-title">Estimated Delivery</div>
                            <div class="delivery-date">
                                <?php echo date('F j, Y', strtotime($order['created_at'] . ' +7 days')); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Back Button -->
                    <div class="text-center mt-4">
                        <a href="order_confirmation.php?order_id=<?php echo htmlspecialchars($order['custom_order_id']); ?>" class="back-button">
                            <i class="fas fa-arrow-left me-2"></i>Back to Order Details
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hasOrder = <?php echo $order ? 'true' : 'false'; ?>;
            const hasError = <?php echo $error_message ? 'true' : 'false'; ?>;
            
    
            initializeAnimations();
            
            const form = document.querySelector('.search-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    showLoading();
                });
            }
            
            function initializeAnimations() {
          
                anime({
                    targets: '#tracking-container',
                    scale: [0.9, 1],
                    opacity: [0, 1],
                    duration: 800,
                    easing: 'easeOutExpo'
                });
                
                if (hasOrder) {
    
                    anime({
                        targets: '.order-info',
                        translateY: [30, 0],
                        opacity: [0, 1],
                        duration: 600,
                        delay: 300,
                        easing: 'easeOutExpo'
                    });
                    
        
                    anime({
                        targets: '.timeline-item',
                        translateX: [-50, 0],
                        opacity: [0, 1],
                        duration: 600,
                        delay: anime.stagger(200, {start: 500}),
                        easing: 'easeOutExpo'
                    });
               
                    const deliveryElement = document.querySelector('.estimated-delivery');
                    if (deliveryElement) {
                        anime({
                            targets: deliveryElement,
                            scale: [0.9, 1],
                            opacity: [0, 1],
                            duration: 600,
                            delay: 1000,
                            easing: 'easeOutExpo'
                        });
                    }
         
                    const orderItems = document.querySelector('.order-items');
                    if (orderItems) {
                        anime({
                            targets: orderItems,
                            translateY: [30, 0],
                            opacity: [0, 1],
                            duration: 600,
                            delay: 400,
                            easing: 'easeOutExpo'
                        });
                    }
                } else if (hasError || <?php echo $order_id && !$order ? 'true' : 'false'; ?>) {

                    anime({
                        targets: '#error-state',
                        translateY: [30, 0],
                        opacity: [0, 1],
                        duration: 600,
                        delay: 300,
                        easing: 'easeOutExpo'
                    });
                }
            }
            
            function showLoading() {
                const searchForm = document.getElementById('order-search');
                const loadingSpinner = document.getElementById('loading-spinner');
                const trackingResults = document.getElementById('tracking-results');
                const errorState = document.getElementById('error-state');
                

                if (searchForm) searchForm.style.display = 'none';
                if (trackingResults) trackingResults.style.display = 'none';
                if (errorState) errorState.style.display = 'none';
                
                loadingSpinner.style.display = 'block';
                
                anime({
                    targets: loadingSpinner,
                    opacity: [0, 1],
                    duration: 300,
                    easing: 'easeOutQuad'
                });
            }
            
            document.querySelectorAll('.btn-track, .back-button').forEach(button => {
                button.addEventListener('mouseenter', function() {
                    anime({
                        targets: this,
                        translateY: -2,
                        duration: 200,
                        easing: 'easeOutQuad'
                    });
                });
                
                button.addEventListener('mouseleave', function() {
                    anime({
                        targets: this,
                        translateY: 0,
                        duration: 200,
                        easing: 'easeOutQuad'
                    });
                });
            });
            
            document.querySelectorAll('.timeline-icon').forEach(icon => {
                icon.addEventListener('mouseenter', function() {
                    if (this.classList.contains('completed') || this.classList.contains('active')) {
                        anime({
                            targets: this,
                            scale: 1.1,
                            duration: 200,
                            easing: 'easeOutQuad'
                        });
                    }
                });
                
                icon.addEventListener('mouseleave', function() {
                    anime({
                        targets: this,
                        scale: 1,
                        duration: 200,
                        easing: 'easeOutQuad'
                    });
                });
            });
        });
    </script>
</body>
</html>