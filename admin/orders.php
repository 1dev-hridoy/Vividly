<?php
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

$allOrders = [
  ['id' => 'ORD001', 'customer' => 'Hridoy', 'status' => 'Completed', 'total' => 2500, 'date' => '2025-05-22'],
  ['id' => 'ORD002', 'customer' => 'Rony', 'status' => 'Pending', 'total' => 1200, 'date' => '2025-05-21'],
  ['id' => 'ORD003', 'customer' => 'Rafi', 'status' => 'Cancelled', 'total' => 0, 'date' => '2025-05-20'],
  ['id' => 'ORD004', 'customer' => 'Tanzim', 'status' => 'Processing', 'total' => 3200, 'date' => '2025-05-19'],
  ['id' => 'ORD005', 'customer' => 'Sabbir', 'status' => 'Completed', 'total' => 2800, 'date' => '2025-05-18'],
  ['id' => 'ORD006', 'customer' => 'Nashit', 'status' => 'Pending', 'total' => 1100, 'date' => '2025-05-17'],
  ['id' => 'ORD007', 'customer' => 'Fahim', 'status' => 'Completed', 'total' => 4500, 'date' => '2025-05-16'],
  ['id' => 'ORD008', 'customer' => 'Sami', 'status' => 'Processing', 'total' => 3800, 'date' => '2025-05-15'],
  ['id' => 'ORD009', 'customer' => 'Ibrahim', 'status' => 'Pending', 'total' => 2100, 'date' => '2025-05-14'],
  ['id' => 'ORD010', 'customer' => 'Rakib', 'status' => 'Completed', 'total' => 3000, 'date' => '2025-05-13'],
  ['id' => 'ORD011', 'customer' => 'Jony', 'status' => 'Pending', 'total' => 3500, 'date' => '2025-05-12'],
  ['id' => 'ORD012', 'customer' => 'Salman', 'status' => 'Cancelled', 'total' => 0, 'date' => '2025-05-11'],
];

$perPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$filteredOrders = array_filter($allOrders, fn($o) => $search === '' || str_contains(strtolower($o['id']), strtolower($search)) || str_contains(strtolower($o['customer']), strtolower($search)) || str_contains(strtolower($o['status']), strtolower($search)));

$totalOrders = count($filteredOrders);
$totalPages = ceil($totalOrders / $perPage);
$start = ($page - 1) * $perPage;
$orders = array_slice($filteredOrders, $start, $perPage);

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
      <div class="table-responsive shadow rounded bg-white">
        <table class="table table-bordered table-hover mb-0">
          <thead class="thead-dark">
            <tr>
              <th>#Order ID</th>
              <th>Customer</th>
              <th>Status</th>
              <th>Total (৳)</th>
              <th>Date</th>
              <th style="width: 15%;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$orders): ?>
              <tr><td colspan="6" class="text-center">No orders found.</td></tr>
            <?php else: foreach ($orders as $order): ?>
              <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['customer']) ?></td>
                <td><span class="<?= statusBadgeClass($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
                <td>৳ <?= number_format($order['total'], 2) ?></td>
                <td><?= htmlspecialchars($order['date']) ?></td>
                <td>
                  <button class="btn btn-sm btn-primary" title="View"><i class="fas fa-eye"></i></button>
                  <button class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
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

          <?php for ($i=1; $i<=$totalPages; $i++): ?>
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
?>
