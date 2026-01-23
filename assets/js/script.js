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

// Navbar scroll effect
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 100) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        navbar.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
    }
});

// Counter animation for stats
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 20);
}

// Intersection Observer for animations
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
            
            // Fade in animation
            if (entry.target.classList.contains('flora-card') || 
                entry.target.classList.contains('fauna-card')) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                entry.target.style.transition = 'all 0.6s ease-out';
                
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
                            <img src="${item.image}" alt="${item.nama}" class="detail-image">
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

// Parallax effect for hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const parallax = document.querySelector('.hero-bg-animation');
    if (parallax) {
        const speed = scrolled * 0.5;
        parallax.style.transform = `translateY(${speed}px)`;
    }
});

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