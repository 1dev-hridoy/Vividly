<?php
require_once './includes/__header__.php';
?>
   <!-- Hero Carousel -->
<?php
require_once './includes/components/__carousel__.php';
?>

    <!-- Categories Section -->
    <section class="category-section py-5">
  <div class="container">
    <div class="row row-cols-2 row-cols-md-4 g-4">
      
      <div class="col">
        <div class="category-card h-100 text-center">
          <div class="category-img mb-3">
            <img class="img-fluid rounded" src="https://images.unsplash.com/photo-1525507119028-ed4c629a60a3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=735&q=80" alt="Women">
          </div>
          <h3 class="category-title">Women</h3>
        </div>
      </div>

      <div class="col">
        <div class="category-card h-100 text-center">
          <div class="category-img mb-3">
            <img class="img-fluid rounded" src="https://images.unsplash.com/photo-1487222477894-8943e31ef7b2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=726&q=80" alt="Men">
          </div>
          <h3 class="category-title">Men</h3>
        </div>
      </div>

      <div class="col">
        <div class="category-card h-100 text-center">
          <div class="category-img mb-3">
            <img class="img-fluid rounded" src="https://images.unsplash.com/photo-1511556820780-d912e42b4980?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80" alt="Accessories">
          </div>
          <h3 class="category-title">Accessories</h3>
        </div>
      </div>

      <div class="col">
        <div class="category-card h-100 text-center">
          <div class="category-img mb-3">
            <img class="img-fluid rounded" src="https://images.unsplash.com/photo-1533750349088-cd871a92f312?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=720&q=80" alt="Shoes">
          </div>
          <h3 class="category-title">Shoes</h3>
        </div>
      </div>

    </div>
  </div>
</section>




    <!-- Products Section -->
    <section class="product-section">
        <div class="container">
            <h2 class="section-title">Featured Products</h2>
            <div class="row">
                <!-- Product 1 -->
                <div class="col-md-3" data-sr-id="4">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1434389677669-e08b4cac3105?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=705&q=80" alt="Product 1">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Cotton T-Shirt</h5>
                            <p class="product-price mb-3">$29</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="col-md-3" data-sr-id="5">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1475178626620-a4d074967452?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=686&q=80" alt="Product 2">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Denim Jacket</h5>
                            <p class="product-price mb-3">$89</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="col-md-3" data-sr-id="6">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1491637639811-60e2756cc1c7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=728&q=80" alt="Product 3">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Minimal Watch</h5>
                            <p class="product-price mb-3">$120</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="col-md-3" data-sr-id="7">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1560343090-f0409e92791a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fA%3D%3D&auto=format&fit=crop&w=764&q=80" alt="Product 4">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Leather Bag</h5>
                            <p class="product-price mb-3">$150</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" data-sr-id="4">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1434389677669-e08b4cac3105?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=705&q=80" alt="Product 1">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Cotton T-Shirt</h5>
                            <p class="product-price mb-3">$29</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="col-md-3" data-sr-id="5">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1475178626620-a4d074967452?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=686&q=80" alt="Product 2">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Denim Jacket</h5>
                            <p class="product-price mb-3">$89</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="col-md-3" data-sr-id="6">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1491637639811-60e2756cc1c7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=728&q=80" alt="Product 3">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Minimal Watch</h5>
                            <p class="product-price mb-3">$120</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="col-md-3" data-sr-id="7">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1560343090-f0409e92791a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fA%3D%3D&auto=format&fit=crop&w=764&q=80" alt="Product 4">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Leather Bag</h5>
                            <p class="product-price mb-3">$150</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>


                <div class="col-md-3" data-sr-id="4">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1434389677669-e08b4cac3105?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=705&q=80" alt="Product 1">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Cotton T-Shirt</h5>
                            <p class="product-price mb-3">$29</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="col-md-3" data-sr-id="5">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1475178626620-a4d074967452?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=686&q=80" alt="Product 2">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Denim Jacket</h5>
                            <p class="product-price mb-3">$89</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="col-md-3" data-sr-id="6">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1491637639811-60e2756cc1c7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=728&q=80" alt="Product 3">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Minimal Watch</h5>
                            <p class="product-price mb-3">$120</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="col-md-3" data-sr-id="7">
                    <div class="card product-card">
                        <div class="product-img">
                            <img src="https://images.unsplash.com/photo-1560343090-f0409e92791a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fA%3D%3D&auto=format&fit=crop&w=764&q=80" alt="Product 4">
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Leather Bag</h5>
                            <p class="product-price mb-3">$150</p>
                            <button class="btn btn-minimal w-100">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
require_once './includes/__footer__.php';
?>