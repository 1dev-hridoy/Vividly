  <title>Order Complete</title>
    <link rel="stylesheet" href="assets/css/ecom/success.css">
    <?php
    require_once 'server/dbcon.php';

    $order_id = isset($_GET['order_id']) ? htmlspecialchars(trim($_GET['order_id'])) : '';

    $order = null;
    $error_message = null;

    if ($order_id) {
        try {
            $stmt = $pdo->prepare("
                SELECT o.custom_order_id, o.total, o.created_at, u.email
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.custom_order_id = ?
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $error_message = 'Error fetching order: ' . htmlspecialchars($e->getMessage());
            $log_file = '../storage/error_log.txt';
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($log_file, "[$timestamp] Order success error: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
    require_once './includes/__header__.php';
    ?>

    <section class="order-complete-section">
        <div class="container">
            <?php if ($order): ?>
                <div class="floating-element confetti-1">
                    <i class="fas fa-star"></i>
                </div>
                <div class="floating-element confetti-2">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="floating-element confetti-3">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="floating-element confetti-4">
                    <i class="fas fa-sparkles"></i>
                </div>
                
                <div class="success-container">
                    <!-- Success Animation -->
                    <div class="success-animation">
                        <div class="success-circle" id="success-circle">
                            <i class="fas fa-check success-icon" id="success-icon"></i>
                        </div>
                    </div>
                    
                    <!-- Success Message -->
                    <h1 class="success-title" id="success-title">Order Complete!</h1>
                    <p class="success-subtitle" id="success-subtitle">Thank you for your purchase. Your order has been successfully placed.</p>
                    
                    <!-- Order Details -->
                    <div class="order-details" id="order-details">
                        <div class="order-id" id="order-id" title="Click to copy">
                            Order #<?php echo htmlspecialchars($order['custom_order_id']); ?>
                        </div>
                        
                        <div class="order-info">
                            <span class="info-label">Order Date:</span>
                            <span class="info-value"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        
                        <div class="order-info">
                            <span class="info-label">Total Amount:</span>
                            <span class="info-value">$<?php echo number_format($order['total'], 2); ?></span>
                        </div>
                        
                        <div class="order-info">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['email']); ?></span>
                        </div>
                        
                        <div class="order-info">
                            <span class="info-label">Estimated Delivery:</span>
                            <span class="info-value"><?php echo date('F j, Y', strtotime($order['created_at'] . ' +7 days')); ?></span>
                        </div>
                    </div>
                    
                    <!-- Email Confirmation -->
                    <div class="email-confirmation" id="email-confirmation">
                        <i class="fas fa-envelope"></i>
                        <strong>Confirmation email sent!</strong> Check your inbox at <strong><?php echo htmlspecialchars($order['email']); ?></strong> for order details and tracking information.
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons" id="action-buttons">
                        <button class="btn btn-minimal" id="track-order">
                            <i class="fas fa-truck me-2"></i>Track Your Order
                        </button>
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-home me-2"></i>Continue Shopping
                        </a>
                    </div>
                    
                    <!-- Next Steps -->
                    <div class="next-steps" id="next-steps">
                        <h4>What happens next?</h4>
                        
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h5>Order Processing</h5>
                                <p>We'll prepare your items for shipment within 1-2 business days.</p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h5>Shipping Notification</h5>
                                <p>You'll receive an email with tracking information once your order ships.</p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h5>Delivery</h5>
                                <p>Your order will arrive at your doorstep within 5-7 business days.</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Error State -->
                <div class="error-container">
                    <!-- Error Animation -->
                    <div class="success-animation">
                        <div class="error-circle" id="error-circle">
                            <i class="fas fa-times error-icon" id="error-icon"></i>
                        </div>
                    </div>
                    
                    <!-- Error Message -->
                    <h1 class="error-title" id="error-title">Order Not Found</h1>
                    <p class="error-subtitle" id="error-subtitle">
                        <?php if ($error_message): ?>
                            <?php echo $error_message; ?>
                        <?php else: ?>
                            Invalid order ID or order not found. Please check your order ID and try again.
                        <?php endif; ?>
                    </p>
                    
                    <!-- Error Alert -->
                    <div class="error-alert" id="error-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Unable to retrieve order information.</strong> If you believe this is an error, please contact our support team.
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons" id="error-actions">
                        <a href="index.php" class="btn btn-minimal">
                            <i class="fas fa-home me-2"></i>Return to Home
                        </a>
                        <a href="contact.php" class="btn btn-outline">
                            <i class="fas fa-envelope me-2"></i>Contact Support
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php require_once './includes/__footer__.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const hasOrder = <?php echo $order ? 'true' : 'false'; ?>;
    
    if (hasOrder) {

        startSuccessAnimation();
    } else {
    
        startErrorAnimation();
    }
    
    function startSuccessAnimation() {
        const timeline = anime.timeline({
            easing: 'easeOutExpo',
            duration: 750
        });
        


             anime.set([
            '#success-circle',
            '#success-title',
            '#success-subtitle',
            '#order-details',
            '#email-confirmation',
            '#action-buttons',
            '#next-steps'
        ], {
            opacity: 0,
            translateY: 30
        });
        timeline.add({
            targets: '#success-circle',
            scale: [0, 1.2, 1],
            opacity: [0, 1],
            duration: 1000,
            complete: function() {
                anime({
                    targets: '#success-icon',
                    scale: [0, 1.3, 1],
                    opacity: [0, 1],
                    duration: 500,
                    delay: 200,
                    easing: 'easeOutBounce'
                });
            }
        })
        

        
        .add({
            targets: '#success-title',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=400')
        
 
        
        .add({
            targets: '#success-subtitle',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=300')
        
  
        
        .add({
            targets: '#order-details',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=200')
        
  
        
        .add({
            targets: '#email-confirmation',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=100')
        

        
        .add({
            targets: '#action-buttons',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=100')
        

        
        .add({
            targets: '#next-steps',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=100');
        

        
        setTimeout(() => {
            animateConfetti();
        }, 1500);
    }
    
 
    function startErrorAnimation() {
        const timeline = anime.timeline({
            easing: 'easeOutExpo',
            duration: 750
        });
        

        anime.set([
            '#error-circle',
            '#error-title',
            '#error-subtitle',
            '#error-alert',
            '#error-actions'
        ], {
            opacity: 0,
            translateY: 30
        });
        
    
        timeline.add({
            targets: '#error-circle',
            scale: [0, 1.2, 1],
            opacity: [0, 1],
            duration: 1000,
            complete: function() {
          
                anime({
                    targets: '#error-icon',
                    scale: [0, 1.3, 1],
                    opacity: [0, 1],
                    duration: 500,
                    delay: 200,
                    easing: 'easeOutBounce'
                });
            }
        })
        
 
        .add({
            targets: '#error-title',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=400')
        
    
        .add({
            targets: '#error-subtitle',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=300')
        
   
        .add({
            targets: '#error-alert',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=200')
        
   
        .add({
            targets: '#error-actions',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=100');
    }
    
 
    function animateConfetti() {
        const confettiElements = document.querySelectorAll('.floating-element');
        
        confettiElements.forEach((element, index) => {
            anime({
                targets: element,
                opacity: [0, 0.3, 0],
                translateY: [0, -50, -100],
                translateX: () => anime.random(-30, 30),
                rotate: () => anime.random(-180, 180),
                scale: [0.5, 1, 0.5],
                duration: 3000,
                delay: index * 200,
                easing: 'easeOutExpo',
                loop: true
            });
        });
    }
    
 
    const trackOrderBtn = document.getElementById('track-order');
    if (trackOrderBtn) {
        trackOrderBtn.addEventListener('click', function() {
          
            anime({
                targets: this,
                scale: [1, 0.95, 1],
                duration: 200,
                easing: 'easeInOutQuad'
            });
            
          
            const orderId = '<?php echo htmlspecialchars($order['custom_order_id'] ?? ''); ?>';
            setTimeout(() => {
                window.location.href = `track-order.php?order_id=${orderId}`;
            }, 300);
        });
    }
    

    document.querySelectorAll('.btn-minimal, .btn-outline').forEach(button => {
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
    
   
    const orderIdElement = document.getElementById('order-id');
    if (orderIdElement) {
        orderIdElement.addEventListener('click', function() {
            const orderId = this.textContent;
            
          
            navigator.clipboard.writeText(orderId).then(() => {
               
                const originalText = this.textContent;
                const originalBg = this.style.backgroundColor;
                const originalColor = this.style.color;
                
                this.textContent = 'Copied to clipboard!';
                this.style.backgroundColor = '#28a745';
                this.style.color = 'white';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.backgroundColor = originalBg;
                    this.style.color = originalColor;
                }, 2000);
            }).catch(() => {
        
                const textArea = document.createElement('textarea');
                textArea.value = orderId;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                setTimeout(() => {
                    this.textContent = originalText;
                }, 2000);
            });
        });
    }
});
</script>