<?php
session_start();
require_once 'server/dbcon.php';
$error_message = '';
$product = null;
$images = [];
$colors = [];
$sizes = [];
$related_products = [];
$product_id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$product_id) {
    $error_message = 'Invalid product ID.';
}

try {
    
    

    $stmt = $pdo->prepare("
        SELECT p.id, p.title, p.price, p.short_description, p.long_description, p.category_id, c.name AS category_name
        FROM products p
        JOIN category c ON p.category_id = c.id
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        $error_message = 'Product not found.';
    } else {
        $stmt = $pdo->prepare("
            SELECT image_path, image_type
            FROM product_images
            WHERE product_id = ?
            ORDER BY image_type DESC
        ");
        $stmt->execute([$product_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
        $stmt = $pdo->prepare("
            SELECT c.id, c.name
            FROM colors c
            JOIN product_colors pc ON c.id = pc.color_id
            WHERE pc.product_id = ?
        ");
        $stmt->execute([$product_id]);
        $colors = $stmt->fetchAll(PDO::FETCH_ASSOC);

      
        $stmt = $pdo->prepare("
            SELECT s.id, s.label
            FROM sizes s
            JOIN product_sizes ps ON s.id = ps.size_id
            WHERE ps.product_id = ?
            ORDER BY s.label
        ");
        $stmt->execute([$product_id]);
        $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        $stmt = $pdo->prepare("
            SELECT p.id, p.title, p.price, pi.image_path
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.image_type = 'main'
            WHERE p.category_id = ? AND p.id != ?
            ORDER BY p.created_at DESC
            LIMIT 4
        ");
        $stmt->execute([$product['category_id'], $product_id]);
        $related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $error_message = 'Error loading product: ' . htmlspecialchars($e->getMessage());
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Failed to load product (ID: $product_id): " . $e->getMessage() . "\n", FILE_APPEND);
}

require_once './includes/__header__.php';
?>

<link rel="stylesheet" href="assets/css/ecom/singel.css">

<!-- Breadcrumb -->
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php">Shop</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $product ? htmlspecialchars($product['title']) : 'Product'; ?></li>
        </ol>
    </nav>
</div>

<!-- Product Section -->
<section class="product-section">
    <div class="container">
        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php elseif ($product): ?>
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="product-main-img" id="main-product-img">
                        <img src="<?php echo !empty($images) ? htmlspecialchars('storage/' . $images[0]['image_path']) : 'assets/img/default_product.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                             id="main-img">
                    </div>
                    <div class="product-thumbnails">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="product-thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                                 data-img="<?php echo htmlspecialchars('storage/' . $image['image_path']); ?>">
                                <img src="<?php echo htmlspecialchars('storage/' . $image['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['title']); ?> Thumbnail <?php echo $index + 1; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="col-lg-6">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h1>
                    <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                    <div class="product-description">
                        <p><?php echo htmlspecialchars($product['short_description'] ?? 'No description available.'); ?></p>
                    </div>
                    
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="product-options">
                            <!-- Color Options -->
                            <label class="option-label">Color</label>
                            <div class="color-options">
                                <?php foreach ($colors as $index => $color): ?>
                                    <div class="color-option <?php echo $index === 0 ? 'active' : ''; ?>" 
                                         data-color="<?php echo htmlspecialchars($color['name']); ?>">
                                        <input type="radio" name="color_id" value="<?php echo $color['id']; ?>" 
                                               <?php echo $index === 0 ? 'checked' : ''; ?> hidden>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Size Options -->
                            <label class="option-label">Size</label>
                            <div class="size-options">
                                <?php foreach ($sizes as $index => $size): ?>
                                    <div class="size-option <?php echo $index === 0 ? 'active' : ''; ?>" 
                                         data-size="<?php echo htmlspecialchars($size['label']); ?>">
                                        <?php echo htmlspecialchars($size['label']); ?>
                                        <input type="radio" name="size_id" value="<?php echo $size['id']; ?>" 
                                               <?php echo $index === 0 ? 'checked' : ''; ?> hidden>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Quantity Selector -->
                            <label class="option-label">Quantity</label>
                            <div class="quantity-selector">
                                <button type="button" class="quantity-btn" id="decrease-quantity">-</button>
                                <input type="text" class="quantity-input" id="quantity" name="quantity" value="1" readonly>
                                <button type="button" class="quantity-btn" id="increase-quantity">+</button>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" name="add_to_cart" class="btn btn-minimal flex-grow-1">Add to Cart</button>
                            <button type="button" class="btn btn-outline">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Additional Info -->
                    <div class="mt-4">
                        <p class="mb-2"><i class="fas fa-truck me-2"></i> Free shipping on orders over $50</p>
                        <p class="mb-2"><i class="fas fa-undo me-2"></i> Free returns within 30 days</p>
                        <p><i class="fas fa-shield-alt me-2"></i> 1 year warranty</p>
                    </div>
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="product-details">
                <div class="details-tabs">
                    <div class="details-tab active" data-tab="description">Description</div>
                </div>
                
                <div class="details-content active" id="description">
                    <p><?php echo htmlspecialchars($product['long_description'] ?? 'No detailed description available.'); ?></p>
                </div>
                
               
                
               
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Products -->
<section class="related-products">
    <div class="container">
        <h2 class="section-title">You May Also Like</h2>
        <div class="row">
            <?php if (!empty($related_products)): ?>
                <?php foreach ($related_products as $index => $related): ?>
                    <div class="col-md-3" data-sr-id="<?php echo $index + 1; ?>">
                        <div class="card product-card">
                            <div class="product-img">
                                <img src="<?php echo $related['image_path'] ? htmlspecialchars('storage/' . $related['image_path']) : 'assets/img/default_product.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>">
                            </div>
                            <div class="card-body">
                                <h5 class="product-card-title"><?php echo htmlspecialchars($related['title']); ?></h5>
                                <p class="product-card-price mb-3">$<?php echo number_format($related['price'], 2); ?></p>
                                <button class="btn btn-minimal w-100" 
                                        onclick="addToCart(<?php echo $related['id']; ?>)">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No related products found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once './includes/__footer__.php'; ?>

<script>

</script>