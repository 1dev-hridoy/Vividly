<?php
ob_start();

require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
  
        if (isset($_POST['add_category'])) {
            $name = trim($_POST['category_name'] ?? '');
            if (empty($name)) {
                throw new Exception('Category name is required.');
            }
            if (!isset($_FILES['category_image']) || $_FILES['category_image']['error'] == UPLOAD_ERR_NO_FILE) {
                throw new Exception('Category image is required.');
            }

            $image = $_FILES['category_image']['name'];
            $image_tmp = $_FILES['category_image']['tmp_name'];
            $image_type = $_FILES['category_image']['type'];
            $target_file = $storage_dir . basename($image);

         
            if (!str_starts_with($image_type, 'image/')) {
                throw new Exception('Invalid image file type.');
            }
            if ($_FILES['category_image']['size'] > 5 * 1024 * 1024) { 
                throw new Exception('Image size exceeds 5MB.');
            }

   
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM category WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Category name already exists.');
            }

            if (!move_uploaded_file($image_tmp, $target_file)) {
                throw new Exception('Failed to upload image.');
            }

            $stmt = $pdo->prepare("INSERT INTO category (name, image) VALUES (?, ?)");
            if (!$stmt->execute([$name, $image])) {
                throw new Exception('Failed to add category to database.');
            }

            $success_message = 'Category added successfully!';
        }

      
        elseif (isset($_POST['edit_category'])) {
            $id = filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT);
            $name = trim($_POST['edit_category_name'] ?? '');
            if (!$id || empty($name)) {
                throw new Exception('Invalid category ID or name.');
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM category WHERE name = ? AND id != ?");
            $stmt->execute([$name, $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Category name already exists.');
            }

            $stmt = $pdo->prepare("SELECT image FROM category WHERE id = ?");
            $stmt->execute([$id]);
            $current_category = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$current_category) {
                throw new Exception('Category not found.');
            }

            if (isset($_FILES['edit_category_image']) && $_FILES['edit_category_image']['error'] != UPLOAD_ERR_NO_FILE) {
                $image = $_FILES['edit_category_image']['name'];
                $image_tmp = $_FILES['edit_category_image']['tmp_name'];
                $image_type = $_FILES['edit_category_image']['type'];
                $target_file = $storage_dir . basename($image);

                if (!str_starts_with($image_type, 'image/')) {
                    throw new Exception('Invalid image file type.');
                }
                if ($_FILES['edit_category_image']['size'] > 5 * 1024 * 1024) {
                    throw new Exception('Image size exceeds 5MB.');
                }

                $old_image_path = $storage_dir . $current_category['image'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }

                if (!move_uploaded_file($image_tmp, $target_file)) {
                    throw new Exception('Failed to upload image.');
                }

                

                $stmt = $pdo->prepare("UPDATE category SET name = ?, image = ? WHERE id = ?");
                if (!$stmt->execute([$name, $image, $id])) {
                    throw new Exception('Failed to update category.');
                }
            } else {
                $stmt = $pdo->prepare("UPDATE category SET name = ? WHERE id = ?");
                if (!$stmt->execute([$name, $id])) {
                    throw new Exception('Failed to update category.');
                }
            }

            $success_message = 'Category updated successfully!';
        }

   
        elseif (isset($_POST['delete_category'])) {
            $name = trim($_POST['delete_category_name'] ?? '');
            if (empty($name)) {
                throw new Exception('Category name is required for deletion.');
            }

            $stmt = $pdo->prepare("SELECT image FROM category WHERE name = ?");
            $stmt->execute([$name]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$category) {
                throw new Exception('Category not found.');
            }

            $image_path = $storage_dir . $category['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            $stmt = $pdo->prepare("DELETE FROM category WHERE name = ?");
            if (!$stmt->execute([$name])) {
                throw new Exception('Failed to delete category.');
            }

            $success_message = 'Category deleted successfully!';
        }


        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=' . urlencode($success_message));
        exit;
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        logError("Error: {$error_message}");
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($error_message));
        exit;
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM category");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    logError("Failed to fetch categories: " . $e->getMessage());
    $error_message = 'Failed to load categories.';
    $categories = [];
}

$success_message = $_GET['success'] ?? '';
$error_message = $_GET['error'] ?? '';
?>


<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1>Product Categories</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <table class="table table-bordered table-hover bg-white shadow-sm">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 10%">#</th>
                        <th>Category Name</th>
                        <th>Image</th>
                        <th style="width: 20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $i => $cat): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td>
                                <img src="../storage/<?php echo htmlspecialchars($cat['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($cat['name']); ?>" 
                                     style="width:40px; height:40px; object-fit:cover; border-radius:8px; box-shadow:0 0 8px #ddd; background:#fff;">
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" 
                                        data-toggle="modal" 
                                        data-target="#editCategoryModal" 
                                        data-id="<?php echo $cat['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($cat['name']); ?>" 
                                        data-image="<?php echo htmlspecialchars($cat['image']); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" 
                                        data-toggle="modal" 
                                        data-target="#deleteCategoryModal" 
                                        data-name="<?php echo htmlspecialchars($cat['name']); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No categories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addCategoryForm" class="modal-content" enctype="multipart/form-data" method="post" action="">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="category_name">Category Name</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" required>
                </div>
                <div class="form-group">
                    <label for="addCategoryImage">Category Image</label>
                    <input type="file" accept="image/*" class="form-control-file" id="addCategoryImage" name="category_image" required>
                    <div class="mt-2">
                        <img id="addImagePreview" src="#" alt="Image Preview" 
                             style="display:none; width:100px; height:100px; object-fit:cover; border-radius:12px; box-shadow:0 0 12px #ddd; background:#fff;">
                        <small id="addImageSize" class="text-muted"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="add_category" onclick="showAddLoader()">Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editCategoryForm" class="modal-content" enctype="multipart/form-data" method="post" action="">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="form-group">
                    <label for="edit_category_name">Category Name</label>
                    <input type="text" class="form-control" name="edit_category_name" id="edit_category_name" required>
                </div>
                <div class="form-group">
                    <label for="editCategoryImage">Category Image (Optional)</label>
                    <input type="file" accept="image/*" class="form-control-file" id="editCategoryImage" name="edit_category_image">
                    <div class="mt-2">
                        <img id="editImagePreview" src="#" alt="Image Preview" 
                             style="display:none; width:100px; height:100px; object-fit:cover; border-radius:12px; box-shadow:0 0 12px #ddd; background:#fff;">
                        <small id="editImageSize" class="text-muted"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" name="edit_category">Update Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" method="post" action="">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Confirm Delete</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="delete_category_name">this category</strong>?
            </div>
            <div class="modal-footer">
                <input type="hidden" name="delete_category_name" id="delete_category_input">
                <button type="submit" class="btn btn-danger" name="delete_category">Yes, Delete</button>
            </div>
        </form>
    </div>
</div>

<?php require_once './includes/__footer__.php'; ?>

<script>
toastr.options = {
    closeButton: true,
    positionClass: 'toast-top-right',
    timeOut: 5000
};

<?php if ($success_message): ?>
    toastr.success(<?php echo json_encode($success_message); ?>);
<?php endif; ?>
<?php if ($error_message): ?>
    toastr.error(<?php echo json_encode($error_message); ?>);
<?php endif; ?>

$('#editCategoryModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const id = button.data('id');
    const name = button.data('name');
    const image = button.data('image');
    const modal = $(this);
    modal.find('#edit_category_id').val(id);
    modal.find('#edit_category_name').val(name);
    if (image) {
        modal.find('#editImagePreview').attr('src', '../storage/' + image).show();
        modal.find('#editImageSize').text('');
    } else {
        modal.find('#editImagePreview').hide();
        modal.find('#editImageSize').text('');
    }
});

$('#deleteCategoryModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const name = button.data('name');
    $(this).find('#delete_category_name').text(name);
    $(this).find('#delete_category_input').val(name);
});

$('#addCategoryImage').change(function() {
    readImage(this, '#addImagePreview', '#addImageSize');
});

$('#editCategoryImage').change(function() {
    readImage(this, '#editImagePreview', '#editImageSize');
});

function readImage(input, previewSelector, sizeSelector) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (!file.type.startsWith('image/')) {
            toastr.error('Please select a valid image file.');
            input.value = '';
            $(previewSelector).hide();
            $(sizeSelector).text('');
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            $(previewSelector).attr('src', e.target.result).show();
            $(sizeSelector).text('Size: ' + (file.size / 1024).toFixed(2) + ' KB');
        };
        reader.readAsDataURL(file);
    }
}

function showAddLoader() {
    Swal.fire({
        title: 'Adding Category...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}
</script>

</body>
</html>