<?php
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Dashboard</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <!-- Stat boxes -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>150</h3>
              <p>New Orders</p>
            </div>
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>৳53,000</h3>
              <p>Total Revenue</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>1,234</h3>
              <p>Visitors</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>67</h3>
              <p>Total Products</p>
            </div>
            <div class="icon"><i class="fas fa-box-open"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <section class="col-lg-12 connectedSortable">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Sales Overview</h3>
    </div>
    <div class="card-body">
      <canvas id="salesChart" height="100"></canvas>
    </div>
  </div>
</section>


      <!-- Orders Table -->
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header bg-primary">
              <h3 class="card-title text-white"><i class="fas fa-receipt"></i> Last 10 Orders</h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Dummy loop — replace with DB data
                  for ($i = 1; $i <= 10; $i++) {
                    $statuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
                    $status = $statuses[array_rand($statuses)];
                    $badgeClass = match ($status) {
                      'Pending' => 'badge-warning',
                      'Shipped' => 'badge-info',
                      'Delivered' => 'badge-success',
                      'Cancelled' => 'badge-danger',
                    };
                    echo "<tr>
                      <td>#ORD10$i</td>
                      <td>Customer $i</td>
                      <td>৳" . rand(500, 5000) . "</td>
                      <td><span class='badge $badgeClass'>$status</span></td>
                      <td>" . date('Y-m-d', strtotime("-$i days")) . "</td>
                    </tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php
require_once './includes/__footer__.php';
?>
