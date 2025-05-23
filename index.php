<?php
require_once './includes/__header__.php';
?>
   <!-- Hero Carousel -->
   <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1445205170230-053b83016050?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80" class="d-block w-100" alt="Fashion 1">
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" class="d-block w-100" alt="Fashion 2">
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" class="d-block w-100" alt="Fashion 3">
            </div>
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