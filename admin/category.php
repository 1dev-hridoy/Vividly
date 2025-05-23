<?php require_once './includes/__navbar__.php'; ?>
<?php require_once './includes/__side_bar__.php'; ?>

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
          <?php
          // Dummy data with images (replace with DB)
          $categories = [
            ['name' => 'Clothing', 'image' => 'clothing.jpg'],
            ['name' => 'Electronics', 'image' => 'electronics.jpg'],
            ['name' => 'Shoes', 'image' => 'shoes.jpg'],
            ['name' => 'Accessories', 'image' => 'accessories.jpg'],
          ];

          foreach ($categories as $i => $cat) {
            echo "
              <tr>
                <td>" . ($i + 1) . "</td>
                <td>{$cat['name']}</td>
                <td><img src='uploads/{$cat['image']}' alt='{$cat['name']}' style='width:40px; height:40px; object-fit:cover; border-radius:8px; box-shadow:0 0 8px #ddd; background:#fff;'></td>
                <td>
                  <button class='btn btn-sm btn-info' data-toggle='modal' data-target='#editCategoryModal' data-name='{$cat['name']}' data-image='{$cat['image']}'><i class='fas fa-edit'></i></button>
                  <button class='btn btn-sm btn-danger' data-toggle='modal' data-target='#deleteCategoryModal' data-name='{$cat['name']}'><i class='fas fa-trash'></i></button>
                </td>
              </tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="addCategoryForm" class="modal-content" enctype="multipart/form-data" method="post" action="your_add_category_handler.php">
      <div class="modal-header">
        <h5 class="modal-title">Add New Category</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Category Name</label>
          <input type="text" class="form-control" name="category_name" required>
        </div>
        <div class="form-group">
          <label>Category Image</label>
          <input type="file" accept="image/*" class="form-control-file" id="addCategoryImage" name="category_image" required>
          <div class="mt-2">
            <img id="addImagePreview" src="#" alt="Image Preview" style="display:none; width:100px; height:100px; object-fit:cover; border-radius:12px; box-shadow:0 0 12px #ddd; background:#fff;">
            <small id="addImageSize" class="text-muted"></small>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add Category</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="editCategoryForm" class="modal-content" enctype="multipart/form-data" method="post" action="your_edit_category_handler.php">
      <div class="modal-header">
        <h5 class="modal-title">Edit Category</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="category_id">
        <div class="form-group">
          <label>Category Name</label>
          <input type="text" class="form-control" name="edit_category_name" id="edit_category_name" required>
        </div>
        <div class="form-group">
          <label>Category Image</label>
          <input type="file" accept="image/*" class="form-control-file" id="editCategoryImage" name="edit_category_image">
          <div class="mt-2">
            <img id="editImagePreview" src="#" alt="Image Preview" style="display:none; width:100px; height:100px; object-fit:cover; border-radius:12px; box-shadow:0 0 12px #ddd; background:#fff;">
            <small id="editImageSize" class="text-muted"></small>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Update Category</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="your_delete_category_handler.php">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete <strong id="delete_category_name">this category</strong>?
      </div>
      <div class="modal-footer">
        <input type="hidden" name="delete_category_name" id="delete_category_input">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
      </div>
    </form>
  </div>
</div>

<?php require_once './includes/__footer__.php'; ?>

<script>
  // Toastr examples (simulate success/error after form submit, replace with your PHP flash messages)
  // toastr.success('Category added successfully!');
  // toastr.error('Failed to add category!');

  // Prefill Edit Modal
  $('#editCategoryModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const catName = button.data('name');
    const catImage = button.data('image');
    const modal = $(this);
    modal.find('#edit_category_name').val(catName);
    modal.find('input[name="category_id"]').val(catName); // replace with ID if you have it

    if(catImage) {
      modal.find('#editImagePreview').attr('src', 'uploads/' + catImage).show();
      modal.find('#editImageSize').text('');
    } else {
      modal.find('#editImagePreview').hide();
      modal.find('#editImageSize').text('');
    }
  });

  // Prefill Delete Modal
  $('#deleteCategoryModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const catName = button.data('name');
    $(this).find('#delete_category_name').text(catName);
    $(this).find('#delete_category_input').val(catName);
  });

  // Image preview & size on Add modal
  $('#addCategoryImage').change(function() {
    readImage(this, '#addImagePreview', '#addImageSize');
  });

  // Image preview & size on Edit modal
  $('#editCategoryImage').change(function() {
    readImage(this, '#editImagePreview', '#editImageSize');
  });

  function readImage(input, previewSelector, sizeSelector) {
    if (input.files && input.files[0]) {
      const file = input.files[0];
      if (!file.type.startsWith('image/')) {
        toastr.error('Please select a valid image file.');
        input.value = ''; // reset input
        $(previewSelector).hide();
        $(sizeSelector).text('');
        return;
      }
      const reader = new FileReader();
      reader.onload = function(e) {
        $(previewSelector).attr('src', e.target.result).show();
        $(sizeSelector).text('Size: ' + (file.size / 1024).toFixed(2) + ' KB');
      }
      reader.readAsDataURL(file);
    }
  }
</script>
