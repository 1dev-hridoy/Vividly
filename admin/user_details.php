<?php
ob_start();
require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

global $pdo;
$userId = $_GET['id'] ?? '';
if (empty($userId) || !is_numeric($userId)) {
    echo '<div class="content-wrapper"><section class="content p-4"><h3 class="text-danger">Invalid user ID!</h3></section></div>';
    require_once './includes/__footer__.php';
    ob_end_flush();
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, CONCAT(first_name, ' ', last_name) AS name, email, phone, created_at AS registered
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo '<div class="content-wrapper"><section class="content p-4"><h3 class="text-danger">User not found!</h3></section></div>';
        require_once './includes/__footer__.php';
        ob_end_flush();
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT address_type, street_address, city, division_name, district_name, upzila_name, union_name, postal_code
        FROM addresses
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT custom_order_id, status, total, created_at
        FROM orders
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalOrders = count($orders);

    $stmt = $pdo->prepare("
        SELECT p.payment_method, p.transaction_id, p.mobile_number, p.amount, p.status, p.created_at
        FROM payments p
        JOIN orders o ON p.order_id = o.id
        WHERE o.user_id = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$userId]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error_message = 'Error fetching user details: ' . htmlspecialchars($e->getMessage());
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] User details error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo '<div class="content-wrapper"><section class="content p-4"><h3 class="text-danger">' . $error_message . '</h3></section></div>';
    require_once './includes/__footer__.php';
    ob_end_flush();
    exit;
}

function statusBadge($status) {
    return match (strtolower($status)) {
        'delivered' => 'badge badge-success',
        'pending' => 'badge badge-warning',
        'processing' => 'badge badge-info',
        'shipped' => 'badge badge-primary',
        'cancelled' => 'badge badge-danger',
        'completed' => 'badge badge-success',
        'failed' => 'badge badge-danger',
        default => 'badge badge-secondary',
    };
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>User Details - <?= htmlspecialchars($user['name']) ?></h1>
            <a href="users.php" class="btn btn-secondary btn-sm">← Back to Users</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            User Information
                        </div>
                        <div class="card-body">
                            <p><strong>ID:</strong> <?= $user['id'] ?></p>
                            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                            <p><strong>Registered:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($user['registered']))) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            Summary
                        </div>
                        <div class="card-body">
                            <p><strong>Total Orders:</strong> <?= $totalOrders ?></p>
                            <p><strong>Total Addresses:</strong> <?= count($addresses) ?></p>
                            <p><strong>Total Payments:</strong> <?= count($payments) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    Addresses
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($addresses)): ?>
                                <tr><td colspan="2" class="text-center text-muted">No addresses found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($addresses as $address): ?>
                                    <tr>
                                        <td><?= ucfirst($address['address_type']) ?></td>
                                        <td><?= htmlspecialchars(
                                            ($address['street_address'] ?? '') . ', ' .
                                            ($address['city'] ?? '') . ', ' .
                                            ($address['upzila_name'] ?? '') . ', ' .
                                            ($address['district_name'] ?? '') . ', ' .
                                            ($address['division_name'] ?? '') . ' ' .
                                            ($address['postal_code'] ?? '')
                                        ) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-warning text-white">
                    Orders
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Total (৳)</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr><td colspan="4" class="text-center text-muted">No orders found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><a href="order.php?id=<?= urlencode($order['custom_order_id']) ?>"><?= htmlspecialchars($order['custom_order_id']) ?></a></td>
                                        <td><span class="<?= statusBadge($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                                        <td><?= number_format($order['total'], 2) ?></td>
                                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($order['created_at']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    Payment History
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Transaction ID</th>
                                <th>Mobile</th>
                                <th>Amount (৳)</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payments)): ?>
                                <tr><td colspan="6" class="text-center text-muted">No payments found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= ucfirst($payment['payment_method']) ?></td>
                                        <td><?= htmlspecialchars($payment['transaction_id'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($payment['mobile_number'] ?? '-') ?></td>
                                        <td><?= number_format($payment['amount'], 2) ?></td>
                                        <td><span class="<?= statusBadge($payment['status']) ?>"><?= ucfirst($payment['status']) ?></span></td>
                                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($payment['created_at']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
require_once './includes/__footer__.php';
ob_end_flush();
?>