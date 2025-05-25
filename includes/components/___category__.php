<?php
require_once 'server/dbcon.php';


try {
    $stmt = $pdo->query("SELECT id, name, image FROM category ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    
    $log_file = 'storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Failed to fetch categories: " . $e->getMessage() . "\n", FILE_APPEND);
    $categories = [];
}
?>

<section class="category-section py-5">
    <div class="container">
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <?php if (empty($categories)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No categories found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <div class="col">
                        <a href="shop.php?category=<?php echo urlencode($category['name']); ?>" class="text-decoration-none">
                            <div class="category-card h-100 text-center">
                                <div class="category-img mb-3">
                                    <img class="img-fluid rounded" 
                                         src="<?php echo $category['image'] ? htmlspecialchars('storage/' . $category['image']) : 'assets/img/default_category.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($category['name']); ?>">
                                </div>
                                <h3 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>