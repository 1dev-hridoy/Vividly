<?php
ob_start(); 
session_start();
require_once 'server/dbcon.php';

$error_message = '';
$success_message = '';
$cart_items = [];
$subtotal = 0;
$item_count = 0;
$shipping_cost = 0; 
$tax_rate = 0.08;
$discount = isset($_SESSION['discount']) ? $_SESSION['discount'] : 0;


$css_path = 'assets/css/ecom/checkout.css';
if (!file_exists(__DIR__ . '/' . $css_path)) {
    $error_message = 'Error: checkout.css not found at ' . $css_path;
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] CSS error: checkout.css not found\n", FILE_APPEND);
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
        file_put_contents($log_file, "[$timestamp] Checkout error: " . $e->getMessage() . "\n", FILE_APPEND);
        $cart_items = [];
    }
} else {
    $error_message = 'Your cart is empty. Please add items to proceed.';
}

$tax = $subtotal * $tax_rate;
$discount_amount = $subtotal * $discount;
$total = $subtotal + $tax + $shipping_cost - $discount_amount;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    try {
        $pdo->beginTransaction();

      
        $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''));
        $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''));
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
        $street_address = htmlspecialchars(trim($_POST['address'] ?? ''));
        $city = htmlspecialchars(trim($_POST['city'] ?? ''));
        $postal_code = htmlspecialchars(trim($_POST['zip'] ?? ''));
        $division_id = filter_var($_POST['division'] ?? '', FILTER_VALIDATE_INT);
        $division_name = htmlspecialchars(trim($_POST['division_name'] ?? ''));
        $district_id = filter_var($_POST['district'] ?? '', FILTER_VALIDATE_INT);
        $district_name = htmlspecialchars(trim($_POST['district_name'] ?? ''));
        $upzila_id = filter_var($_POST['upzila'] ?? '', FILTER_VALIDATE_INT);
        $upzila_name = htmlspecialchars(trim($_POST['upzila_name'] ?? ''));
        $union_id = filter_var($_POST['union'] ?? '', FILTER_VALIDATE_INT);
        $union_name = htmlspecialchars(trim($_POST['union_name'] ?? ''));
        $save_address = isset($_POST['save_address']) ? 1 : 0;
        $payment_method = htmlspecialchars(trim($_POST['payment'] ?? ''));
        $billing_same = isset($_POST['billing_address']) ? 1 : 0;

     
        $mobile_number = htmlspecialchars(trim($_POST['mobile_number'] ?? ''));
        $transaction_id = htmlspecialchars(trim($_POST['transaction_id'] ?? ''));

       
        if (!$first_name || !$last_name || !$email || !$phone || !$street_address || !$city || !$postal_code || 
            !$division_id || !$district_id || !$upzila_id || !$union_id || !$payment_method) {
            throw new Exception('All required fields must be filled.');
        }
        if (in_array($payment_method, ['bkash', 'nagad', 'rocket']) && (!$mobile_number || !$transaction_id)) {
            throw new Exception('Mobile number and transaction ID are required for ' . ucfirst($payment_method) . ' payment.');
        }
        if (empty($cart_items)) {
            throw new Exception('Cart is empty. Please add items to proceed.');
        }

       
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, phone)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE first_name = ?, last_name = ?, phone = ?
        ");
        $stmt->execute([$first_name, $last_name, $email, $phone, $first_name, $last_name, $phone]);
        $user_id = $pdo->lastInsertId() ?: $pdo->query("SELECT id FROM users WHERE email = '$email'")->fetchColumn();

       
        $stmt = $pdo->prepare("
            INSERT INTO addresses (user_id, address_type, street_address, division_id, division_name, district_id, district_name, 
                upzila_id, upzila_name, union_id, union_name, city, postal_code, save_for_future)
            VALUES (?, 'shipping', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $street_address, $division_id, $division_name, $district_id, $district_name, 
                        $upzila_id, $upzila_name, $union_id, $union_name, $city, $postal_code, $save_address]);
        $shipping_address_id = $pdo->lastInsertId();

     
        $billing_address_id = $billing_same ? $shipping_address_id : null;
        if (!$billing_same) {
            $stmt = $pdo->prepare("
                INSERT INTO addresses (user_id, address_type, street_address, division_id, division_name, district_id, district_name, 
                    upzila_id, upzila_name, union_id, union_name, city, postal_code, save_for_future)
                VALUES (?, 'billing', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $street_address, $division_id, $division_name, $district_id, $district_name, 
                            $upzila_id, $upzila_name, $union_id, $union_name, $city, $postal_code, $save_address]);
            $billing_address_id = $pdo->lastInsertId();
        }

     
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $custom_order_id = "ORD-$date-$random";

     
        $stmt = $pdo->prepare("
            INSERT INTO orders (custom_order_id, user_id, shipping_address_id, billing_address_id, subtotal, shipping_cost, tax, discount, total, 
                payment_method, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$custom_order_id, $user_id, $shipping_address_id, $billing_address_id, $subtotal, $shipping_cost, $tax, 
                        $discount_amount, $total, $payment_method]);
        $order_id = $pdo->lastInsertId();

       
        foreach ($cart_items as $item) {
            $total_price = $item['price'] * $item['quantity'];
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, color_id, size_id, quantity, unit_price, total_price)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$order_id, $item['id'], $_SESSION['cart'][$item['cart_key']]['color_id'], 
                            $_SESSION['cart'][$item['cart_key']]['size_id'], $item['quantity'], $item['price'], $total_price]);
        }

      
        $stmt = $pdo->prepare("
            INSERT INTO payments (order_id, payment_method, transaction_id, mobile_number, amount, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$order_id, $payment_method, $transaction_id, $mobile_number, $total]);

       
        unset($_SESSION['cart']);
        unset($_SESSION['discount']);

        $pdo->commit();
        ob_end_clean(); 
        header('Location: success.php?order_id=' . urlencode($custom_order_id));
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = 'Error placing order: ' . htmlspecialchars($e->getMessage());
        $log_file = '../storage/error_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] Order placement error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

require_once './includes/__header__.php';
?>

<link rel="stylesheet" href="<?php echo htmlspecialchars($css_path); ?>">

<!-- Breadcrumb -->
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>
</div>

<!-- Checkout Section -->
<section class="checkout-section">
    <div class="container">
        <h1 class="page-title">Checkout</h1>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <!-- Progress Steps -->
        <div class="checkout-progress" data-sr-id="1">
            <div class="progress-steps">
                <div class="progress-step completed">1</div>
                <div class="progress-step active">2</div>
                <div class="progress-step">3</div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="step-label">Cart</div>
                </div>
                <div class="col-4">
                    <div class="step-label">Checkout</div>
                </div>
                <div class="col-4">
                    <div class="step-label">Complete</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <form class="checkout-form" method="POST">
                    <!-- Shipping Information -->
                    <div class="form-section" data-sr-id="2">
                        <h3 class="section-title">Shipping Information</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Street Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="division" class="form-label">Division</label>
                                <select class="form-control" id="division" name="division" required>
                                    <option value="">-- Select Division --</option>
                                </select>
                                <input type="hidden" id="division_name" name="division_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="district" class="form-label">District</label>
                                <select class="form-control" id="district" name="district" disabled required>
                                    <option value="">-- Select District --</option>
                                </select>
                                <input type="hidden" id="district_name" name="district_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="upzila" class="form-label">Upzila</label>
                                <select class="form-control" id="upzila" name="upzila" disabled required>
                                    <option value="">-- Select Upzila --</option>
                                </select>
                                <input type="hidden" id="upzila_name" name="upzila_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="union" class="form-label">Union</label>
                                <select class="form-control" id="union" name="union" disabled required>
                                    <option value="">-- Select Union --</option>
                                </select>
                                <input type="hidden" id="union_name" name="union_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="zip" class="form-label">ZIP Code</label>
                                <input type="text" class="form-control" id="zip" name="zip" required>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="saveAddress" name="save_address">
                            <label class="form-check-label" for="saveAddress">
                                Save this address for future orders
                            </label>
                        </div>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="form-section" data-sr-id="3">
                        <h3 class="section-title">Payment Information</h3>
                        
                        <!-- Payment Methods -->
                        <div class="mb-4">
                            <div class="payment-method active">
                                <div class="d-flex align-items-center">
                                    <input type="radio" name="payment" value="bkash" checked>
                                    <span>Bkash</span>
                                    <div class="payment-icons">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-method">
                                <div class="d-flex align-items-center">
                                    <input type="radio" name="payment" value="nagad">
                                    <span>Nagad</span>
                                    <div class="payment-icons">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-method">
                                <div class="d-flex align-items-center">
                                    <input type="radio" name="payment" value="rocket">
                                    <span>Rocket</span>
                                    <div class="payment-icons">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile Payment Details -->
                        <div id="mobile-payment-details">
                            <div class="alert alert-info">
                                Please send the payment to the <span id="payment-method-name">Bkash</span> number: 
                                <strong id="payment-number">+8801234567890</strong> and provide the transaction ID below.
                            </div>
                            <div class="mb-3">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobileNumber" name="mobile_number" placeholder="e.g., +8801234567890" required>
                            </div>
                            <div class="mb-3">
                                <label for="transactionId" class="form-label">Transaction ID</label>
                                <input type="text" class="form-control" id="transactionId" name="transaction_id" placeholder="e.g., TX1234567890" required>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="billingAddress" name="billing_address" checked>
                            <label class="form-check-label" for="billingAddress">
                                Billing address is the same as shipping address
                            </label>
                        </div>
                    </div>
                    
                    <input type="hidden" name="place_order" value="1">
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary" data-sr-id="4">
                    <h3 class="summary-title">Order Summary</h3>
                    
                    <!-- Order Items -->
                    <?php if (empty($cart_items)): ?>
                        <div class="alert alert-warning">Your cart is empty.</div>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                <div class="order-item-img">
                                    <img src="<?php echo $item['image_path'] ? htmlspecialchars('storage/' . $item['image_path']) : 'assets/img/default_product.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>">
                                </div>
                                <div class="order-item-details">
                                    <div class="order-item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                    <div class="order-item-options">
                                        <?php echo htmlspecialchars($item['color'] ?? 'N/A'); ?>, 
                                        <?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?> × <?php echo $item['quantity']; ?>
                                    </div>
                                </div>
                                <div class="order-item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Summary Totals -->
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span id="shipping-cost">Free</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax</span>
                        <span id="tax">$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    
                    <?php if ($discount_amount > 0): ?>
                        <div class="summary-row">
                            <span>Discount (SAVE10)</span>
                            <span style="color: #28a745;">-$<?php echo number_format($discount_amount, 2); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="total-cost">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <!-- Place Order Button -->
                    <button type="submit" class="btn btn-minimal" id="place-order" form="checkout-form">
                        Place Order
                    </button>
                    
                    <!-- Security Badge -->
                    <div class="security-badge">
                        <i class="fas fa-lock"></i>
                        <small>Your payment information is secure and encrypted</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once './includes/__footer__.php'; ?>
<script>
    const sr = ScrollReveal({
        origin: 'bottom',
        distance: '20px',
        duration: 800,
        delay: 100,
        easing: 'ease',
        reset: false
    });
    
    sr.reveal('.checkout-progress', { origin: 'top', distance: '30px' });
    sr.reveal('.form-section', { interval: 200 });
    sr.reveal('.order-summary', { origin: 'right', distance: '30px' });
    document.addEventListener('DOMContentLoaded', function() {
     
        const cssLoaded = document.querySelector('link[href="<?php echo htmlspecialchars($css_path); ?>"]');
        if (!cssLoaded) {
            console.error('checkout.css failed to load');
        }
        async function loadDivisions() {
            try {
                const response = await fetch('https://sohojapi.vercel.app/api/divisions');
                const divisions = await response.json();
                const divisionSelect = document.getElementById('division');
                divisions.forEach(division => {
                    const option = document.createElement('option');
                    option.value = division.id;
                    option.textContent = division.bn_name; 
                    divisionSelect.appendChild(option);
                });
            } catch (error) {
                console.error("Error loading divisions:", error);
            }
        }

        document.getElementById('division').addEventListener('change', async (e) => {
            const divisionId = e.target.value;
            const divisionName = e.target.selectedOptions[0]?.text || '';
            document.getElementById('division_name').value = divisionName;
            const districtSelect = document.getElementById('district');
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';
            document.getElementById('upzila').innerHTML = '<option value="">-- Select Upzila --</option>';
            document.getElementById('union').innerHTML = '<option value="">-- Select Union --</option>';
            document.getElementById('upzila').disabled = true;
            document.getElementById('union').disabled = true;

            if (divisionId) {
                try {
                    const response = await fetch(`https://sohojapi.vercel.app/api/districts/${divisionId}`);
                    const districts = await response.json();
                    districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.bn_name;
                        districtSelect.appendChild(option);
                    });
                    districtSelect.disabled = false;
                } catch (error) {
                    console.error("Error loading districts:", error);
                }
            }
        });

        document.getElementById('district').addEventListener('change', async (e) => {
            const districtId = e.target.value;
            const districtName = e.target.selectedOptions[0]?.text || '';
            document.getElementById('district_name').value = districtName;
            const upzilaSelect = document.getElementById('upzila');
            upzilaSelect.innerHTML = '<option value="">-- Select Upzila --</option>';
            document.getElementById('union').innerHTML = '<option value="">-- Select Union --</option>';
            document.getElementById('union').disabled = true;

            if (districtId) {
                try {
                    const response = await fetch(`https://sohojapi.vercel.app/api/upzilas/${districtId}`);
                    const upzilas = await response.json();
                    upzilas.forEach(upzila => {
                        const option = document.createElement('option');
                        option.value = upzila.id;
                        option.textContent = upzila.bn_name;
                        upzilaSelect.appendChild(option);
                    });
                    upzilaSelect.disabled = false;
                } catch (error) {
                    console.error("Error loading upzilas:", error);
                }
            }
        });

        document.getElementById('upzila').addEventListener('change', async (e) => {
            const upzilaId = e.target.value;
            const upzilaName = e.target.selectedOptions[0]?.text || '';
            document.getElementById('upzila_name').value = upzilaName;
            const unionSelect = document.getElementById('union');
            unionSelect.innerHTML = '<option value="">-- Select Union --</option>';

            if (upzilaId) {
                try {
                    const response = await fetch(`https://sohojapi.vercel.app/api/unions/${upzilaId}`);
                    const unions = await response.json();
                    unions.forEach(union => {
                        const option = document.createElement('option');
                        option.value = union.id;
                        option.textContent = union.bn_name;
                        unionSelect.appendChild(option);
                    });
                    unionSelect.disabled = false;
                } catch (error) {
                    console.error("Error loading unions:", error);
                }
            }
        });

        document.getElementById('union').addEventListener('change', (e) => {
            const unionName = e.target.selectedOptions[0]?.text || '';
            document.getElementById('union_name').value = unionName;
        });

        loadDivisions();


        const paymentMethods = document.querySelectorAll('input[name="payment"]');
        const mobilePaymentDetails = document.getElementById('mobile-payment-details');
        const paymentMethodName = document.getElementById('payment-method-name');
        const paymentNumber = document.getElementById('payment-number');

        const paymentNumbers = {
            bkash: '+8801234567890',
            nagad: '+8809876543210',
            rocket: '+8805556667778'
        };

        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                document.querySelectorAll('.payment-method').forEach(pm => {
                    if (pm.querySelector('input[name="payment"]')) {
                        pm.classList.remove('active');
                    }
                });
                this.closest('.payment-method').classList.add('active');

                mobilePaymentDetails.style.display = 'block';
                paymentMethodName.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                paymentNumber.textContent = paymentNumbers[this.value];
                anime({
                    targets: mobilePaymentDetails,
                    opacity: [0, 1],
                    translateY: [-20, 0],
                    duration: 500,
                    easing: 'easeOutExpo'
                });
            });
        });

   
        const form = document.querySelector('.checkout-form');
        const placeOrderBtn = document.getElementById('place-order');

        placeOrderBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ddd';
                }
            });

            if (isValid) {
                anime({
                    targets: placeOrderBtn,
                    scale: [1, 0.95, 1],
                    duration: 300,
                    easing: 'easeInOutQuad'
                });
                placeOrderBtn.textContent = 'Processing...';
                placeOrderBtn.disabled = true;
                form.submit();
            } else {
                anime({
                    targets: form,
                    translateX: [-10, 10, -10, 10, 0],
                    duration: 500,
                    easing: 'easeInOutQuad'
                });
            }
        });
    });
</script>
<?php ob_end_flush(); 