<?php
ob_start();
require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

global $pdo;

try {

    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $new_orders = $stmt->fetch(PDO::FETCH_ASSOC)['count'];


    $stmt = $pdo->prepare("
        SELECT SUM(total) as revenue
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        AND status = 'delivered'
    ");
    $stmt->execute();
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?? 0;


    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM users
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $new_users = $stmt->fetch(PDO::FETCH_ASSOC)['count'];


    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM products
        WHERE stock < 10
    ");
    $stmt->execute();
    $low_stock = $stmt->fetch(PDO::FETCH_ASSOC)['count'];


    $stmt = $pdo->prepare("
        SELECT DATE_FORMAT(created_at, '%b') as month, SUM(total) as total
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 8 MONTH)
        AND status = 'delivered'
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY created_at ASC
        LIMIT 8
    ");
    $stmt->execute();
    $sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $sales_labels = [];
    $sales_values = [];
    $months = array_slice(array_reverse(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']), 0, 8);
    $month_map = array_fill_keys($months, 0);
    
    foreach ($sales_data as $row) {
        $month_map[$row['month']] = (float)$row['total'];
    }
    
    foreach ($months as $month) {
        $sales_labels[] = $month;
        $sales_values[] = $month_map[$month];
    }


    $stmt = $pdo->prepare("
        SELECT a.division_name, COUNT(DISTINCT a.user_id) as count
        FROM addresses a
        JOIN users u ON a.user_id = u.id
        GROUP BY a.division_name
    ");
    $stmt->execute();
    $user_demographics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $user_labels = [];
    $user_values = [];
    
    foreach ($user_demographics as $row) {
        $user_labels[] = $row['division_name'];
        $user_values[] = (int)$row['count'];
    }
    

    if (empty($user_labels)) {
        $user_labels = ['No Data'];
        $user_values = [0];
    }
} catch (Exception $e) {
    $error_message = 'Error fetching dashboard data: ' . htmlspecialchars($e->getMessage());
    $log_file = './storage/error_log.txt';
  
    if (!is_dir('./storage')) {
        mkdir('./storage', 0755, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Dashboard error: " . $e->getMessage() . "\n", FILE_APPEND);
    $new_orders = 0;
    $revenue = 0;
    $new_users = 0;
    $low_stock = 0;
    $sales_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'];
    $sales_values = [0, 0, 0, 0, 0, 0, 0, 0];
    $user_labels = ['No Data'];
    $user_values = [0];
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Analytics Dashboard</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center"><?= $error_message ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $new_orders ?></h3>
                            <p>New Orders</p>
                        </div>
                        <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                        <a href="orders.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>৳<?= number_format($revenue, 2) ?></h3>
                            <p>Revenue</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                        <a href="products.php?status=delivered" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $new_users ?></h3>
                            <p>New Users</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-plus"></i></div>
                        <a href="users.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $low_stock ?></h3>
                            <p>Low Stock Products</p>
                        </div>
                        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <a href="products.php?stock=low" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row">
                <section class="col-lg-8 connectedSortable">
                    <div class="card chart-container">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Sales Over Time</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </section>

                <section class="col-lg-4 connectedSortable">
                    <div class="card chart-container">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> User Demographics by Division</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="userPieChart"></canvas>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</div>

<?php require_once './includes/__footer__.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($sales_labels); ?>,
                datasets: [{
                    label: 'Sales (৳)',
                    data: <?php echo json_encode($sales_values); ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales (৳)'
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

        const userPieCtx = document.getElementById('userPieChart').getContext('2d');
        const userPieChart = new Chart(userPieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($user_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($user_values); ?>,
                    backgroundColor: [
                        '#17a2b8', // Cyan
                        '#28a745', // Green
                        '#ffc107', // Yellow
                        '#dc3545', // Red
                        '#6f42c1', // Purple
                        '#fd7e14', // Orange
                        '#20c997', // Teal
                        '#007bff'  // Blue
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>

<?php ob_end_flush(); ?>
</body>
</html>