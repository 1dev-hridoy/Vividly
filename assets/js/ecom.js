        // Initialize ScrollReveal with minimal settings
        const sr = ScrollReveal({
            origin: 'bottom',
            distance: '20px',
            duration: 800,
            delay: 100,
            easing: 'ease',
            reset: false
        });
        
        // Apply ScrollReveal to elements
        sr.reveal('.category-card', { 
            interval: 150 
        });
        
        sr.reveal('.product-card', { 
            interval: 100 
        });
        
        // Enhance carousel transitions with Anime.js
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('heroCarousel');
            
            // Enhance the Bootstrap carousel with Anime.js
            carousel.addEventListener('slide.bs.carousel', function (e) {
                const currentItem = e.from;
                const nextItem = e.to;
                
                // Get the current and next slides
                const slides = carousel.querySelectorAll('.carousel-item');
                const currentSlide = slides[currentItem];
                const nextSlide = slides[nextItem];
                
                // Animate the transition
                anime({
                    targets: nextSlide.querySelector('img'),
                    scale: [1.05, 1],
                    opacity: [0.8, 1],
                    easing: 'easeOutExpo',
                    duration: 1000
                });
            });
            
            // Initialize the first slide animation
            anime({
                targets: carousel.querySelector('.carousel-item.active img'),
                scale: [1.05, 1],
                opacity: [0.8, 1],
                easing: 'easeOutExpo',
                duration: 1000
            });
        });
        
        // Product hover animation
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                anime({
                    targets: card,
                    translateY: -10,
                    boxShadow: '0 10px 20px rgba(0, 0, 0, 0.1)',
                    duration: 300,
                    easing: 'easeOutExpo'
                });
            });
            
            card.addEventListener('mouseleave', () => {
                anime({
                    targets: card,
                    translateY: 0,
                    boxShadow: '0 2px 10px rgba(0, 0, 0, 0.05)',
                    duration: 300,
                    easing: 'easeOutExpo'
                });
            });
        });