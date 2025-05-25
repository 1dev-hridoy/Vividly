<?php
ob_start();
require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

global $pdo;
$orderId = $_GET['id'] ?? '';
if (empty($orderId)) {
    echo '<div class="content-wrapper"><section class="content p-4"><h3 class="text-danger">Order ID is required!</h3></section></div>';
    require_once './includes/__footer__.php';
    ob_end_flush();
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT o.custom_order_id, o.status, o.created_at AS date, o.total,
               CONCAT(u.first_name, ' ', u.last_name) AS customer,
               u.email, u.phone,
               a.street_address, a.city, a.division_name, a.district_name, a.upzila_name, a.union_name, a.postal_code
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN addresses a ON o.shipping_address_id = a.id
        WHERE o.custom_order_id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo '<div class="content-wrapper"><section class="content p-4"><h3 class="text-danger">Order not found!</h3></section></div>';
        require_once './includes/__footer__.php';
        ob_end_flush();
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT p.title AS name, oi.quantity AS qty, oi.unit_price AS price,
               c.name AS color, s.label AS size
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN colors c ON oi.color_id = c.id
        LEFT JOIN sizes s ON oi.size_id = s.id
        WHERE oi.order_id = (SELECT id FROM orders WHERE custom_order_id = ?)
    ");
    $stmt->execute([$orderId]);
    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error_message = 'Error fetching order: ' . htmlspecialchars($e->getMessage());
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Order error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo '<div class="content-wrapper"><section class="content p-4"><h3 class="text-danger">' . $error_message . '</h3></section></div>';
    require_once './includes/__footer__.php';
    ob_end_flush();
    exit;
}

$successMsg = '';
$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = strtolower(trim($_POST['status'])); 
    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    try {
        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception('Invalid status selected: ' . htmlspecialchars($newStatus));
        }
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE custom_order_id = ?");
        $stmt->execute([$newStatus, $orderId]);
        $order['status'] = $newStatus;
        $successMsg = "Order status updated to <strong>" . ucfirst($newStatus) . "</strong>";
    } catch (Exception $e) {
        $errorMsg = 'Error updating status: ' . htmlspecialchars($e->getMessage());
        $log_file = '../storage/error_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] Status update error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

$totalPrice = 0;
foreach ($order['items'] as $item) {
    $totalPrice += $item['qty'] * $item['price'];
}

function statusBadge($status) {
    return match (strtolower($status)) {
        'delivered' => 'badge badge-success',
        'pending' => 'badge badge-warning',
        'processing' => 'badge badge-info',
        'shipped' => 'badge badge-primary',
        'cancelled' => 'badge badge-danger',
        default => 'badge badge-secondary',
    };
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Order Details - <?= htmlspecialchars($orderId) ?></h1>
            <a href="orders.php" class="btn btn-secondary btn-sm">← Back to Orders</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (!empty($successMsg)): ?>
                <script>
                    toastr.success("<?= $successMsg ?>");
                </script>
            <?php endif; ?>
            <?php if (!empty($errorMsg)): ?>
                <script>
                    toastr.error("<?= $errorMsg ?>");
                </script>
            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            Customer Information
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> <?= htmlspecialchars($order['customer']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                            <p><strong>Address:</strong> <?= htmlspecialchars(
                                ($order['street_address'] ?? '') . ', ' .
                                ($order['city'] ?? '') . ', ' .
                                ($order['upzila_name'] ?? '') . ', ' .
                                ($order['district_name'] ?? '') . ', ' .
                                ($order['division_name'] ?? '') . ' ' .
                                ($order['postal_code'] ?? '')
                            ) ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            Order Info
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label for="statusSelect">Order Status</label>
                                    <select name="status" id="statusSelect" class="form-control">
                                        <?php
                                        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                                        foreach ($statuses as $s) {
                                            $sel = strtolower($order['status']) === $s ? 'selected' : '';
                                            echo "<option value=\"$s\" $sel>" . ucfirst($s) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <p><strong>Order Date:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($order['date']))) ?></p>
                                <button type="submit" class="btn btn-primary btn-sm">Save Status</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    Ordered Items
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price (৳)</th>
                                <th>Subtotal (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item):
                                $subtotal = $item['qty'] * $item['price'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name'] . ($item['color'] ? ' (' . $item['color'] . ')' : '') . ($item['size'] ? ' (' . $item['size'] . ')' : '')) ?></td>
                                <td><?= $item['qty'] ?></td>
                                <td><?= number_format($item['price'], 2) ?></td>
                                <td><?= number_format($subtotal, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total:</th>
                                <th>৳ <?= number_format($totalPrice, 2) ?></th>
                            </tr>
                        </tfoot>
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