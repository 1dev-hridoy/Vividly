<?php
ob_start();
session_start();
require_once 'server/dbcon.php';

global $pdo;

$category = $_GET['category'] ?? '';
$price = $_GET['price'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'featured';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;

$sql = "
    SELECT p.id, p.title, p.short_description, p.price, p.created_at, c.name AS category, pi.image_path
    FROM products p
    LEFT JOIN category c ON p.category_id = c.id
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.image_type = 'main'
";
$where = [];
$params = [];

if ($category) {
    if ($category === 'uncategorized') {
        $where[] = "(p.category_id IS NULL OR p.category_id NOT IN (SELECT id FROM category))";
    } else {
        $where[] = "c.name = ?";
        $params[] = $category;
    }
}
if ($price) {
    $ranges = [
        '0-50' => [0, 50],
        '50-100' => [50, 100],
        '100-200' => [100, 200],
        '200+' => [200, 999999]
    ];
    if (isset($ranges[$price])) {
        $where[] = "p.price BETWEEN ? AND ?";
        $params[] = $ranges[$price][0];
        $params[] = $ranges[$price][1];
    }
}
if ($search) {
    $where[] = "(p.title LIKE ? OR p.short_description LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}


$sortOptions = [
    'price-low' => 'p.price ASC',
    'price-high' => 'p.price DESC',
    'newest' => 'p.created_at DESC',
    'featured' => 'p.id DESC'
];
$sql .= " ORDER BY " . ($sortOptions[$sort] ?? 'p.id DESC');

try {
   
    $countSql = "SELECT COUNT(*) FROM products p LEFT JOIN category c ON p.category_id = c.id" . ($where ? " WHERE " . implode(' AND ', $where) : "");
    $stmt = $pdo->prepare($countSql);
    $countParams = $params;
    $stmt->execute($countParams);
    $total = $stmt->fetchColumn();
    $pages = ceil($total / $perPage);

    $sql .= " LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $paramIndex = 1;
    foreach ($params as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    $stmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, ($page - 1) * $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $productId = $_POST['product_id'];
        $colorId = $_POST['color_id'] ?? null;
        $sizeId = $_POST['size_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $cartKey = "$productId-$colorId-$sizeId";
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'color_id' => $colorId,
                'size_id' => $sizeId,
                'quantity' => $quantity
            ];
        }
        echo '<script>toastr.success("Product added to cart!");</script>';
    }

    
    $stmt = $pdo->query("SELECT name FROM category ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = 'Error fetching products: ' . htmlspecialchars($e->getMessage());
    file_put_contents('../storage/error_log.txt', "[" . date('Y-m-d H:i:s') . "] Shop error: " . $e->getMessage() . "\n", FILE_APPEND);
    $products = [];
    $categories = [];
    $total = 0;
    $pages = 1;
}
require_once './includes/__header__.php';
?>

<link rel="stylesheet" href="assets/css/ecom/shop.css">

<!-- Shop Header -->
<section class="shop-header">
    <div class="container">
        <h1 class="shop-title">Shop Collection</h1>
        <p class="shop-subtitle">Discover our curated selection of minimal, essential pieces designed for modern living</p>
    </div>
</section>

<!-- Filters Section -->
<section class="filters-section">
    <div class="container">
        <div class="filters-container">
            <div class="filter-group">
                <select class="filter-dropdown" id="category-filter" onchange="applyFilters()">
                    <option value="">All Categories</option>
                    <option value="uncategorized" <?= $category === 'uncategorized' ? 'selected' : '' ?>>Uncategorized</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['name']) ?>" <?= $cat['name'] === $category ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <select class="filter-dropdown" id="price-filter" onchange="applyFilters()">
                    <option value="">All Prices</option>
                    <option value="0-50" <?= $price === '0-50' ? 'selected' : '' ?>>৳0 - ৳50</option>
                    <option value="50-100" <?= $price === '50-100' ? 'selected' : '' ?>>৳50 - ৳100</option>
                    <option value="100-200" <?= $price === '100-200' ? 'selected' : '' ?>>৳100 - ৳200</option>
                    <option value="200+" <?= $price === '200+' ? 'selected' : '' ?>>৳200+</option>
                </select>
                
                <select class="filter-dropdown" id="sort-filter" onchange="applyFilters()">
                    <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Featured</option>
                    <option value="price-low" <?= $sort === 'price-low' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price-high" <?= $sort === 'price-high' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                </select>
            </div>
            
            <div class="filter-group">
                <input type="text" class="search-box" placeholder="Search products..." id="search-input" value="<?= htmlspecialchars($search) ?>" oninput="debounce(applyFilters, 300)">
                
                <div class="view-toggle">
                    <button class="view-btn active" data-view="grid"><i class="fas fa-th"></i></button>
                    <button class="view-btn" data-view="list"><i class="fas fa-list"></i></button>
                </div>
            </div>
        </div>
        
        <div class="results-count mt-3">
            Showing <span id="results-count"><?= count($products) ?></span> of <span id="total-count"><?= $total ?></span> products
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products-section">
    <div class="container">
        <!-- Grid View -->
        <div class="products-grid active" id="products-grid">
            <?php if (empty($products)): ?>
                <p class="text-muted text-center">No products found.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-category="<?= htmlspecialchars(strtolower($product['category'] ?? 'uncategorized')) ?>" data-price="<?= $product['price'] ?>">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars('storage/' . $product['image_path'] ?? 'assets/images/placeholder.jpg') ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                            <?php
                            $createdAt = new DateTime($product['created_at'] ?? 'now');
                            $thirtyDaysAgo = new DateTime('-30 days');
                            if ($createdAt > $thirtyDaysAgo): ?>
                                <div class="product-badge new">New</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Uncategorized') ?></div>
                            <h3 class="product-title"><a href="product.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></a></h3>
                            <div class="product-price">
                                <span class="current-price">৳<?= number_format($product['price'], 2) ?></span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- List View -->
        <div class="products-list" id="products-list">
            <?php if (empty($products)): ?>
                <p class="text-muted text-center">No products found.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card-list" data-category="<?= htmlspecialchars(strtolower($product['category'] ?? 'uncategorized')) ?>" data-price="<?= $product['price'] ?>">
                        <div class="product-image-list">
                            <img src="<?= htmlspecialchars('storage/' . $product['image_path'] ?? 'assets/images/placeholder.jpg') ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                        </div>
                        <div class="product-info-list">
                            <div class="product-details-list">
                                <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Uncategorized') ?></div>
                                <h3 class="product-title-list"><a href="product.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></a></h3>
                                <p class="product-description-list"><?= htmlspecialchars($product['short_description'] ?? 'No description available.') ?></p>
                            </div>
                            <div class="product-actions-list">
                                <div class="product-price-list">
                                    <span class="current-price">৳<?= number_format($product['price'], 2) ?></span>
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" name="add_to_cart" class="add-to-cart-list">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Pagination -->
<section class="pagination-section">
    <div class="container">
        <nav aria-label="Product pagination">
            <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>&category=<?= urlencode($category) ?>&price=<?= urlencode($price) ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php for ($p = 1; $p <= $pages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>&category=<?= urlencode($category) ?>&price=<?= urlencode($price) ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&category=<?= urlencode($category) ?>&price=<?= urlencode($price) ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</section>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading-overlay">
    <div class="loading-spinner"></div>
</div>

<?php require_once './includes/__footer__.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {

    

    const sr = ScrollReveal({
        origin: 'bottom',
        distance: '20px',
        duration: 800,
        delay: 100,
        easing: 'ease',
        reset: false
    });
    
    sr.reveal('.shop-header', { origin: 'top', distance: '30px' });
    sr.reveal('.filters-section', { origin: 'top', distance: '20px', delay: 200 });
    sr.reveal('.product-card', { interval: 100 });
    sr.reveal('.product-card-list', { interval: 150 });
    const viewButtons = document.querySelectorAll('.view-btn');
    const gridView = document.getElementById('products-grid');
    const listView = document.getElementById('products-list');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            if (view === 'grid') {
                gridView.classList.add('active');
                listView.classList.remove('active');
            } else {
                gridView.classList.remove('active');
                listView.classList.add('active');
            }
            anime({
                targets: view === 'grid' ? gridView : listView,
                opacity: [0, 1],
                translateY: [20, 0],
                duration: 500,
                easing: 'easeOutExpo'
            });
        });
    });
    

    function applyFilters() {
        const category = document.getElementById('category-filter').value;
        const price = document.getElementById('price-filter').value;
        const search = document.getElementById('search-input').value;
        const sort = document.getElementById('sort-filter').value;
        const url = new URL(window.location);
        url.searchParams.set('category', category);
        url.searchParams.set('price', price);
        url.searchParams.set('search', search);
        url.searchParams.set('sort', sort);
        url.searchParams.set('page', 1);
        window.location = url;
    }
    

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    

    document.querySelectorAll('.add-to-cart, .add-to-cart-list').forEach(btn => {
        btn.addEventListener('click', function(e) {
            anime({
                targets: this,
                scale: [1, 0.95, 1],
                duration: 200,
                easing: 'easeInOutQuad'
            });
        });
    });
});
</script>