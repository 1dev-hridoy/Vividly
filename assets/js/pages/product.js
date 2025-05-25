   
    document.querySelectorAll('.product-thumbnail').forEach(thumbnail => {
        thumbnail.addEventListener('click', () => {
            document.querySelectorAll('.product-thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
            document.getElementById('main-img').src = thumbnail.dataset.img;
        });
    });

  
    document.querySelectorAll('.details-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.details-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.details-content').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });

    
    const quantityInput = document.getElementById('quantity');
    document.getElementById('decrease-quantity').addEventListener('click', () => {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });
    document.getElementById('increase-quantity').addEventListener('click', () => {
        let value = parseInt(quantityInput.value);
        quantityInput.value = value + 1;
    });

   
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', () => {
            document.querySelectorAll('.color-option').forEach(o => o.classList.remove('active'));
            option.classList.add('active');
            option.querySelector('input').checked = true;
        });
    });
    document.querySelectorAll('.size-option').forEach(option => {
        option.addEventListener('click', () => {
            document.querySelectorAll('.size-option').forEach(o => o.classList.remove('active'));
            option.classList.add('active');
            option.querySelector('input').checked = true;
        });
    });

  
    function addToCart(productId) {
        alert('Product ' + productId + ' added to cart!');
    }