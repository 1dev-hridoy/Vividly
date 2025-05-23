<?php
require_once '../../server/dbcon.php';

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: /");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            // Login successful
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            
            // If remember me is checked
            if (isset($_POST['remember'])) {
                setcookie('admin_email', $email, time() + (86400 * 30), "/"); // 30 days
            }
            
            header("Location: /"); // Redirect to homepage/dashboard
            exit();
        } else {
            header("Location: login.php?error=Invalid credentials");
            exit();
        }
    } catch(PDOException $e) {
        header("Location: login.php?error=Database error");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b>My</b>Admin</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="post">
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" 
                           value="<?php echo isset($_COOKIE['admin_email']) ? htmlspecialchars($_COOKIE['admin_email']) : ''; ?>" 
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                </div>
            </form>

            <p class="mb-1 mt-3">
                <a href="#">I forgot my password</a>
            </p>
            <p class="mb-0">
                <a href="#" class="text-center">Register a new account</a>
            </p>
        </div>
    </div>
</div>

<!-- AdminLTE & jQuery -->
<script src="../../assets/plugins/jquery/jquery.min.js"></script>
<script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/adminlte.min.js"></script>
<script src="../../assets/js/demo.js"></script>
</body>
</html>