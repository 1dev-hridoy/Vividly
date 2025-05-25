<?php
session_start();
require_once 'server/dbcon.php';

$error_message = '';
$cart_items = [];
$subtotal = 0;
$item_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
     
        if (isset($_POST['add_to_cart'])) {
            $product_id = filter_var($_POST['product_id'] ?? 0, FILTER_VALIDATE_INT);
            $color_id = filter_var($_POST['color_id'] ?? 0, FILTER_VALIDATE_INT);
            $size_id = filter_var($_POST['size_id'] ?? 0, FILTER_VALIDATE_INT);
            $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT);

            if ($product_id && $quantity > 0) {
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                $cart_key = "{$product_id}_{$color_id}_{$size_id}";
                if (isset($_SESSION['cart'][$cart_key])) {
                    $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$cart_key] = [
                        'product_id' => $product_id,
                        'color_id' => $color_id,
                        'size_id' => $size_id,
                        'quantity' => $quantity
                    ];
                }
                header('Location: cart.php?success=' . urlencode('Product added to cart!'));
                exit;
            } else {
                $error_message = 'Invalid cart data.';
            }
        }

   
        if (isset($_POST['update_quantity'])) {
            $cart_key = filter_var($_POST['cart_key'] ?? '', FILTER_SANITIZE_STRING);
            $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT);
            if ($cart_key && isset($_SESSION['cart'][$cart_key]) && $quantity > 0) {
                $_SESSION['cart'][$cart_key]['quantity'] = $quantity;
                header('Location: cart.php?success=' . urlencode('Quantity updated!'));
                exit;
            } else {
                $error_message = 'Invalid quantity update.';
            }
        }

        if (isset($_POST['remove_item'])) {
            $cart_key = filter_var($_POST['cart_key'] ?? '', FILTER_SANITIZE_STRING);
            if ($cart_key && isset($_SESSION['cart'][$cart_key])) {
                unset($_SESSION['cart'][$cart_key]);
                header('Location: cart.php?success=' . urlencode('Item removed!'));
                exit;
            } else {
                $error_message = 'Invalid item removal.';
            }
        }

    
        if (isset($_POST['apply_promo'])) {
            $promo_code = filter_var($_POST['promo_code'] ?? '', FILTER_SANITIZE_STRING);
            if (strtolower($promo_code) === 'save10') {
                $_SESSION['discount'] = 0.10;
                header('Location: cart.php?success=' . urlencode('Promo code applied!'));
                exit;
            } else {
                $error_message = 'Invalid promo code.';
            }
        }
    } catch (Exception $e) {
        $error_message = 'Error processing cart action: ' . htmlspecialchars($e->getMessage());
        $log_file = '../storage/error_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] Cart error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    try {
        foreach ($_SESSION['cart'] as $cart_key => $item) {
            $stmt = $pdo->prepare("
                SELECT p.id, p.title, p.price, pi.image_path, c.name AS color, s.label AS size
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.image_type = 'main'
                LEFT JOIN colors c ON c.id = ?
                LEFT JOIN sizes s ON s.id = ?
                WHERE p.id = ?
            ");
            $stmt->execute([$item['color_id'], $item['size_id'], $item['product_id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $product['quantity'] = $item['quantity'];
                $product['cart_key'] = $cart_key;
                $cart_items[] = $product;
                $subtotal += $product['price'] * $item['quantity'];
                $item_count += $item['quantity'];
            }
        }
    } catch (Exception $e) {
        $error_message = 'Error fetching cart items: ' . htmlspecialchars($e->getMessage());
        $log_file = '../storage/error_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] Failed to fetch cart items: " . $e->getMessage() . "\n", FILE_APPEND);
        $cart_items = [];
    }
}


$tax = $subtotal * 0.08;
$discount = isset($_SESSION['discount']) ? $subtotal * $_SESSION['discount'] : 0;
$total = $subtotal + $tax - $discount;

require_once './includes/__header__.php';
?>

<link rel="stylesheet" href="assets/css/ecom/cart.css">

<!-- Breadcrumb -->
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
        </ol>
    </nav>
</div>

<!-- Cart Section -->
<section class="cart-section">
    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success text-center"><?php echo htmlspecialchars(urldecode($_GET['success'])); ?></div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="cart-items">
                    <?php if (empty($cart_items)): ?>
                        <div class="empty-cart">
                            <div class="empty-cart-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <h2 class="empty-cart-title">Your cart is empty</h2>
                            <p class="empty-cart-text">Looks like you haven't added anything to your cart yet.</p>
                            <a href="index.php" class="btn btn-minimal">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($cart_items as $index => $item): ?>
                            <div class="cart-item" data-sr-id="<?php echo $index + 1; ?>">
                                <div class="cart-item-img">
                                    <img src="<?php echo $item['image_path'] ? htmlspecialchars('storage/' . $item['image_path']) : 'assets/img/default_product.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>">
                                </div>
                                <div class="cart-item-details">
                                    <h3 class="cart-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <div class="cart-item-options">
                                        Color: <?php echo htmlspecialchars($item['color'] ?? 'N/A'); ?> | 
                                        Size: <?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?>
                                    </div>
                                    <div class="cart-item-price">$<?php echo number_format($item['price'], 2); ?></div>
                                </div>
                                <div class="cart-item-actions">
                                    <form method="POST" class="quantity-selector">
                                        <input type="hidden" name="cart_key" value="<?php echo htmlspecialchars($item['cart_key']); ?>">
                                        <button type="button" class="quantity-btn decrease-qty">-</button>
                                        <input type="text" class="quantity-input" name="quantity" 
                                               value="<?php echo $item['quantity']; ?>" readonly>
                                        <button type="button" class="quantity-btn increase-qty">+</button>
                                        <input type="hidden" name="update_quantity" value="1">
                                    </form>
                                    <form method="POST">
                                        <input type="hidden" name="cart_key" value="<?php echo htmlspecialchars($item['cart_key']); ?>">
                                        <button type="submit" name="remove_item" class="remove-btn" title="Remove item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Continue Shopping -->
                <div class="mt-4">
                    <a href="index.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="cart-summary" data-sr-id="3">
                    <h3 class="summary-title">Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal (<?php echo $item_count; ?> items)</span>
                        <span id="subtotal">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax</span>
                        <span id="tax">$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    
                    <?php if ($discount > 0): ?>
                        <div class="summary-row discount-row">
                            <span>Discount (SAVE10)</span>
                            <span style="color: #28a745;">-<?php echo number_format($discount, 2); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="total">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <div class="mt-4">
                       <a href="checkout.php"> <button class="btn btn-minimal">Proceed to Checkout</button></a>
                    </div>
                    
                    <!-- Promo Code -->
                    <div class="mt-3">
                        <form method="POST" class="input-group">
                            <input type="text" name="promo_code" class="form-control" placeholder="Promo code">
                            <button type="submit" name="apply_promo" class="btn btn-outline-secondary">Apply</button>
                        </form>
                    </div>
                    
                    <!-- Payment Icons -->
                    <div class="mt-4 text-center">
                        <small class="text-muted">We accept</small>
                        <div class="mt-2">
                            <i class="fab fa-cc-visa fa-2x me-2 text-muted"></i>
                            <i class="fab fa-cc-mastercard fa-2x me-2 text-muted"></i>
                            <i class="fab fa-cc-paypal fa-2x me-2 text-muted"></i>
                            <i class="fab fa-cc-apple-pay fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
   
    const sr = ScrollReveal({
        origin: 'bottom',
        distance: '20px',
        duration: 800,
        delay: 100,
        easing: 'ease',
        reset: false
    });
    sr.reveal('.cart-item', { interval: 150 });
    sr.reveal('.cart-summary', { origin: 'right', distance: '30px' });
    document.addEventListener('DOMContentLoaded', function() {
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach(item => {
            const decreaseBtn = item.querySelector('.decrease-qty');
            const increaseBtn = item.querySelector('.increase-qty');
            const quantityInput = item.querySelector('.quantity-input');
            const quantityForm = item.querySelector('.quantity-selector');
            
   
            decreaseBtn.addEventListener('click', function() {
                let quantity = parseInt(quantityInput.value);
                if (quantity > 1) {
                    quantity--;
                    quantityInput.value = quantity;
                    updateCartTotals();
                    
              
                    quantityForm.submit();
                    
       
                    anime({
                        targets: quantityInput,
                        scale: [1, 1.1, 1],
                        duration: 300,
                        easing: 'easeInOutQuad'
                    });
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                let quantity = parseInt(quantityInput.value);
                quantity++;
                quantityInput.value = quantity;
                updateCartTotals();
                
        
                quantityForm.submit();
                
        
                anime({
                    targets: quantityInput,
                    scale: [1, 1.1, 1],
                    duration: 300,
                    easing: 'easeInOutQuad'
                });
            });
        });
        

        function updateCartTotals() {
            const items = document.querySelectorAll('.cart-item');
            let subtotal = 0;
            let itemCount = 0;
            
            items.forEach(item => {
                const priceText = item.querySelector('.cart-item-price').textContent;
                const price = parseFloat(priceText.replace('$', ''));
                const quantity = parseInt(item.querySelector('.quantity-input').value);
                
                subtotal += price * quantity;
                itemCount += quantity;
            });
            
            const tax = subtotal * 0.08;
            const discount = <?php echo $discount; ?>;
            const total = subtotal + tax - discount;
      
            document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
            document.getElementById('total').textContent = `$${total.toFixed(2)}`;
            
        
            const cartBadge = document.querySelector('.badge');
            if (cartBadge) {
                cartBadge.textContent = itemCount;
            }
            
         
            const summaryTitle = document.querySelector('.summary-title');
            summaryTitle.textContent = `Order Summary`;
            
     
            const subtotalLabel = document.querySelector('.summary-row span');
            subtotalLabel.textContent = `Subtotal (${itemCount} items)`;
        }
        
  
        function checkEmptyCart() {
            const cartItems = document.querySelectorAll('.cart-item');
            const cartContainer = document.querySelector('.cart-items');
            
            if (cartItems.length === 0) {
                cartContainer.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h2 class="empty-cart-title">Your cart is empty</h2>
                        <p class="empty-cart-text">Looks like you haven't added anything to your cart yet.</p>
                        <a href="index.php" class="btn btn-minimal">Start Shopping</a>
                    </div>
                `;
  
                document.querySelector('.cart-summary').style.display = 'none';
            }
        }
        
        updateCartTotals();
    });
</script>

<?php require_once './includes/__footer__.php'; ?>