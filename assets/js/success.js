document.addEventListener('DOMContentLoaded', function() {
    const hasOrder = <?php echo $order ? 'true' : 'false'; ?>;
    
    if (hasOrder) {

        startSuccessAnimation();
    } else {
    
        startErrorAnimation();
    }
    
    function startSuccessAnimation() {
        const timeline = anime.timeline({
            easing: 'easeOutExpo',
            duration: 750
        });
        


             anime.set([
            '#success-circle',
            '#success-title',
            '#success-subtitle',
            '#order-details',
            '#email-confirmation',
            '#action-buttons',
            '#next-steps'
        ], {
            opacity: 0,
            translateY: 30
        });
        timeline.add({
            targets: '#success-circle',
            scale: [0, 1.2, 1],
            opacity: [0, 1],
            duration: 1000,
            complete: function() {
                anime({
                    targets: '#success-icon',
                    scale: [0, 1.3, 1],
                    opacity: [0, 1],
                    duration: 500,
                    delay: 200,
                    easing: 'easeOutBounce'
                });
            }
        })
        

        
        .add({
            targets: '#success-title',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=400')
        
 
        
        .add({
            targets: '#success-subtitle',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=300')
        
  
        
        .add({
            targets: '#order-details',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=200')
        
  
        
        .add({
            targets: '#email-confirmation',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=100')
        

        
        .add({
            targets: '#action-buttons',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=100')
        

        
        .add({
            targets: '#next-steps',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=100');
        

        
        setTimeout(() => {
            animateConfetti();
        }, 1500);
    }
    
 
    function startErrorAnimation() {
        const timeline = anime.timeline({
            easing: 'easeOutExpo',
            duration: 750
        });
        

        anime.set([
            '#error-circle',
            '#error-title',
            '#error-subtitle',
            '#error-alert',
            '#error-actions'
        ], {
            opacity: 0,
            translateY: 30
        });
        
    
        timeline.add({
            targets: '#error-circle',
            scale: [0, 1.2, 1],
            opacity: [0, 1],
            duration: 1000,
            complete: function() {
          
                anime({
                    targets: '#error-icon',
                    scale: [0, 1.3, 1],
                    opacity: [0, 1],
                    duration: 500,
                    delay: 200,
                    easing: 'easeOutBounce'
                });
            }
        })
        
 
        .add({
            targets: '#error-title',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=400')
        
    
        .add({
            targets: '#error-subtitle',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=300')
        
   
        .add({
            targets: '#error-alert',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=200')
        
   
        .add({
            targets: '#error-actions',
            opacity: [0, 1],
            translateY: [30, 0],
            duration: 600
        }, '-=100');
    }
    
 
    function animateConfetti() {
        const confettiElements = document.querySelectorAll('.floating-element');
        
        confettiElements.forEach((element, index) => {
            anime({
                targets: element,
                opacity: [0, 0.3, 0],
                translateY: [0, -50, -100],
                translateX: () => anime.random(-30, 30),
                rotate: () => anime.random(-180, 180),
                scale: [0.5, 1, 0.5],
                duration: 3000,
                delay: index * 200,
                easing: 'easeOutExpo',
                loop: true
            });
        });
    }
    
 
    const trackOrderBtn = document.getElementById('track-order');
    if (trackOrderBtn) {
        trackOrderBtn.addEventListener('click', function() {
          
            anime({
                targets: this,
                scale: [1, 0.95, 1],
                duration: 200,
                easing: 'easeInOutQuad'
            });
            
          
            const orderId = '<?php echo htmlspecialchars($order['custom_order_id'] ?? ''); ?>';
            setTimeout(() => {
                window.location.href = `track-order.php?order_id=${orderId}`;
            }, 300);
        });
    }
    

    document.querySelectorAll('.btn-minimal, .btn-outline').forEach(button => {
        button.addEventListener('mouseenter', function() {
            anime({
                targets: this,
                translateY: -2,
                duration: 200,
                easing: 'easeOutQuad'
            });
        });
        
        button.addEventListener('mouseleave', function() {
            anime({
                targets: this,
                translateY: 0,
                duration: 200,
                easing: 'easeOutQuad'
            });
        });
    });
    
   
    const orderIdElement = document.getElementById('order-id');
    if (orderIdElement) {
        orderIdElement.addEventListener('click', function() {
            const orderId = this.textContent;
            
          
            navigator.clipboard.writeText(orderId).then(() => {
               
                const originalText = this.textContent;
                const originalBg = this.style.backgroundColor;
                const originalColor = this.style.color;
                
                this.textContent = 'Copied to clipboard!';
                this.style.backgroundColor = '#28a745';
                this.style.color = 'white';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.backgroundColor = originalBg;
                    this.style.color = originalColor;
                }, 2000);
            }).catch(() => {
        
                const textArea = document.createElement('textarea');
                textArea.value = orderId;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                setTimeout(() => {
                    this.textContent = originalText;
                }, 2000);
            });
        });
    }
});