// Mobile Navigation
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
});

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
    hamburger.classList.remove('active');
    navMenu.classList.remove('active');
}));

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Navbar scroll effect with class-based approach
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 100) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Enhanced counter animation for stats
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString('id-ID');
    }, 20);
}

// Enhanced Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            // Counter animation
            if (entry.target.classList.contains('stat-number')) {
                const target = parseInt(entry.target.getAttribute('data-target'));
                animateCounter(entry.target, target);
                observer.unobserve(entry.target);
            }
            
            // Staggered card animations
            if (entry.target.classList.contains('flora-card') || 
                entry.target.classList.contains('fauna-card')) {
                const cards = document.querySelectorAll('.flora-card, .fauna-card');
                const index = Array.from(cards).indexOf(entry.target);
                
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                entry.target.style.transition = 'all 0.6s ease-out';
                
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100); // Stagger animation
                
                observer.unobserve(entry.target);
            }
            
            // Section animations
            if (entry.target.classList.contains('section-header')) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                entry.target.style.transition = 'all 0.8s ease-out';
                
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                observer.unobserve(entry.target);
            }
        }
    });
}, observerOptions);

// Observe elements
document.querySelectorAll('.stat-number').forEach(el => observer.observe(el));
document.querySelectorAll('.flora-card, .fauna-card').forEach(el => observer.observe(el));
document.querySelectorAll('.section-header').forEach(el => observer.observe(el));

// Modal functionality
const modal = document.getElementById('detailModal');
const modalBody = document.getElementById('modalBody');
const closeBtn = document.querySelector('.close');

function showDetail(type, id) {
    // Show loading
    modalBody.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
            <p style="margin-top: 1rem;">Memuat detail...</p>
        </div>
    `;
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Fetch detail data
    fetch(`get_detail.php?type=${type}&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                modalBody.innerHTML = `
                    <div class="detail-content">
                        <div class="detail-header">
                            <img src="${item.image}" alt="${item.nama}" class="detail-image" onerror="this.src='assets/images/default-fauna.svg'">
                            <div class="detail-info">
                                <h2 class="detail-title">${item.nama}</h2>
                                <p class="detail-scientific">${item.nama_ilmiah}</p>
                                <div class="detail-tags">
                                    <span class="tag">${item.habitat}</span>
                                    <span class="tag">${item.status_konservasi}</span>
                                    <span class="tag">${item.asal_daerah}</span>
                                </div>
                            </div>
                        </div>
                        <div class="detail-body">
                            <div class="detail-section">
                                <h3><i class="fas fa-info-circle"></i> Deskripsi</h3>
                                <p>${item.deskripsi}</p>
                            </div>
                            <div class="detail-section">
                                <h3><i class="fas fa-map-marker-alt"></i> Habitat</h3>
                                <p>${item.habitat_detail || item.habitat}</p>
                            </div>
                            <div class="detail-section">
                                <h3><i class="fas fa-heart"></i> Status Konservasi</h3>
                                <p>${item.status_konservasi}</p>
                            </div>
                            ${item.manfaat ? `
                                <div class="detail-section">
                                    <h3><i class="fas fa-leaf"></i> Manfaat</h3>
                                    <p>${item.manfaat}</p>
                                </div>
                            ` : ''}
                            ${item.makanan ? `
                                <div class="detail-section">
                                    <h3><i class="fas fa-utensils"></i> Makanan</h3>
                                    <p>${item.makanan}</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #e74c3c;"></i>
                        <p style="margin-top: 1rem;">Gagal memuat detail. Silakan coba lagi.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #e74c3c;"></i>
                    <p style="margin-top: 1rem;">Terjadi kesalahan. Silakan coba lagi.</p>
                </div>
            `;
        });
}

// Close modal
closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
});

window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});

// Enhanced Parallax effect for hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const parallax = document.querySelector('.hero-bg-animation');
    if (parallax) {
        const speed = scrolled * 0.3;
        parallax.style.transform = `translateY(${speed}px)`;
    }
    
    // Parallax for floating cards
    const floatingCards = document.querySelectorAll('.floating-card');
    floatingCards.forEach((card, index) => {
        const speed = scrolled * (0.1 + index * 0.05);
        card.style.transform = `translateY(${speed}px)`;
    });
    
    // Parallax for decoration elements
    const decorations = document.querySelectorAll('.decoration-element, .decoration-particle');
    decorations.forEach((decoration, index) => {
        const speed = scrolled * (0.05 + index * 0.02);
        decoration.style.transform = `translateY(${speed}px)`;
    });
});

// Add dynamic color changing for floating elements
function addDynamicColors() {
    const floatingElements = document.querySelectorAll('.floating-element');
    const colors = [
        'rgba(46, 139, 87, 0.15)',
        'rgba(255, 107, 53, 0.15)',
        'rgba(78, 205, 196, 0.15)',
        'rgba(240, 147, 251, 0.15)',
        'rgba(102, 126, 234, 0.15)',
        'rgba(245, 101, 101, 0.15)'
    ];
    
    floatingElements.forEach((element, index) => {
        setInterval(() => {
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            element.style.color = randomColor;
        }, 3000 + index * 500);
    });
}

// Add hover effects to stat cards
function enhanceStatCards() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach((card, index) => {
        card.addEventListener('mouseenter', () => {
            // Add ripple effect
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.top = '50%';
            ripple.style.left = '50%';
            ripple.style.width = '0';
            ripple.style.height = '0';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.borderRadius = '50%';
            ripple.style.transform = 'translate(-50%, -50%)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            card.style.position = 'relative';
            card.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// Add floating animation to feature items
function enhanceFeatureItems() {
    const featureItems = document.querySelectorAll('.feature-item');
    
    featureItems.forEach((item, index) => {
        item.addEventListener('mouseenter', () => {
            item.style.animation = `featureFloat 0.6s ease-out`;
        });
        
        item.addEventListener('mouseleave', () => {
            item.style.animation = '';
        });
    });
}

// Add dynamic background particles
function createBackgroundParticles() {
    const sections = document.querySelectorAll('.flora-section, .fauna-section');
    
    sections.forEach(section => {
        for (let i = 0; i < 5; i++) {
            const particle = document.createElement('div');
            particle.style.position = 'absolute';
            particle.style.width = Math.random() * 4 + 2 + 'px';
            particle.style.height = particle.style.width;
            particle.style.background = 'rgba(46, 139, 87, 0.1)';
            particle.style.borderRadius = '50%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animation = `particleFloat ${Math.random() * 10 + 5}s ease-in-out infinite`;
            particle.style.animationDelay = Math.random() * 5 + 's';
            particle.style.pointerEvents = 'none';
            particle.style.zIndex = '1';
            
            section.style.position = 'relative';
            section.appendChild(particle);
        }
    });
}

// Initialize all enhancements
document.addEventListener('DOMContentLoaded', () => {
    addDynamicColors();
    enhanceStatCards();
    enhanceFeatureItems();
    createBackgroundParticles();
});

// Add CSS animations dynamically
const additionalStyles = `
    @keyframes ripple {
        0% {
            width: 0;
            height: 0;
            opacity: 1;
        }
        100% {
            width: 300px;
            height: 300px;
            opacity: 0;
        }
    }
    
    @keyframes featureFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-10px) scale(1.05); }
    }
    
    @keyframes particleFloat {
        0%, 100% { 
            transform: translateY(0) translateX(0) rotate(0deg);
            opacity: 0.1;
        }
        25% { 
            transform: translateY(-20px) translateX(10px) rotate(90deg);
            opacity: 0.3;
        }
        50% { 
            transform: translateY(-10px) translateX(-5px) rotate(180deg);
            opacity: 0.2;
        }
        75% { 
            transform: translateY(-30px) translateX(-10px) rotate(270deg);
            opacity: 0.4;
        }
    }
    
    .floating-card:hover {
        animation-play-state: paused;
    }
    
    .orbit-element:hover {
        animation-play-state: paused;
        transform: scale(1.2) !important;
        background: rgba(255, 255, 255, 0.4) !important;
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);

// Add loading animation to buttons
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!this.classList.contains('loading')) {
            this.classList.add('loading');
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            
            setTimeout(() => {
                this.classList.remove('loading');
                this.innerHTML = originalText;
            }, 1000);
        }
    });
});

// Search functionality (if search input exists)
const searchInput = document.querySelector('#searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const cards = document.querySelectorAll('.flora-card, .fauna-card');
        
        cards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const scientific = card.querySelector('.card-scientific').textContent.toLowerCase();
            const description = card.querySelector('.card-description').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || scientific.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.5s ease-in';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// Add CSS for loading button
const style = document.createElement('style');
style.textContent = `
    .btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .detail-content {
        max-width: 100%;
    }
    
    .detail-header {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
        align-items: start;
    }
    
    .detail-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-medium);
    }
    
    .detail-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }
    
    .detail-scientific {
        font-style: italic;
        color: var(--primary-color);
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }
    
    .detail-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .detail-section {
        margin-bottom: 2rem;
    }
    
    .detail-section h3 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
        font-size: 1.3rem;
    }
    
    .detail-section p {
        line-height: 1.8;
        color: #666;
    }
    
    @media (max-width: 768px) {
        .detail-header {
            grid-template-columns: 1fr;
            text-align: center;
        }
        
        .detail-image {
            height: 200px;
        }
        
        .detail-title {
            font-size: 1.5rem;
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show/hide floating action button based on scroll
window.addEventListener('scroll', () => {
    const floatingAction = document.querySelector('.floating-action');
    if (floatingAction) {
        if (window.scrollY > 300) {
            floatingAction.style.opacity = '1';
            floatingAction.style.visibility = 'visible';
        } else {
            floatingAction.style.opacity = '0';
            floatingAction.style.visibility = 'hidden';
        }
    }
});

// Create dynamic particles
function createParticles() {
    const particleContainer = document.createElement('div');
    particleContainer.className = 'particle-container';
    document.body.appendChild(particleContainer);
    
    setInterval(() => {
        if (document.querySelectorAll('.particle').length < 20) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 2 + 's';
            particleContainer.appendChild(particle);
            
            setTimeout(() => {
                particle.remove();
            }, 8000);
        }
    }, 500);
}

// Enhanced card animations with stagger effect
function enhanceCardAnimations() {
    const cards = document.querySelectorAll('.flora-card, .fauna-card');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('grid-item-enter');
                    entry.target.style.opacity = '1';
                }, index * 100);
                cardObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    cards.forEach(card => {
        card.style.opacity = '0';
        cardObserver.observe(card);
    });
}

// Add shimmer effect to titles
function addShimmerEffect() {
    const titles = document.querySelectorAll('.section-title, .page-title');
    titles.forEach(title => {
        title.classList.add('text-shimmer');
    });
}

// Enhanced button interactions
function enhanceButtons() {
    const buttons = document.querySelectorAll('.btn, .search-btn, .reset-btn-flora, .reset-btn-fauna');
    
    buttons.forEach(button => {
        button.classList.add('btn-enhanced');
        
        button.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple-effect');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// Add glow effect to important elements
function addGlowEffects() {
    const importantElements = document.querySelectorAll('.stat-card, .feature-item');
    importantElements.forEach((element, index) => {
        setTimeout(() => {
            element.classList.add('glow-pulse');
        }, index * 200);
    });
}

// Dynamic color changing for floating elements
function addDynamicColorChanging() {
    const floatingElements = document.querySelectorAll('.floating-element, .hero-float-element');
    const colors = [
        'rgba(46, 139, 87, 0.2)',
        'rgba(255, 107, 53, 0.2)',
        'rgba(78, 205, 196, 0.2)',
        'rgba(240, 147, 251, 0.2)',
        'rgba(102, 126, 234, 0.2)',
        'rgba(245, 101, 101, 0.2)'
    ];
    
    floatingElements.forEach((element, index) => {
        setInterval(() => {
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            element.style.color = randomColor;
        }, 2000 + index * 300);
    });
}

// Mouse trail effect
function createMouseTrail() {
    let mouseTrail = [];
    const maxTrailLength = 10;
    
    document.addEventListener('mousemove', (e) => {
        mouseTrail.push({ x: e.clientX, y: e.clientY, time: Date.now() });
        
        if (mouseTrail.length > maxTrailLength) {
            mouseTrail.shift();
        }
        
        // Remove old trail elements
        document.querySelectorAll('.mouse-trail').forEach(trail => {
            if (Date.now() - parseInt(trail.dataset.time) > 1000) {
                trail.remove();
            }
        });
        
        // Create new trail element
        const trailElement = document.createElement('div');
        trailElement.className = 'mouse-trail';
        trailElement.dataset.time = Date.now();
        trailElement.style.cssText = `
            position: fixed;
            left: ${e.clientX}px;
            top: ${e.clientY}px;
            width: 6px;
            height: 6px;
            background: var(--primary-color);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            opacity: 0.6;
            animation: trailFade 1s ease-out forwards;
        `;
        document.body.appendChild(trailElement);
    });
}

// Initialize all enhancements
document.addEventListener('DOMContentLoaded', () => {
    // Initialize existing functions
    addDynamicColors();
    enhanceStatCards();
    enhanceFeatureItems();
    createBackgroundParticles();
    
    // Initialize new functions
    createParticles();
    enhanceCardAnimations();
    addShimmerEffect();
    enhanceButtons();
    addGlowEffects();
    addDynamicColorChanging();
    createMouseTrail();
    
    // Set initial state for floating action button
    const floatingAction = document.querySelector('.floating-action');
    if (floatingAction) {
        floatingAction.style.opacity = '0';
        floatingAction.style.visibility = 'hidden';
        floatingAction.style.transition = 'all 0.3s ease';
    }
});

// Add additional CSS for new effects
const newStyles = `
    @keyframes trailFade {
        0% {
            opacity: 0.6;
            transform: scale(1);
        }
        100% {
            opacity: 0;
            transform: scale(0.3);
        }
    }
    
    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: rippleAnimation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes rippleAnimation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .mouse-trail {
        transition: all 0.1s ease;
    }
    
    @media (max-width: 768px) {
        .mouse-trail {
            display: none;
        }
    }
    
    @media (prefers-reduced-motion: reduce) {
        .mouse-trail,
        .particle,
        .ripple-effect {
            display: none !important;
        }
    }
`;

const additionalStyleSheet = document.createElement('style');
additionalStyleSheet.textContent = newStyles;
document.head.appendChild(additionalStyleSheet);