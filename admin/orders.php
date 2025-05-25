<?php
ob_start();
require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

global $pdo;

$perPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$start = ($page - 1) * $perPage;

try {
    $sql = "
        SELECT o.custom_order_id AS id, CONCAT(u.first_name, ' ', u.last_name) AS customer, 
               o.status, o.total, o.created_at AS date
        FROM orders o
        JOIN users u ON o.user_id = u.id
    ";
    $params = [];
    
    if ($search !== '') {
        $sql .= " WHERE o.custom_order_id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR o.status LIKE ?";
        $searchTerm = '%' . $search . '%';
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $totalOrders = $stmt->rowCount();
    $totalPages = ceil($totalOrders / $perPage);
    
    $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    
    $paramIndex = 1;
    foreach ($params as $param) {
        $stmt->bindValue($paramIndex++, $param, PDO::PARAM_STR);
    }
    $stmt->bindValue($paramIndex++, (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, (int)$start, PDO::PARAM_INT);
    
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = 'Error fetching orders: ' . htmlspecialchars($e->getMessage());
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Orders error: " . $e->getMessage() . "\n", FILE_APPEND);
    $orders = [];
    $totalOrders = 0;
    $totalPages = 1;
}

function statusBadgeClass($status) {
    return match (strtolower($status)) {
        'completed' => 'badge badge-success',
        'pending' => 'badge badge-warning',
        'processing' => 'badge badge-info',
        'cancelled' => 'badge badge-danger',
        default => 'badge badge-secondary',
    };
}
?>

<div class="content-wrapper">
    <section class="content-header d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
        <h1>Order List</h1>
        <form class="form-inline" method="GET" action="">
            <input type="text" name="search" class="form-control mr-2 mb-2 mb-md-0" placeholder="Search Order ID, Customer, Status" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
    </section>

    <section class="content">
        <div class="container-fluid p-0">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center"><?= $error_message ?></div>
            <?php endif; ?>
            <div class="table-responsive shadow rounded bg-white">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#Order ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total (৳)</th>
                            <th>Date</th>
                            <th style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="6" class="text-center">No orders found.</td></tr>
                        <?php else: foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['customer']) ?></td>
                                <td><span class="<?= statusBadgeClass($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
                                <td>৳ <?= number_format($order['total'], 2) ?></td>
                                <td><?= htmlspecialchars(date('Y-m-d', strtotime($order['date']))) ?></td>
                                <td>
                                    <a href="./order?id=<?= urlencode($order['id']) ?>" class="btn btn-sm btn-primary" title="View"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center flex-wrap">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= max(1, $page-1) ?>&search=<?= urlencode($search) ?>">Previous</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= min($totalPages, $page+1) ?>&search=<?= urlencode($search) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
</div>

<?php
require_once './includes/__footer__.php';
ob_end_flush();
?>