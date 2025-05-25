<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
      <a href="index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="carousel.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'carousel.php') ? 'active' : ''; ?>">
      <i class="fa fa-images"></i>
        <p>Carousel</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="category.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'category.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-list"></i>
        <p>Category</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="products.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-box"></i>
        <p>Products</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="add-product.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add-product.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-plus-square"></i>
        <p>Add Products</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="orders.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>">
      <i class="nav-icon fas fa-shopping-cart"></i>
        <p>Orders</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="users.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-users"></i>
        <p>Users</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="analytics.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'analytics.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-chart-line"></i>
        <p>Analytics</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="settings.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-cogs"></i>
        <p>Settings</p>
      </a>
    </li>
  </ul>
</nav>
</div>
</aside>