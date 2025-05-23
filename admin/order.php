<?php
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

$orderId = $_GET['id'] ?? 'ORD001';

$orders = [
  'ORD001' => [
    'customer' => 'Hridoy',
    'email' => 'hridoy@example.com',
    'phone' => '+880123456789',
    'address' => 'Dhaka, Bangladesh',
    'status' => 'Completed',
    'date' => '2025-05-22',
    'items' => [
      ['name' => 'Product A', 'qty' => 2, 'price' => 1200],
      ['name' => 'Product B', 'qty' => 1, 'price' => 1100],
    ],
  ],
  'ORD002' => [
    'customer' => 'Rony',
    'email' => 'rony@example.com',
    'phone' => '+880987654321',
    'address' => 'Chittagong, Bangladesh',
    'status' => 'Pending',
    'date' => '2025-05-21',
    'items' => [
      ['name' => 'Product C', 'qty' => 3, 'price' => 400],
      ['name' => 'Product D', 'qty' => 2, 'price' => 800],
    ],
  ],
];

if (!isset($orders[$orderId])) {
  echo '<div class="content-wrapper"><section class="content p-4"><h3 class="text-danger">Order not found!</h3></section></div>';
  require_once './includes/__footer__.php';
  exit;
}

// Simulate DB update on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
  $newStatus = $_POST['status'];
  $validStatuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
  if (in_array($newStatus, $validStatuses)) {
    $orders[$orderId]['status'] = $newStatus;
    $successMsg = "Order status updated to <strong>$newStatus</strong>";
  } else {
    $errorMsg = "Invalid status selected.";
  }
}

$order = $orders[$orderId];
$totalPrice = 0;

function statusBadge($status) {
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
  <section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1>Order Details - <?= htmlspecialchars($orderId) ?></h1>
      <a href="order_list.php" class="btn btn-secondary btn-sm">← Back to Orders</a>
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
              <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
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
                    $statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
                    foreach ($statuses as $s) {
                      $sel = $order['status'] === $s ? 'selected' : '';
                      echo "<option value=\"$s\" $sel>$s</option>";
                    }
                    ?>
                  </select>
                </div>
                <p><strong>Order Date:</strong> <?= htmlspecialchars($order['date']) ?></p>
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
                $totalPrice += $subtotal;
              ?>
              <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
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
?>
