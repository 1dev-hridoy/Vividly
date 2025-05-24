<?php
ob_start();

require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

if (!isset($pdo)) {
    die("Database connection error. Check included files for PDO setup.");
}


function logError($message) {
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] {$message}\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

$success_message = $_GET['success'] ?? '';
$error_message = $_GET['error'] ?? '';


$products_per_page = 10;
$page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT, ['min_range' => 1]) : 1;
if (!$page) $page = 1;
$offset = ($page - 1) * $products_per_page;


$category_id = isset($_GET['category_id']) ? filter_var($_GET['category_id'], FILTER_VALIDATE_INT) : 0;


$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $stmt = $pdo->query("SELECT id, name FROM category ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    logError("Failed to fetch categories: " . $e->getMessage());
    $error_message = 'Failed to load categories.';
    $categories = [];
}

$count_query = "SELECT COUNT(*) 
                FROM products p
                LEFT JOIN category c ON p.category_id = c.id";
$count_params = [];
$where_clauses = [];

if ($category_id) {
    $where_clauses[] = "p.category_id = ?";
    $count_params[] = $category_id;
}

if ($search) {
    $where_clauses[] = "(p.title LIKE ? OR p.short_description LIKE ?)";
    $search_term = "%$search%";
    $count_params[] = $search_term;
    $count_params[] = $search_term;
}

if (!empty($where_clauses)) {
    $count_query .= " WHERE " . implode(" AND ", $where_clauses);
}

try {
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($count_params);
    $total_products = $stmt->fetchColumn();
    $total_pages = ceil($total_products / $products_per_page);
} catch (Exception $e) {
    logError("Failed to count products: " . $e->getMessage());
    $error_message = 'Failed to load product count.';
    $total_products = 0;
    $total_pages = 1;
}

try {
    $query = "SELECT p.id, p.title, p.price, p.stock, c.name AS category_name, 
                     (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.id AND pi.image_type = 'main' LIMIT 1) AS main_image
              FROM products p
              LEFT JOIN category c ON p.category_id = c.id";
    
    $params = [];
    if ($category_id) {
        $where_clauses[] = "p.category_id = ?";
        $params[] = $category_id;
    }
    if ($search) {
        $where_clauses[] = "(p.title LIKE ? OR p.short_description LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
    }
    if (!empty($where_clauses)) {
        $query .= " WHERE " . implode(" AND ", $where_clauses);
    }
    $query .= " ORDER BY p.created_at DESC LIMIT $products_per_page OFFSET $offset";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    logError("Executed product query: $query with params: " . json_encode($params));
} catch (Exception $e) {
    logError("Failed to fetch products: " . $e->getMessage());
    $error_message = 'Failed to load products.';
    $products = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = filter_var($_POST['delete_id'], FILTER_VALIDATE_INT);
    if (!$delete_id) {
        $error_message = 'Invalid product ID for deletion.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
            $stmt->execute([$delete_id]);
            $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($images as $image) {
                $image_path = '../storage/' . $image;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            $stmt = $pdo->prepare("DELETE FROM product_images WHERE product_id = ?");
            $stmt->execute([$delete_id]);
            $stmt = $pdo->prepare("DELETE FROM product_sizes WHERE product_id = ?");
            $stmt->execute([$delete_id]);
            $stmt = $pdo->prepare("DELETE FROM product_colors WHERE product_id = ?");
            $stmt->execute([$delete_id]);
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            if (!$stmt->execute([$delete_id])) {
                throw new Exception('Failed to delete product.');
            }

            $pdo->commit();
            $success_message = 'Product deleted successfully!';
            $query_string = http_build_query(array_filter([
                'success' => $success_message,
                'category_id' => $category_id ?: null,
                'search' => $search ?: null,
                'page' => $page
            ]));
            header("Location: products.php?$query_string");
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            logError("Error deleting product ID $delete_id: " . $e->getMessage());
            $error_message = 'Failed to delete product.';
        }
    }
}

$base_query = array_filter([
    'category_id' => $category_id ?: null,
    'search' => $search ?: null
]);
?>


    <title>Products</title>
    <style>
        .product-img {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php require_once './includes/__navbar__.php'; ?>
    <?php require_once './includes/__side_bar__.php'; ?>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Products</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Products</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="card-title">Product List</h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <form method="GET" action="products.php">
                                    <div class="input-group">
                                        <select name="category_id" class="form-control" onchange="this.form.submit()">
                                            <option value="0">All Categories</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-filter"></i> Filter</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                                </form>
                            </div>
                            <div class="col-md-8">
                                <form method="GET" action="products.php">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search by title or description" value="<?php echo htmlspecialchars($search); ?>">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i> Search</button>
                                            <?php if ($search): ?>
                                                <a href="products.php?<?php echo http_build_query(['category_id' => $category_id, 'page' => 1]); ?>" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Clear</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
                                    <input type="hidden" name="page" value="1">
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Price ($)</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($products)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No products found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo $product['main_image'] ? '../storage/' . htmlspecialchars($product['main_image']) : 'assets/img/default.jpg'; ?>" alt="Product Image" class="product-img">
                                                </td>
                                                <td><?php echo htmlspecialchars($product['title']); ?></td>
                                                <td><?php echo htmlspecialchars($product['category_name'] ?: 'Uncategorized'); ?></td>
                                                <td><?php echo number_format($product['price'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($product['stock']); ?></td>
                                                <td>
                                                    <a href="edit-product.php?id=<?php echo $product['id']; ?>&<?php echo http_build_query($base_query); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $product['id']; ?>)"><i class="fas fa-trash"></i> Delete</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Product pagination">
                                <ul class="pagination">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="products.php?<?php echo http_build_query(array_merge($base_query, ['page' => $page - 1])); ?>" aria-label="Previous">
                                            <span aria-hidden="true">«</span>
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="products.php?<?php echo http_build_query(array_merge($base_query, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="products.php?<?php echo http_build_query(array_merge($base_query, ['page' => $page + 1])); ?>" aria-label="Next">
                                            <span aria-hidden="true">»</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once './includes/__footer__.php'; ?>
</div>

<script>
toastr.options = {
    closeButton: true,
    positionClass: 'toast-top-right',
    timeOut: 5000,
    progressBar: true
};

<?php if ($success_message): ?>
    toastr.success(<?php echo json_encode($success_message); ?>);
<?php endif; ?>
<?php if ($error_message): ?>
    toastr.error(<?php echo json_encode($error_message); ?>);
<?php endif; ?>

function confirmDelete(productId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'products.php?<?php echo http_build_query($base_query); ?>&page=<?php echo $page; ?>';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_id';
            input.value = productId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
</body>
</html>