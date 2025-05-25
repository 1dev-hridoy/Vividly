<?php
ob_start();

require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

global $pdo;

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS count FROM orders WHERE status IN ('pending', 'processing')");
    $newOrders = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $stmt = $pdo->query("SELECT SUM(total) AS revenue FROM orders WHERE status IN ('delivered', 'shipped')");
    $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?? 0;
    $stmt = $pdo->query("SELECT COUNT(*) AS count FROM users");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $stmt = $pdo->query("SELECT COUNT(*) AS count FROM products");
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $stmt = $pdo->prepare("
        SELECT o.custom_order_id, CONCAT(u.first_name, ' ', u.last_name) AS customer, o.total, o.status, o.created_at
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total) AS revenue
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        AND status IN ('delivered', 'shipped')
        GROUP BY month
        ORDER BY month
    ");
    $stmt->execute();
    $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

  
    $labels = [];
    $data = [];
    $currentMonth = new DateTime('now', new DateTimeZone('Asia/Dhaka')); 
    for ($i = 5; $i >= 0; $i--) {
        $month = (clone $currentMonth)->modify("-$i months");
        $monthStr = $month->format('Y-m');
        $labels[] = $month->format('M Y');
        $revenue = 0;
        foreach ($salesData as $row) {
        if ($row['month'] === $monthStr) {
            $revenue = $row['revenue'] ?? 0;
            break;
        }
        }
        $data[] = $revenue;
    }

} catch (Exception $e) {
    $error_message = 'Error fetching dashboard data: ' . htmlspecialchars($e->getMessage());
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Dashboard error: " . $e->getMessage() . "\n", FILE_APPEND);
    $newOrders = $totalRevenue = $totalUsers = $totalProducts = 0;
    $orders = [];
}
?>

    <style>

        #salesChart {
           width: auto !important; /* Responsive width */
            height: 400px !important; /* Fixed height for the chart */
        }
    </style>


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
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center"><?= $error_message ?></div>
            <?php endif; ?>

            <!-- Stat boxes -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $newOrders ?></h3>
                            <p>New Orders</p>
                        </div>
                        <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                        <a href="orders.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>৳<?= number_format($totalRevenue, 2) ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                        <a href="orders.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $totalUsers ?></h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="users.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $totalProducts ?></h3>
                            <p>Total Products</p>
                        </div>
                        <div class="icon"><i class="fas fa-box-open"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Sales Chart -->
            <section class="col-lg-12 connectedSortable">
                <div class="card chart-container">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Sales Overview</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
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
                                    <?php if (empty($orders)): ?>
                                        <tr><td colspan="5" class="text-center text-muted">No orders found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($orders as $order):
                                            $badgeClass = match (strtolower($order['status'])) {
                                                'pending' => 'badge-warning',
                                                'processing' => 'badge-info',
                                                'shipped' => 'badge-primary',
                                                'delivered' => 'badge-success',
                                                'cancelled' => 'badge-danger',
                                                default => 'badge-secondary',
                                            };
                                        ?>
                                            <tr>
                                                <td><a href="order.php?id=<?= urlencode($order['custom_order_id']) ?>"><?= htmlspecialchars($order['custom_order_id']) ?></a></td>
                                                <td><?= htmlspecialchars($order['customer']) ?></td>
                                                <td>৳<?= number_format($order['total'], 2) ?></td>
                                                <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($order['status']) ?></span></td>
                                                <td><?= htmlspecialchars(date('Y-m-d', strtotime($order['created_at']))) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Revenue (৳)',
                    data: <?php echo json_encode($data); ?>,
                    fill: false,
                    borderColor: '#007bff',
                    backgroundColor: '#007bff',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (৳)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    });
</script>

<?php
require_once './includes/__footer__.php';
ob_end_flush();
?>