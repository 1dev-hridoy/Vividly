<?php
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Analytics Dashboard</h1>
  </section>

  <section class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>250</h3>
              <p>New Orders</p>
            </div>
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>৳120K</h3>
              <p>Revenue</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>80</h3>
              <p>New Users</p>
            </div>
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>15</h3>
              <p>Low Stock Products</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="row">
        <section class="col-lg-8 connectedSortable">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Sales Over Time</h3>
            </div>
            <div class="card-body">
              <canvas id="salesChart" style="height: 300px;"></canvas>
            </div>
          </div>
        </section>

        <section class="col-lg-4 connectedSortable">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> User Demographics</h3>
            </div>
            <div class="card-body">
              <canvas id="userPieChart" style="height: 300px;"></canvas>
            </div>
          </div>
        </section>
      </div>

    </div>
  </section>
</div>

<?php require_once './includes/__footer__.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const salesCtx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
      datasets: [{
        label: 'Sales (৳)',
        data: [12000, 19000, 30000, 25000, 40000, 35000, 45000, 50000],
        borderColor: '#007bff',
        backgroundColor: 'rgba(0,123,255,0.1)',
        fill: true,
        tension: 0.3,
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      },
      plugins: {
        legend: { display: true }
      }
    }
  });

  const userPieCtx = document.getElementById('userPieChart').getContext('2d');
  const userPieChart = new Chart(userPieCtx, {
    type: 'pie',
    data: {
      labels: ['Male', 'Female', 'Other'],
      datasets: [{
        data: [55, 40, 5],
        backgroundColor: ['#17a2b8', '#28a745', '#ffc107'],
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });
</script>
