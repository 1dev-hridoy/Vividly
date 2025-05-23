<?php
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';
?>

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

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Product List</h3>
          <div class="card-tools">
            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add New</a>
          </div>
        </div>

        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price (৳)</th>
                <th>Stock</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <!-- Dummy Rows -->
              <tr>
                <td><img src="assets/img/product1.jpg" alt="prod" width="50"></td>
                <td>iPhone 13 Pro</td>
                <td>Electronics</td>
                <td>৳120,000</td>
                <td><span class="badge badge-success">In Stock</span></td>
                <td>
                  <a href="#" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                  <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              <tr>
                <td><img src="assets/img/product2.jpg" alt="prod" width="50"></td>
                <td>Nike Air Max</td>
                <td>Footwear</td>
                <td>৳8,500</td>
                <td><span class="badge badge-warning">Low Stock</span></td>
                <td>
                  <a href="#" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                  <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              <tr>
                <td><img src="assets/img/product3.jpg" alt="prod" width="50"></td>
                <td>Samsung LED TV 55"</td>
                <td>Home Appliances</td>
                <td>৳65,000</td>
                <td><span class="badge badge-danger">Out of Stock</span></td>
                <td>
                  <a href="#" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                  <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              <!-- /Dummy Rows -->
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>
</div>

<?php require_once './includes/__footer__.php'; ?>
