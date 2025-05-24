<?php
ob_start();

require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

// Check if PDO is defined
if (!isset($pdo)) {
    die("Database connection error. Check included files for PDO setup.");
}

// Ensure storage directory exists
$storage_dir = '../storage/';
if (!is_dir($storage_dir)) {
    mkdir($storage_dir, 0755, true);
}

// Error logging function
function logError($message) {
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] {$message}\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Initialize messages
$success_message = $_GET['success'] ?? '';
$error_message = $_GET['error'] ?? '';

// Fetch carousels
try {
    $stmt = $pdo->query("SELECT id, image_path, created_at FROM carousels ORDER BY created_at DESC");
    $carousels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    logError("Failed to fetch carousels: " . $e->getMessage());
    $error_message = 'Failed to load carousels.';
    $carousels = [];
}

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        if (isset($_POST['add_carousel'])) {
            // Validate image
            if (!isset($_FILES['image']) || $_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
                throw new Exception('Carousel image is required.');
            }
            $image = $_FILES['image'];
            if (!str_starts_with($image['type'], 'image/')) {
                throw new Exception('Please upload a valid image file.');
            }
            if ($image['size'] > 5 * 1024 * 1024) {
                throw new Exception('Image size exceeds 5MB.');
            }

            // Upload image
            $image_name = time() . '_carousel_' . basename($image['name']);
            $image_path = $storage_dir . $image_name;
            if (!move_uploaded_file($image['tmp_name'], $image_path)) {
                throw new Exception('Failed to upload image.');
            }

            // Insert carousel
            $stmt = $pdo->prepare("INSERT INTO carousels (image_path) VALUES (?)");
            if (!$stmt->execute([$image_name])) {
                throw new Exception('Failed to add carousel.');
            }

            $success_message = 'Carousel added successfully!';
        } elseif (isset($_POST['edit_carousel'])) {
            $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('Invalid carousel ID.');
            }

            // Fetch current carousel
            $stmt = $pdo->prepare("SELECT image_path FROM carousels WHERE id = ?");
            $stmt->execute([$id]);
            $carousel = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$carousel) {
                throw new Exception('Carousel not found.');
            }

            $new_image_path = $carousel['image_path'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                // Validate new image
                $image = $_FILES['image'];
                if (!str_starts_with($image['type'], 'image/')) {
                    throw new Exception('Please upload a valid image file.');
                }
                if ($image['size'] > 5 * 1024 * 1024) {
                    throw new Exception('Image size exceeds 5MB.');
                }

                // Upload new image
                $image_name = time() . '_carousel_' . basename($image['name']);
                $new_image_path = $storage_dir . $image_name;
                if (!move_uploaded_file($image['tmp_name'], $new_image_path)) {
                    throw new Exception('Failed to upload new image.');
                }

                // Delete old image
                $old_image_path = $storage_dir . $carousel['image_path'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }

            // Update carousel
            $stmt = $pdo->prepare("UPDATE carousels SET image_path = ? WHERE id = ?");
            if (!$stmt->execute([$new_image_path, $id])) {
                throw new Exception('Failed to update carousel.');
            }

            $success_message = 'Carousel updated successfully!';
        } elseif (isset($_POST['delete_id'])) {
            $id = filter_var($_POST['delete_id'] ?? 0, FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('Invalid carousel ID.');
            }

            // Fetch carousel
            $stmt = $pdo->prepare("SELECT image_path FROM carousels WHERE id = ?");
            $stmt->execute([$id]);
            $carousel = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$carousel) {
                throw new Exception('Carousel not found.');
            }

            // Delete image
            $image_path = $storage_dir . $carousel['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            // Delete carousel
            $stmt = $pdo->prepare("DELETE FROM carousels WHERE id = ?");
            if (!$stmt->execute([$id])) {
                throw new Exception('Failed to delete carousel.');
            }

            $success_message = 'Carousel deleted successfully!';
        }

        $pdo->commit();
        header('Location: carousel.php?success=' . urlencode($success_message));
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        logError("Error processing carousel: " . $e->getMessage());
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carousel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .carousel-img {
            max-height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .img-preview {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            display: none;
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>Carousel List</h1>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCarouselModal"><i class="fas fa-plus"></i> Add New Carousel</button>
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
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        All Carousel Images
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Created At</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($carousels)): ?>
                                    <tr><td colspan="4" class="text-center">No carousel images found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($carousels as $i => $carousel): ?>
                                        <tr>
                                            <td><?php echo $i + 1; ?></td>
                                            <td>
                                                <img src="<?php echo htmlspecialchars('../storage/' . $carousel['image_path']); ?>" alt="Carousel Image" class="carousel-img">
                                            </td>
                                            <td><?php echo htmlspecialchars($carousel['created_at']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editCarouselModal" onclick="loadEditModal(<?php echo $carousel['id']; ?>, '<?php echo htmlspecialchars($carousel['image_path']); ?>')"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $carousel['id']; ?>)"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add Carousel Modal -->
        <div class="modal fade" id="addCarouselModal" tabindex="-1" role="dialog" aria-labelledby="addCarouselModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCarouselModalLabel">Add New Carousel</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="add_image">Carousel Image</label>
                                <input type="file" name="image" id="add_image" class="form-control" accept="image/*" onchange="previewImage(event, 'addPreview')" required>
                                <img id="addPreview" class="img-preview" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="add_carousel" class="btn btn-primary">Add Carousel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Carousel Modal -->
        <div class="modal fade" id="editCarouselModal" tabindex="-1" role="dialog" aria-labelledby="editCarouselModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCarouselModalLabel">Edit Carousel</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit_id">
                            <div class="form-group">
                                <label for="edit_image">Carousel Image</label>
                                <input type="file" name="image" id="edit_image" class="form-control" accept="image/*" onchange="previewImage(event, 'editPreview')">
                                <img id="editPreview" class="img-preview" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="edit_carousel" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once './includes/__footer__.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

function loadEditModal(id, imagePath) {
    document.getElementById('edit_id').value = id;
    const preview = document.getElementById('editPreview');
    preview.src = '../storage/' + imagePath;
    preview.style.display = 'block';
}

function confirmDelete(id) {
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
            form.action = 'carousel.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_id';
            input.value = id;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
</body>
</html>