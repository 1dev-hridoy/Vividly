<?php
ob_start();
require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

global $pdo;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;
$offset = ($page - 1) * $perPage;

try {

    $sql = "
        SELECT id, CONCAT(first_name, ' ', last_name) AS name, email, phone, created_at AS registered
        FROM users
    ";
    $params = [];

    if ($search !== '') {
        $sql .= " WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?";
        $searchTerm = '%' . $search . '%';
        $params = [$searchTerm, $searchTerm, $searchTerm];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $total = $stmt->rowCount();
    $pages = ceil($total / $perPage);

    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $paramIndex = 1;
    foreach ($params as $param) {
        $stmt->bindValue($paramIndex++, $param, PDO::PARAM_STR);
    }
    $stmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = 'Error fetching users: ' . htmlspecialchars($e->getMessage());
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Users error: " . $e->getMessage() . "\n", FILE_APPEND);
    $users = [];
    $total = 0;
    $pages = 1;
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Users / Customers</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form method="GET" class="mb-3 d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-info">Search</button>
            </form>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center"><?= $error_message ?></div>
            <?php endif; ?>

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
                                    <td><?= htmlspecialchars(date('Y-m-d', strtotime($user['registered']))) ?></td>
                                    <td>
                                        <a href="user_details.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary" title="View"><i class="fas fa-eye"></i></a>
                                        
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
ob_end_flush();
?>