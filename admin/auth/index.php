<?php
require_once '../../server/dbcon.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: ../");
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
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['login_time'] = date('Y-m-d H:i:s'); 
            
            if (isset($_POST['remember'])) {
                setcookie('admin_email', $email, time() + (86400 * 30), "/"); 
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
            exit();
        }
    } catch(PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database error']);
        exit();
    }
}

$current_utc = gmdate('Y-m-d H:i:s');
$current_user = 'hridoy09bg'; 
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        .current-info {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b>My</b>Admin</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form id="loginForm" action="index.php" method="post">
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
        </div>
    </div>
</div>

<!-- Current Date/Time and User Info -->
<div class="current-info">
    <div>Current UTC: <?php echo $current_utc; ?></div>
    <div>User: <?php echo htmlspecialchars($current_user); ?></div>
</div>

<script src="../../assets/plugins/jquery/jquery.min.js"></script>
<script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    function updateUTCTime() {
        let now = new Date();
        let utcString = now.getUTCFullYear() + '-' + 
                        String(now.getUTCMonth() + 1).padStart(2, '0') + '-' +
                        String(now.getUTCDate()).padStart(2, '0') + ' ' +
                        String(now.getUTCHours()).padStart(2, '0') + ':' +
                        String(now.getUTCMinutes()).padStart(2, '0') + ':' +
                        String(now.getUTCSeconds()).padStart(2, '0');
        $('.current-info div:first-child').text('Current UTC: ' + utcString);
    }
    
    setInterval(updateUTCTime, 1000);
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $('#loginForm').on('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Logging in...',
            html: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success('Login successful!', 'Success');
                    
                    setTimeout(function() {
                        window.location.href = '../';
                    }, 2000);
                } else {
                    
                    Swal.close();
                    
                
                    toastr.error(response.error || 'An error occurred', 'Error');
                }
            },
            error: function() {
              
                Swal.close();
                toastr.error('An error occurred while processing your request', 'Error');
            }
        });
    });

    // Check for error parameter in URL and show Toastr message
    <?php if (isset($_GET['error'])): ?>
        toastr.error('<?php echo htmlspecialchars($_GET['error']); ?>', 'Error');
    <?php endif; ?>
});
</script>
</body>
</html>