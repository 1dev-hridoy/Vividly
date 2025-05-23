<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Account - eCommerce</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container py-5">
    <h2 class="mb-4">👤 My Account</h2>

    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3 mb-3">
        <div class="list-group">
          <a href="#" class="list-group-item list-group-item-action active">Profile</a>
          <a href="#" class="list-group-item list-group-item-action">Orders</a>
          <a href="#" class="list-group-item list-group-item-action">Address</a>
          <a href="#" class="list-group-item list-group-item-action">Change Password</a>
          <a href="#" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-9">
        <!-- Profile Card -->
        <div class="card mb-4">
          <div class="card-header">👤 Profile Info</div>
          <div class="card-body">
            <p><strong>Name:</strong> John Doe</p>
            <p><strong>Email:</strong> johndoe@example.com</p>
            <p><strong>Phone:</strong> +880 1234 567890</p>
            <a href="#" class="btn btn-primary btn-sm">Edit Profile</a>
          </div>
        </div>

        <!-- Orders -->
        <div class="card mb-4">
          <div class="card-header">📦 Recent Orders</div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr>
                    <th>#OrderID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>#1001</td>
                    <td>2025-05-21</td>
                    <td><span class="badge bg-success">Delivered</span></td>
                    <td>৳850</td>
                    <td><a href="#" class="btn btn-info btn-sm">View</a></td>
                  </tr>
                  <tr>
                    <td>#1002</td>
                    <td>2025-05-18</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td>৳1,200</td>
                    <td><a href="#" class="btn btn-info btn-sm">View</a></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Address -->
        <div class="card">
          <div class="card-header">🏠 Shipping Address</div>
          <div class="card-body">
            <p>123, Gulshan 2, Dhaka, Bangladesh</p>
            <a href="#" class="btn btn-primary btn-sm">Edit Address</a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
