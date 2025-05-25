<?php
ob_start();
require_once './includes/check_auth.php';
require_once './includes/__navbar__.php';
require_once './includes/__side_bar__.php';

$settings = [
    'site' => ['site_name' => '', 'site_description' => '', 'site_logo' => '', 'footer_tagline' => ''],
    'social' => ['facebook_url' => '', 'twitter_url' => '', 'instagram_url' => '', 'linkedin_url' => ''],
    'contact' => ['phone_number' => '', 'address' => ''],
    'payment' => [
        'bkash_number' => '', 'bkash_note' => '',
        'nagad_number' => '', 'nagad_note' => '',
        'rocket_number' => '', 'rocket_note' => ''
    ],
    'admin' => ['name' => '', 'email' => '']
];
$error_message = '';
$success_message = '';

try {
    require_once '../server/dbcon.php'; 


    $stmt = $pdo->query("SELECT * FROM site_settings LIMIT 1");
    $settings['site'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: $settings['site'];
    $stmt = $pdo->query("SELECT * FROM social_links LIMIT 1");
    $settings['social'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: $settings['social'];
    $stmt = $pdo->query("SELECT * FROM contact_info LIMIT 1");
    $settings['contact'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: $settings['contact'];
    $stmt = $pdo->query("SELECT * FROM payment_info LIMIT 1");
    $settings['payment'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: $settings['payment'];
    $stmt = $pdo->prepare("SELECT name, email FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $settings['admin'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: $settings['admin'];
} catch (Exception $e) {
    $error_message = 'Error fetching settings: ' . htmlspecialchars($e->getMessage());
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Settings fetch error: " . $e->getMessage() . "\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once '../server/dbcon.php'; 
        $pdo->beginTransaction();

      
        $site_name = trim($_POST['siteName'] ?? '');
        $site_desc = trim($_POST['siteDesc'] ?? '');
        $footer_tagline = trim($_POST['footerTagline'] ?? '');
        $facebook_link = filter_var(trim($_POST['facebookLink'] ?? ''), FILTER_VALIDATE_URL) ?: '';
        $twitter_link = filter_var(trim($_POST['twitterLink'] ?? ''), FILTER_VALIDATE_URL) ?: '';
        $instagram_link = filter_var(trim($_POST['instagramLink'] ?? ''), FILTER_VALIDATE_URL) ?: '';
        $linkedin_link = filter_var(trim($_POST['linkedinLink'] ?? ''), FILTER_VALIDATE_URL) ?: '';
        $contact_phone = trim($_POST['contactPhone'] ?? '');
        $contact_address = trim($_POST['contactAddress'] ?? '');
        $bkash_number = trim($_POST['bkashNumber'] ?? '');
        $bkash_note = trim($_POST['bkashNote'] ?? '');
        $nagad_number = trim($_POST['nagadNumber'] ?? '');
        $nagad_note = trim($_POST['nagadNote'] ?? '');
        $rocket_number = trim($_POST['rocketNumber'] ?? '');
        $rocket_note = trim($_POST['rocketNote'] ?? '');
        $admin_name = trim($_POST['adminName'] ?? '');
        $admin_email = filter_var(trim($_POST['adminEmail'] ?? ''), FILTER_VALIDATE_EMAIL);
        $admin_password = $_POST['adminPassword'] ?? '';
        $admin_password_confirm = $_POST['adminPasswordConfirm'] ?? '';

     
        if (!$site_name || !$admin_name || !$admin_email) {
            throw new Exception('Site name, admin name, and admin email are required.');
        }

       
        if ($admin_password || $admin_password_confirm) {
            if ($admin_password !== $admin_password_confirm) {
                throw new Exception('Passwords do not match.');
            }
            if (strlen($admin_password) < 8) {
                throw new Exception('Password must be at least 8 characters.');
            }
        }

        $logo_path = $settings['site']['site_logo'] ?? '';
        if (isset($_FILES['siteLogo']) && $_FILES['siteLogo']['size'] > 0) {
            $file = $_FILES['siteLogo'];
            if (!in_array($file['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
                throw new Exception('Only JPEG, PNG, or GIF images are allowed.');
            }
            if ($file['size'] > 2 * 1024 * 1024) {
                throw new Exception('Logo file size must be under 2MB.');
            }

            $upload_dir = '../storage/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $filename = 'logo_' . date('Ymd_His') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $destination = $upload_dir . $filename;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new Exception('Failed to upload logo.');
            }
            $logo_path = $destination;
        }

     
        if ($settings['site']['id'] ?? false) {
            $stmt = $pdo->prepare("
                UPDATE site_settings SET
                    site_name = ?, site_description = ?, site_logo = ?, footer_tagline = ?
                WHERE id = ?
            ");
            $stmt->execute([$site_name, $site_desc, $logo_path, $footer_tagline, $settings['site']['id']]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO site_settings (site_name, site_description, site_logo, footer_tagline)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$site_name, $site_desc, $logo_path, $footer_tagline]);
        }

 
        if ($settings['social']['id'] ?? false) {
            $stmt = $pdo->prepare("
                UPDATE social_links SET
                    facebook_url = ?, twitter_url = ?, instagram_url = ?, linkedin_url = ?
                WHERE id = ?
            ");
            $stmt->execute([$facebook_link, $twitter_link, $instagram_link, $linkedin_link, $settings['social']['id']]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO social_links (facebook_url, twitter_url, instagram_url, linkedin_url)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$facebook_link, $twitter_link, $instagram_link, $linkedin_link]);
        }


        if ($settings['contact']['id'] ?? false) {
            $stmt = $pdo->prepare("
                UPDATE contact_info SET
                    phone_number = ?, address = ?
                WHERE id = ?
            ");
            $stmt->execute([$contact_phone, $contact_address, $settings['contact']['id']]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO contact_info (phone_number, address)
                VALUES (?, ?)
            ");
            $stmt->execute([$contact_phone, $contact_address]);
        }
        if ($settings['payment']['id'] ?? false) {
            $stmt = $pdo->prepare("
                UPDATE payment_info SET
                    bkash_number = ?, bkash_note = ?, nagad_number = ?, nagad_note = ?,
                    rocket_number = ?, rocket_note = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $bkash_number, $bkash_note, $nagad_number, $nagad_note,
                $rocket_number, $rocket_note,
                $settings['payment']['id']
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO payment_info (bkash_number, bkash_note, nagad_number, nagad_note,
                rocket_number, rocket_note)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $bkash_number, $bkash_note, $nagad_number, $nagad_note,
                $rocket_number, $rocket_note
            ]);
        }
        if ($admin_password) {
            $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                UPDATE admins SET
                    name = ?, email = ?, password = ?
                WHERE id = ?
            ");
            $stmt->execute([$admin_name, $admin_email, $password_hash, $_SESSION['admin_id']]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE admins SET name = ?, email = ?
                WHERE id = ?
            ");
            $stmt->execute([$admin_name, $admin_email, $_SESSION['admin_id']]);
        }

        $pdo->commit();
        $success_message = 'Settings saved successfully!';
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = 'Error saving settings: ' . htmlspecialchars($e->getMessage());
        $log_file = '../storage/error_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] Save settings error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Settings</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if ($error_message): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        toastr.error('<?php echo htmlspecialchars($error_message); ?>');
                    });
                </script>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        toastr.success('<?php echo htmlspecialchars($success_message); ?>');
                    });
                </script>
            <?php endif; ?>

            <form id="settingsForm" method="POST" enctype="multipart/form-data" autocomplete="off">
                <div class="card card-primary">
                    <div class="card-header"><h3 class="card-title">Site Info</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="siteName">Site Name</label>
                            <input type="text" class="form-control" id="siteName" name="siteName" placeholder="Enter site name" value="<?php echo htmlspecialchars($settings['site']['site_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="siteDesc">Site Description</label>
                            <textarea class="form-control" id="siteDesc" name="siteDesc" rows="4" placeholder="Short site description"><?php echo htmlspecialchars($settings['site']['site_description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="siteLogo">Site Logo</label>
                            <input type="file" class="form-control-file" id="siteLogo" name="siteLogo" accept="image/*">
                            <div class="mt-2" id="logoPreviewWrapper" style="width:120px; height:120px; border-radius:10px; box-shadow:0 0 10px rgba(255,255,255,0.7); background:#fff; display:flex; justify-content:center; align-items:center;">
                                <img id="logoPreview" src="<?php echo htmlspecialchars($settings['site']['site_logo'] ?? 'https://via.placeholder.com/100?text=Logo'); ?>" alt="Logo Preview" style="max-width:100%; max-height:100%; border-radius:10px;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="footerTagline">Footer Tagline</label>
                            <input type="text" class="form-control" id="footerTagline" name="footerTagline" placeholder="Footer tagline" value="<?php echo htmlspecialchars($settings['site']['footer_tagline'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="card card-secondary">
                    <div class="card-header"><h3 class="card-title">Social Links</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="facebookLink">Facebook URL</label>
                            <input type="url" class="form-control" id="facebookLink" name="facebookLink" placeholder="https://facebook.com/yourpage" value="<?php echo htmlspecialchars($settings['social']['facebook_url'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="twitterLink">Twitter URL</label>
                            <input type="url" class="form-control" id="twitterLink" name="twitterLink" placeholder="https://twitter.com/yourhandle" value="<?php echo htmlspecialchars($settings['social']['twitter_url'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="instagramLink">Instagram URL</label>
                            <input type="url" class="form-control" id="instagramLink" name="instagramLink" placeholder="https://instagram.com/yourhandle" value="<?php echo htmlspecialchars($settings['social']['instagram_url'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="linkedinLink">LinkedIn URL</label>
                            <input type="url" class="form-control" id="linkedinLink" name="linkedinLink" placeholder="https://linkedin.com/in/yourprofile" value="<?php echo htmlspecialchars($settings['social']['linkedin_url'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="card card-info">
                    <div class="card-header"><h3 class="card-title">Contact Info</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="contactPhone">Phone Number</label>
                            <input type="tel" class="form-control" id="contactPhone" name="contactPhone" placeholder="+8801234567890" value="<?php echo htmlspecialchars($settings['contact']['phone_number'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="contactAddress">Address</label>
                            <textarea class="form-control" id="contactAddress" name="contactAddress" rows="4" placeholder="Store address"><?php echo htmlspecialchars($settings['contact']['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card card-success">
                    <div class="card-header"><h3 class="card-title">Payment Info</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="bkashNumber">Bkash Number</label>
                            <input type="tel" class="form-control" id="bkashNumber" name="bkashNumber" placeholder="+8801xxxxxxxxx" value="<?php echo htmlspecialchars($settings['payment']['bkash_number'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="bkashNote">Bkash Note</label>
                            <input type="text" class="form-control" id="bkashNote" name="bkashNote" placeholder="e.g., Send as Personal" value="<?php echo htmlspecialchars($settings['payment']['bkash_note'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="nagadNumber">Nagad Number</label>
                            <input type="tel" class="form-control" id="nagadNumber" name="nagadNumber" placeholder="+8801xxxxxxxxx" value="<?php echo htmlspecialchars($settings['payment']['nagad_number'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="nagadNote">Nagad Note</label>
                            <input type="text" class="form-control" id="nagadNote" name="nagadNote" placeholder="e.g., Use this for payment only" value="<?php echo htmlspecialchars($settings['payment']['nagad_note'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="rocketNumber">Rocket Number</label>
                            <input type="tel" class="form-control" id="rocketNumber" name="rocketNumber" placeholder="+8801xxxxxxxxx" value="<?php echo htmlspecialchars($settings['payment']['rocket_number'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="rocketNote">Rocket Note</label>
                            <input type="text" class="form-control" id="rocketNote" name="rocketNote" placeholder="e.g., Add your order ID in reference" value="<?php echo htmlspecialchars($settings['payment']['rocket_note'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="card card-warning">
                    <div class="card-header"><h3 class="card-title">Admin Info & Password</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="adminName">Admin Name</label>
                            <input type="text" class="form-control" id="adminName" name="adminName" placeholder="Admin Name" value="<?php echo htmlspecialchars($settings['admin']['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="adminEmail">Admin Email</label>
                            <input type="email" class="form-control" id="adminEmail" name="adminEmail" placeholder="admin@example.com" value="<?php echo htmlspecialchars($settings['admin']['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group position-relative">
                            <label for="adminPassword">New Password</label>
                            <input type="password" class="form-control" id="adminPassword" name="adminPassword" placeholder="Enter new password">
                            <button type="button" class="btn btn-sm btn-outline-secondary position-absolute" style="top: 38px; right: 10px;" id="togglePass">Show</button>
                        </div>
                        <div class="form-group position-relative">
                            <label for="adminPasswordConfirm">Confirm Password</label>
                            <input type="password" class="form-control" id="adminPasswordConfirm" name="adminPasswordConfirm" placeholder="Confirm new password">
                            <button type="button" class="btn btn-sm btn-outline-secondary position-absolute" style="top: 38px; right: 10px;" id="togglePassConfirm">Show</button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mb-4">Save Settings</button>
            </form>
        </div>
    </section>
</div>

<?php require_once './includes/__footer__.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script>
    $(function() {
        toastr.options = {
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000
        };

        const logoInput = document.getElementById('siteLogo');
        const logoPreview = document.getElementById('logoPreview');

        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                toastr.error('Only image files allowed!');
                logoInput.value = '';
                logoPreview.src = '<?php echo htmlspecialchars($settings['site']['site_logo'] ?? 'https://via.placeholder.com/100?text=Logo'); ?>';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                toastr.error('File size must be under 2MB!');
                logoInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });

        function togglePassword(inputId, btnId) {
            const input = document.getElementById(inputId);
            const btn = document.getElementById(btnId);
            btn.addEventListener('click', () => {
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.innerText = 'Hide';
                } else {
                    input.type = 'password';
                    btn.innerText = 'Show';
                }
            });
        }

        togglePassword('adminPassword', 'togglePass');
        togglePassword('adminPasswordConfirm', 'togglePassConfirm');

        $('#settingsForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: '',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    toastr.success('Settings saved successfully!');
                    location.reload(); 
                },
                error: function(xhr, status, error) {
                    toastr.error('Failed to save settings: ' + (xhr.responseText || error));
                }
            });
        });
    });
</script>

<?php ob_end_flush(); ?>