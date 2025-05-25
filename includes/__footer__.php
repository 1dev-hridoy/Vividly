<?php
require_once 'server/dbcon.php';

try {
    $stmt = $pdo->query("SELECT site_name, site_logo, footer_tagline FROM site_settings ORDER BY updated_at DESC LIMIT 1");
    $site = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $site = ['site_name' => 'Minimal', 'site_logo' => '', 'footer_tagline' => 'Simple, clean, essential.'];
}

try {
    $stmt = $pdo->query("SELECT facebook_url, twitter_url, instagram_url, linkedin_url FROM social_links ORDER BY updated_at DESC LIMIT 1");
    $social = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $social = [];
}

try {
    $stmt = $pdo->query("SELECT name FROM category ORDER BY created_at DESC LIMIT 3");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

try {
    $stmt = $pdo->query("SELECT phone_number, address FROM contact_info ORDER BY updated_at DESC LIMIT 1");
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $contact = ['phone_number' => '', 'address' => ''];
}
?>
<footer>
    <div class="container">
        <div class="row">
            <!-- Branding & Social -->
            <div class="col-md-3 mb-4">
                <?php if (!empty($site['site_logo'])): ?>
                    <img src="<?php echo htmlspecialchars('storage/' . $site['site_logo']); ?>" alt="Logo" height="40" class="mb-3">
                <?php else: ?>
                    <h4 class="footer-title"><?php echo htmlspecialchars($site['site_name']); ?></h4>
                <?php endif; ?>
                <p class="mb-3"><?php echo htmlspecialchars($site['footer_tagline']); ?></p>
                <div class="social-icons">
                    <?php if (!empty($social['facebook_url'])): ?>
                        <a href="<?php echo htmlspecialchars($social['facebook_url']); ?>" class="social-icon" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social['instagram_url'])): ?>
                        <a href="<?php echo htmlspecialchars($social['instagram_url']); ?>" class="social-icon" target="_blank"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social['twitter_url'])): ?>
                        <a href="<?php echo htmlspecialchars($social['twitter_url']); ?>" class="social-icon" target="_blank"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social['linkedin_url'])): ?>
                        <a href="<?php echo htmlspecialchars($social['linkedin_url']); ?>" class="social-icon" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Shop Categories -->
            <div class="col-md-3 mb-4">
                <h4 class="footer-title">Shop</h4>
                <ul class="footer-links">
                    <?php foreach ($categories as $cat): ?>
                        <li><a href="./shop.php?category=<?php echo urlencode($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Static Info -->
            <div class="col-md-3 mb-4">
                <h4 class="footer-title">Information</h4>
                <ul class="footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Shipping & Returns</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-md-3 mb-4">
                <h4 class="footer-title">Contact</h4>
                <ul class="footer-links">
                    <?php if (!empty($contact['address'])): ?>
                        <li><?php echo nl2br(htmlspecialchars($contact['address'])); ?></li>
                    <?php endif; ?>
                    <?php if (!empty($contact['phone_number'])): ?>
                        <li><?php echo htmlspecialchars($contact['phone_number']); ?></li>
                    <?php endif; ?>
                    <li><a href="mailto:info@<?php echo strtolower(str_replace(' ', '', $site['site_name'])); ?>.com">info@<?php echo strtolower(str_replace(' ', '', $site['site_name'])); ?>.com</a></li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <p class="copyright">
                    © <?php echo date('Y'); ?> <?php echo htmlspecialchars($site['site_name']); ?>. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/scrollreveal@4.0.9/dist/scrollreveal.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
