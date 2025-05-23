<?php
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Add New Product</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active">Add Product</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <form method="POST" enctype="multipart/form-data">
      <div class="container-fluid">
        <div class="row">
          <!-- Product Form -->
          <div class="col-md-8">
            <div class="card card-primary">
              <div class="card-header"><h3 class="card-title">Product Details</h3></div>
              <div class="card-body">

                <div class="form-group">
                  <label>Product Title</label>
                  <input type="text" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                  <label>Short Description</label>
                  <textarea name="short_desc" class="form-control" rows="2" required></textarea>
                </div>

                <div class="form-group">
                  <label>Long Description</label>
                  <textarea name="long_desc" class="form-control" rows="5" required></textarea>
                </div>

                <div class="form-group">
  <label>Available Sizes</label>
  <div class="row">
    <?php
    $sizes = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL', '6XL', '7XL', '8XL'];
    foreach ($sizes as $size) {
      echo '
      <div class="col-6 col-md-4 mb-2">
        <div class="custom-control custom-switch">
          <input type="checkbox" class="custom-control-input" id="size_' . $size . '" name="size[]" value="' . $size . '">
          <label class="custom-control-label" for="size_' . $size . '">' . $size . '</label>
        </div>
      </div>';
    }
    ?>
  </div>
</div>


                <div class="form-group">
                  <label>Colors (Max 4)</label>
                  <input type="text" name="color[]" class="form-control mb-2" placeholder="e.g. Red">
                  <input type="text" name="color[]" class="form-control mb-2" placeholder="e.g. Blue">
                  <input type="text" name="color[]" class="form-control mb-2" placeholder="e.g. Black">
                  <input type="text" name="color[]" class="form-control" placeholder="e.g. White">
                </div>

                <div class="form-group">
                  <label>Category</label>
                  <select name="category" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <option>Clothing</option>
                    <option>Accessories</option>
                    <option>Electronics</option>
                    <option>Home & Living</option>
                  </select>
                </div>

              </div>
            </div>
          </div>

          <!-- Product Images -->
          <div class="col-md-4">
            <div class="card card-info">
              <div class="card-header"><h3 class="card-title">Product Images</h3></div>
              <div class="card-body">

                <div class="form-group">
                  <label>Main Image</label>
                  <input type="file" name="main_image" class="form-control" accept="image/*" onchange="previewImage(event, 'mainPreview')" required>
                  <div class="img-preview-wrapper">
                    <img id="mainPreview" class="img-preview" />
                  </div>
                </div>

                <div class="form-group">
                  <label>Additional Image 1</label>
                  <input type="file" name="more_images[]" class="form-control" accept="image/*" onchange="previewImage(event, 'more1')">
                  <div class="img-preview-wrapper">
                    <img id="more1" class="img-preview" />
                  </div>
                </div>

                <div class="form-group">
                  <label>Additional Image 2</label>
                  <input type="file" name="more_images[]" class="form-control" accept="image/*" onchange="previewImage(event, 'more2')">
                  <div class="img-preview-wrapper">
                    <img id="more2" class="img-preview" />
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="row">
          <div class="col-12 text-right">
            <button type="submit" class="btn btn-success btn-lg">Add Product</button>
          </div>
        </div>
      </div>
    </form>
  </section>
</div>

<?php require_once './includes/__footer__.php'; ?>

<!-- 💅 Style for image previews -->
<style>
  .img-preview-wrapper {
    margin-top: 10px;
    width: 150px;
    height: 150px;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .img-preview {
    max-width: 100%;
    max-height: 100%;
    display: none;
    object-fit: cover;
  }
</style>

<!-- 🧠 Preview script -->
<script>
  function previewImage(event, id) {
    const file = event.target.files[0];
    if (!file.type.startsWith('image/')) {
      alert("Only image files are allowed!");
      event.target.value = "";
      return;
    }

    const reader = new FileReader();
    reader.onload = function () {
      const img = document.getElementById(id);
      img.src = reader.result;
      img.style.display = 'block';
    }
    reader.readAsDataURL(file);
  }
</script>
