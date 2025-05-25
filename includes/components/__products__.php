<?php
require_once 'server/dbcon.php';

$error_message = '';

try {
    $stmt = $pdo->query("
        SELECT p.id, p.title, p.price, pi.image_path
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.image_type = 'main'
        ORDER BY p.created_at DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products)) {
        $error_message = 'No products found in the database.';
  
        $count_stmt = $pdo->query("SELECT COUNT(*) FROM products");
        $product_count = $count_stmt->fetchColumn();
        if ($product_count == 0) {
            $error_message .= ' The products table is empty.';
        } else {
      
            $image_count_stmt = $pdo->query("SELECT COUNT(*) FROM product_images WHERE image_type = 'main'");
            $image_count = $image_count_stmt->fetchColumn();
            if ($image_count == 0) {
                $error_message .= ' No main images found in product_images.';
            }
        }
    }

    $invalid_categories = $pdo->query("
        SELECT p.id, p.title
        FROM products p
        WHERE p.category_id IS NULL
           OR p.category_id NOT IN (SELECT id FROM category)
    ")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($invalid_categories)) {
        $error_message .= ' Warning: Some products have invalid category_id values: ';
        foreach ($invalid_categories as $invalid) {
            $error_message .= "Product ID {$invalid['id']} ({$invalid['title']}), ";
        }
        $error_message = rtrim($error_message, ', ') . '.';
    }

} catch (Exception $e) {
 
    $error_message = 'Error fetching products: ' . htmlspecialchars($e->getMessage());
    $log_file = 'storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Failed to fetch products: " . $e->getMessage() . "\n", FILE_APPEND);
    $products = [];
}
?>
<link rel="stylesheet" href="assets/css/ecom/main.css">
<link rel="stylesheet" href="assets/css/ecom/shop.css">
<section class="product-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">Featured Products</h2>
        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <?php if (empty($products) && !$error_message): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No products found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $index => $product): ?>
                    <div class="col-md-3">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                            <div class="card product-card shadow-sm">
                                <div class="product-img">
                                    <img src="<?php echo $product['image_path'] ? htmlspecialchars('storage/' . $product['image_path']) : 'assets/img/default_product.jpg'; ?>" 
                                         class="img-fluid" 
                                         alt="<?php echo htmlspecialchars($product['title']); ?>">
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                                    <p class="product-price mb-3">$<?php echo number_format($product['price'], 2); ?></p>
                                    <button class="btn btn-minimal w-100 add-to-cart" 
                                            data-product-id="<?php echo $product['id']; ?>" 
                                            onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
function addToCart(productId) {
    alert('Product ' + productId + ' added to cart!');
}
</script>