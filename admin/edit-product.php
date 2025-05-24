<?php
ob_start();

require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

if (!isset($pdo)) {
    die("Database connection error. Check included files for PDO setup.");
}

$storage_dir = '../storage/';
if (!is_dir($storage_dir)) {
    mkdir($storage_dir, 0755, true);
}

function logError($message) {
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] {$message}\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

$success_message = '';
$error_message = '';

$product_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : 0;
if (!$product_id) {
    header('Location: products.php?error=' . urlencode('Invalid product ID.'));
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT title, short_description, long_description, price, stock, category_id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        header('Location: products.php?error=' . urlencode('Product not found.'));
        exit;
    }
} catch (Exception $e) {
    logError("Failed to fetch product: " . $e->getMessage());
    header('Location: products.php?error=' . urlencode('Failed to load product data.'));
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, name FROM category ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    logError("Failed to fetch categories: " . $e->getMessage());
    $error_message = 'Failed to load categories.';
    $categories = [];
}
try {
    $stmt = $pdo->prepare("SELECT s.label FROM product_sizes ps JOIN sizes s ON ps.size_id = s.id WHERE ps.product_id = ?");
    $stmt->execute([$product_id]);
    $selected_sizes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    logError("Failed to fetch selected sizes: " . $e->getMessage());
    $selected_sizes = [];
}
try {
    $stmt = $pdo->prepare("SELECT c.name FROM product_colors pc JOIN colors c ON pc.color_id = c.id WHERE pc.product_id = ?");
    $stmt->execute([$product_id]);
    $selected_colors = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    logError("Failed to fetch selected colors: " . $e->getMessage());
    $selected_colors = [];
}

try {
    $stmt = $pdo->prepare("SELECT image_path, image_type FROM product_images WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $main_image = null;
    $additional_images = [];
    foreach ($images as $img) {
        if ($img['image_type'] === 'main') {
            $main_image = $img['image_path'];
        } else {
            $additional_images[] = $img['image_path'];
        }
    }
} catch (Exception $e) {
    logError("Failed to fetch product images: " . $e->getMessage());
    $main_image = null;
    $additional_images = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {

        $title = trim($_POST['title'] ?? '');
        $short_desc = trim($_POST['short_desc'] ?? '');
        $long_desc = trim($_POST['long_desc'] ?? '');
        $category_id = filter_var($_POST['category'] ?? '', FILTER_VALIDATE_INT);
        $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
        $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT);
        $sizes = $_POST['size'] ?? [];
        $colors = array_filter(array_map('trim', $_POST['color'] ?? []));

        if (empty($title)) {
            throw new Exception('Product title is required.');
        }
        if (empty($short_desc)) {
            throw new Exception('Short description is required.');
        }
        if (empty($long_desc)) {
            throw new Exception('Long description is required.');
        }
        if (!$category_id || !$pdo->query("SELECT id FROM category WHERE id = $category_id")->fetchColumn()) {
            throw new Exception('Please select a valid category.');
        }
        if ($price === false || $price < 0) {
            throw new Exception('Price must be a valid non-negative number.');
        }
        if ($stock === false || $stock < 0) {
            throw new Exception('Stock must be a valid non-negative integer.');
        }
        if (empty($sizes)) {
            throw new Exception('At least one size must be selected.');
        }
        if (count($colors) > 4) {
            throw new Exception('Maximum 4 colors allowed.');
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE products SET title = ?, short_description = ?, long_description = ?, price = ?, stock = ?, category_id = ? WHERE id = ?");
        if (!$stmt->execute([$title, $short_desc, $long_desc, $price, $stock, $category_id, $product_id])) {
            throw new Exception('Failed to update product.');
        }

        $stmt = $pdo->prepare("DELETE FROM product_sizes WHERE product_id = ?");
        $stmt->execute([$product_id]);
        foreach ($sizes as $size) {
            $stmt = $pdo->prepare("SELECT id FROM sizes WHERE label = ?");
            $stmt->execute([$size]);
            $size_id = $stmt->fetchColumn();
            if (!$size_id) {
                throw new Exception("Invalid size: $size");
            }
            $stmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size_id) VALUES (?, ?)");
            if (!$stmt->execute([$product_id, $size_id])) {
                throw new Exception('Failed to add size association.');
            }
        }

        $stmt = $pdo->prepare("DELETE FROM product_colors WHERE product_id = ?");
        $stmt->execute([$product_id]);
        foreach ($colors as $color) {
            if (empty($color)) {
                continue;
            }
            $stmt = $pdo->prepare("SELECT id FROM colors WHERE name = ?");
            $stmt->execute([$color]);
            $color_id = $stmt->fetchColumn();
            if (!$color_id) {
                $stmt = $pdo->prepare("INSERT INTO colors (name) VALUES (?)");
                if (!$stmt->execute([$color])) {
                    throw new Exception("Failed to add color: $color");
                }
                $color_id = $pdo->lastInsertId();
            }
            $stmt = $pdo->prepare("INSERT INTO product_colors (product_id, color_id) VALUES (?, ?)");
            if (!$stmt->execute([$product_id, $color_id])) {
                throw new Exception('Failed to add color association.');
            }
        }

        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == UPLOAD_ERR_OK) {
            $main_image_file = $_FILES['main_image'];
            if (!str_starts_with($main_image_file['type'], 'image/')) {
                throw new Exception('Main image must be a valid image file.');
            }
            if ($main_image_file['size'] > 5 * 1024 * 1024) {
                throw new Exception('Main image size exceeds 5MB.');
            }
            if ($main_image) {
                $old_image_path = $storage_dir . $main_image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
                $stmt = $pdo->prepare("DELETE FROM product_images WHERE product_id = ? AND image_type = 'main'");
                $stmt->execute([$product_id]);
            }
            $main_image_name = time() . '_main_' . basename($main_image_file['name']);
            $main_image_path = $storage_dir . $main_image_name;
            if (!move_uploaded_file($main_image_file['tmp_name'], $main_image_path)) {
                throw new Exception('Failed to upload main image.');
            }
            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, image_type) VALUES (?, ?, 'main')");
            if (!$stmt->execute([$product_id, $main_image_name])) {
                throw new Exception('Failed to save main image to database.');
            }
        }

  
        $additional_images_files = $_FILES['more_images'] ?? [];
        if (!empty($additional_images_files['name'][0])) {
        
            $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ? AND image_type = 'additional'");
            $stmt->execute([$product_id]);
            $old_images = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($old_images as $old_image) {
                $old_image_path = $storage_dir . $old_image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $stmt = $pdo->prepare("DELETE FROM product_images WHERE product_id = ? AND image_type = 'additional'");
            $stmt->execute([$product_id]);

            $valid_additional_images = [];
            foreach ($additional_images_files['name'] as $key => $name) {
                if ($additional_images_files['error'][$key] == UPLOAD_ERR_OK) {
                    if (!str_starts_with($additional_images_files['type'][$key], 'image/')) {
                        throw new Exception('Additional image ' . ($key + 1) . ' must be a valid image file.');
                    }
                    if ($additional_images_files['size'][$key] > 5 * 1024 * 1024) {
                        throw new Exception('Additional image ' . ($key + 1) . ' size exceeds 5MB.');
                    }
                    $valid_additional_images[] = $key;
                }
            }
            foreach ($valid_additional_images as $key) {
                $add_image_name = time() . '_add_' . $key . '_' . basename($additional_images_files['name'][$key]);
                $add_image_path = $storage_dir . $add_image_name;
                if (!move_uploaded_file($additional_images_files['tmp_name'][$key], $add_image_path)) {
                    throw new Exception('Failed to upload additional image ' . ($key + 1) . '.');
                }
                $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, image_type) VALUES (?, ?, 'additional')");
                if (!$stmt->execute([$product_id, $add_image_name])) {
                    throw new Exception('Failed to save additional image ' . ($key + 1) . ' to database.');
                }
            }
        }

    
        $pdo->commit();

        $success_message = 'Product updated successfully!';
        header('Location: products.php?success=' . urlencode($success_message));
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error_message = $e->getMessage();
        logError("Error updating product ID $product_id: {$error_message}");
    }
}

$success_message = $_GET['success'] ?? '';
$error_message = $_GET['error'] ?? $error_message;
?>
    <title>Edit Product</title>
    <style>
        .img-preview-wrapper { margin-top: 10px; }
        .img-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 8px;
            display: none;
        }
        .existing-image {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
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
                        <h1>Edit Product</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                            <li class="breadcrumb-item active">Edit Product</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <form method="POST" enctype="multipart/form-data" id="editProductForm">
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
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Product Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="title">Product Title</label>
                                        <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($product['title']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price ($)</label>
                                        <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock">Stock Quantity</label>
                                        <input type="number" name="stock" id="stock" class="form-control" min="0" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="short_desc">Short Description</label>
                                        <textarea name="short_desc" id="short_desc" class="form-control" rows="2" required><?php echo htmlspecialchars($product['short_description']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="long_desc">Long Description</label>
                                        <textarea name="long_desc" id="long_desc" class="form-control" rows="5" required><?php echo htmlspecialchars($product['long_description']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Available Sizes</label>
                                        <div class="row">
                                            <?php
                                            $stmt = $pdo->query("SELECT label FROM sizes ORDER BY label");
                                            $sizes = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                            foreach ($sizes as $size) {
                                                $checked = in_array($size, $selected_sizes) ? 'checked' : '';
                                                echo '
                                                <div class="col-6 col-md-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="size_' . $size . '" name="size[]" value="' . $size . '" ' . $checked . '>
                                                        <label class="custom-control-label" for="size_' . $size . '">' . $size . '</label>
                                                    </div>
                                                </div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Colors (Max 4)</label>
                                        <?php
                                        $color_inputs = array_pad($selected_colors, 4, '');
                                        foreach ($color_inputs as $index => $color) {
                                            echo '<input type="text" name="color[]" class="form-control mb-2" placeholder="e.g. Red" value="' . htmlspecialchars($color) . '">';
                                        }
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select name="category" id="category" class="form-control" required>
                                            <option value="">-- Select Category --</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Product Images</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="main_image">Main Image (Optional)</label>
                                        <?php if ($main_image): ?>
                                            <div>
                                                <img src="../storage/<?php echo htmlspecialchars($main_image); ?>" alt="Main Image" class="existing-image">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="main_image" id="main_image" class="form-control" accept="image/*" onchange="previewImage(event, 'mainPreview')">
                                        <div class="img-preview-wrapper">
                                            <img id="mainPreview" class="img-preview" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="more_images_1">Additional Image 1 (Optional)</label>
                                        <?php if (isset($additional_images[0])): ?>
                                            <div>
                                                <img src="../storage/<?php echo htmlspecialchars($additional_images[0]); ?>" alt="Additional Image 1" class="existing-image">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="more_images[]" id="more_images_1" class="form-control" accept="image/*" onchange="previewImage(event, 'more1')">
                                        <div class="img-preview-wrapper">
                                            <img id="more1" class="img-preview" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="more_images_2">Additional Image 2 (Optional)</label>
                                        <?php if (isset($additional_images[1])): ?>
                                            <div>
                                                <img src="../storage/<?php echo htmlspecialchars($additional_images[1]); ?>" alt="Additional Image 2" class="existing-image">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="more_images[]" id="more_images_2" class="form-control" accept="image/*" onchange="previewImage(event, 'more2')">
                                        <div class="img-preview-wrapper">
                                            <img id="more2" class="img-preview" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-success btn-lg" onclick="showUpdateLoader()">Update Product</button>
                        </div>
                    </div>
                </div>
            </form>
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

function previewImage(event, previewId) {
    const input = event.target;
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (!file.type.startsWith('image/')) {
            toastr.error('Please select a valid image file.');
            input.value = '';
            preview.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

function showUpdateLoader() {
    Swal.fire({
        title: 'Updating Product...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

$('#editProductForm').on('submit', function(e) {
    const sizes = $('input[name="size[]"]:checked').length;
    const colors = $('input[name="color[]"]').filter(function() {
        return $(this).val().trim() !== '';
    }).length;
    const price = parseFloat($('#price').val());
    const stock = parseInt($('#stock').val());
    const category = $('#category').val();
    if (sizes === 0) {
        e.preventDefault();
        toastr.error('Please select at least one size.');
        Swal.close();
        return false;
    }
    if (colors > 4) {
        e.preventDefault();
        toastr.error('Maximum 4 colors allowed.');
        Swal.close();
        return false;
    }
    if (isNaN(price) || price < 0) {
        e.preventDefault();
        toastr.error('Price must be a valid non-negative number.');
        Swal.close();
        return false;
    }
    if (isNaN(stock) || stock < 0) {
        e.preventDefault();
        toastr.error('Stock must be a valid non-negative integer.');
        Swal.close();
        return false;
    }
    if (!category) {
        e.preventDefault();
        toastr.error('Please select a category.');
        Swal.close();
        return false;
    }
});
</script>
