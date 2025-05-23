<?php
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

// Dummy user data (normally from DB)
$users = [
  ['id' => 1, 'name' => 'Hridoy', 'email' => 'hridoy@example.com', 'phone' => '+880123456789', 'registered' => '2024-12-01'],
  ['id' => 2, 'name' => 'Rony', 'email' => 'rony@example.com', 'phone' => '+880987654321', 'registered' => '2025-01-15'],
  ['id' => 3, 'name' => 'Sadia', 'email' => 'sadia@example.com', 'phone' => '+880112233445', 'registered' => '2025-02-20'],
  ['id' => 4, 'name' => 'Fahim', 'email' => 'fahim@example.com', 'phone' => '+880998877665', 'registered' => '2025-03-05'],
  ['id' => 5, 'name' => 'Tania', 'email' => 'tania@example.com', 'phone' => '+880556677889', 'registered' => '2025-04-10'],
  ['id' => 6, 'name' => 'Rana', 'email' => 'rana@example.com', 'phone' => '+880667788990', 'registered' => '2025-04-21'],
  ['id' => 7, 'name' => 'Jahid', 'email' => 'jahid@example.com', 'phone' => '+880778899001', 'registered' => '2025-05-01'],
  ['id' => 8, 'name' => 'Nila', 'email' => 'nila@example.com', 'phone' => '+880889900112', 'registered' => '2025-05-12'],
  ['id' => 9, 'name' => 'Raju', 'email' => 'raju@example.com', 'phone' => '+880990011223', 'registered' => '2025-05-18'],
  ['id' => 10, 'name' => 'Mita', 'email' => 'mita@example.com', 'phone' => '+880101112131', 'registered' => '2025-05-20'],
  ['id' => 11, 'name' => 'Sami', 'email' => 'sami@example.com', 'phone' => '+880202122232', 'registered' => '2025-05-22'],
];

// Search filter
$search = $_GET['search'] ?? '';
if ($search) {
  $users = array_filter($users, fn($u) => str_contains(strtolower($u['name']), strtolower($search)) || str_contains(strtolower($u['email']), strtolower($search)));
}

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;
$total = count($users);
$pages = ceil($total / $perPage);
$offset = ($page - 1) * $perPage;
$users = array_slice($users, $offset, $perPage);
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1>Users / Customers</h1>
      <a href="#" class="btn btn-primary btn-sm">Add New User</a>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-info">Search</button>
      </form>

      <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered">
          <thead class="thead-dark">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Registered Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr><td colspan="6" class="text-center text-muted">No users found.</td></tr>
            <?php else: ?>
              <?php foreach ($users as $user): ?>
                <tr>
                  <td><?= $user['id'] ?></td>
                  <td><?= htmlspecialchars($user['name']) ?></td>
                  <td><?= htmlspecialchars($user['email']) ?></td>
                  <td><?= htmlspecialchars($user['phone']) ?></td>
                  <td><?= htmlspecialchars($user['registered']) ?></td>
                  <td>
                    <a href="user_edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete this user?')){ window.location='user_delete.php?id=<?= $user['id'] ?>'; }">Delete</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <nav>
        <ul class="pagination justify-content-center">
          <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">Previous</a>
          </li>
          <?php for ($p = 1; $p <= $pages; $p++): ?>
            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
              <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $p ?>"><?= $p ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next</a>
          </li>
        </ul>
      </nav>
    </div>
  </section>
</div>

<?php
require_once './includes/__footer__.php';
?>
