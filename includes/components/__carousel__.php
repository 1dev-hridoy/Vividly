<?php
require_once 'server/dbcon.php';

try {
    $stmt = $pdo->query("SELECT image_path FROM carousels ORDER BY created_at DESC");
    $carousels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $log_file = '../storage/error_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] Failed to fetch carousels: " . $e->getMessage() . "\n", FILE_APPEND);
    $carousels = [];
}
?>

<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php if (empty($carousels)): ?>
            <div class="carousel-item active">
                <img src="assets/img/default_carousel.jpg" class="d-block w-100" alt="Default Carousel">
            </div>
        <?php else: ?>
            <?php foreach ($carousels as $index => $carousel): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars('storage/' . $carousel['image_path']); ?>" class="d-block w-100" alt="Carousel Image <?php echo $index + 1; ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>