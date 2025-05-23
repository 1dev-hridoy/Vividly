<?php
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

$name = $email = $phone = $password = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$name) $errors['name'] = "Name is required";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Valid email required";
    if (!$phone) $errors['phone'] = "Phone number is required";
    if (!$password || strlen($password) < 6) $errors['password'] = "Password must be 6+ chars";

    if (empty($errors)) {
        // Insert into DB here (hash password!)
        // For now, just simulate success and redirect
        header("Location: users.php?added=1");
        exit;
    }
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Add New User</h1>
    <a href="users.php" class="btn btn-sm btn-secondary mb-3">← Back to Users</a>
  </section>

  <section class="content">
    <div class="container-fluid">
      <form method="POST" novalidate>
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($name) ?>" required>
          <div class="invalid-feedback"><?= $errors['name'] ?? '' ?></div>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($email) ?>" required>
          <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
        </div>

        <div class="form-group">
          <label>Phone</label>
          <input type="tel" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($phone) ?>" required>
          <div class="invalid-feedback"><?= $errors['phone'] ?? '' ?></div>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required>
          <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
        </div>

        <button type="submit" class="btn btn-success">Add User</button>
      </form>
    </div>
  </section>
</div>

<?php require_once './includes/__footer__.php'; ?>
