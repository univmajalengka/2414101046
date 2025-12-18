// ============ Navigation Highlight Animation ============
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link');
    const navHighlight = document.querySelector('.nav-highlight');
    const sections = document.querySelectorAll('.section');
    let currentActiveLink = navLinks[0];

    function updateHighlight(link) {
        const navMenu = document.querySelector('.nav-menu');
        const left = link.offsetLeft;
        const top = link.offsetTop;
        const width = link.offsetWidth;
        const height = link.offsetHeight;
        navHighlight.style.left = left + 'px';
        navHighlight.style.top = top + 'px';
        navHighlight.style.width = width + 'px';
        navHighlight.style.height = height + 'px';
    }

    // Initial highlight position
    updateHighlight(currentActiveLink);

    // Add click listeners to nav links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Only intercept internal section links (those with data-section)
            const isInternal = this.hasAttribute('data-section');
            if (isInternal) {
                e.preventDefault();

                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                currentActiveLink = this;
                updateHighlight(this);

                const sectionId = this.getAttribute('data-section');
                const targetSection = document.getElementById(sectionId);

                if (targetSection) {
                    targetSection.scrollIntoView({ behavior: 'smooth' });
                }
            }
            // otherwise allow normal navigation for page links (pemesanan, pesanan_saya, etc.)
        });
    });

    // Update active nav link on scroll
    window.addEventListener('scroll', function() {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop - 150) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-section') === current) {
                link.classList.add('active');
                updateHighlight(link);
                currentActiveLink = link;
            }
        });
    });

    // Update highlight on window resize
    window.addEventListener('resize', function() {
        if (currentActiveLink) {
            updateHighlight(currentActiveLink);
        }
    });

    // Video thumbnail -> embed replacement
    document.querySelectorAll('.video-thumb').forEach(thumb => {
        thumb.addEventListener('click', function() {
            const videoId = this.getAttribute('data-video-id');
            const iframe = document.createElement('iframe');
            iframe.setAttribute('width', '100%');
            iframe.setAttribute('height', '600');
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
            iframe.setAttribute('allowfullscreen', '');
            iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
            this.parentNode.replaceChild(iframe, this);
        });
    });
});

// ============ Carousel Auto-Rotation ============
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const totalSlides = slides.length;

function showSlide(index) {
    slides.forEach(slide => slide.classList.remove('active'));
    slides[index].classList.add('active');
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
}

// Auto-rotate carousel every 5 seconds
let carouselInterval = setInterval(nextSlide, 5000);

// Reset interval on manual control
function resetCarouselInterval() {
    clearInterval(carouselInterval);
    carouselInterval = setInterval(nextSlide, 5000);
}

// Carousel navigation buttons
document.querySelectorAll('.carousel-control').forEach(button => {
    button.addEventListener('click', function() {
        if (this.classList.contains('prev')) {
            prevSlide();
        } else {
            nextSlide();
        }
        resetCarouselInterval();
    });
});

// Initialize first slide
if (slides.length > 0) {
    showSlide(0);
}

// ============ Intersection Observer for Scroll Animations ============
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.animationPlayState = 'running';
        }
    });
}, observerOptions);

// Observe all animated elements
document.querySelectorAll('.slide-in-left, .slide-in-right, .fade-in, .fade-in-up, .fade-in-left, .flip-in, .hover-zoom').forEach(el => {
    el.style.opacity = '0';
    el.style.animationPlayState = 'paused';
    observer.observe(el);
});

// ============ Smooth Scroll Behavior for Section-by-Section Scrolling ============
let isScrolling = false;
const scrollSections = document.querySelectorAll('.section');

document.addEventListener('wheel', function(e) {
    if (isScrolling) {
        e.preventDefault();
        return;
    }

    // Only apply section scrolling if not currently in a scrollable element
    if (e.target.closest('iframe') || e.target.closest('select')) {
        return;
    }

    isScrolling = true;
    const scrollDirection = e.deltaY > 0 ? 1 : -1;
    
    let currentSectionIndex = Array.from(scrollSections).findIndex(section => {
        const rect = section.getBoundingClientRect();
        return rect.top >= -200 && rect.top <= window.innerHeight / 2;
    });

    if (currentSectionIndex === -1) {
        currentSectionIndex = 0;
    }

    let nextSectionIndex = currentSectionIndex + scrollDirection;
    
    if (nextSectionIndex >= 0 && nextSectionIndex < scrollSections.length) {
        scrollSections[nextSectionIndex].scrollIntoView({ behavior: 'smooth' });
    }

    setTimeout(() => {
        isScrolling = false;
    }, 1000);
}, { passive: false });

// Allow normal scrolling on touch devices
let touchStartY = 0;

document.addEventListener('touchstart', function(e) {
    touchStartY = e.touches[0].clientY;
}, { passive: true });

document.addEventListener('touchmove', function(e) {
    // Allow normal scrolling on touch
}, { passive: true });

// ============ Parallax Effect ============
window.addEventListener('scroll', function() {
    const scrollY = window.pageYOffset;
    
    document.querySelectorAll('.carousel-image').forEach(img => {
        img.style.transform = `translateY(${scrollY * 0.3}px)`;
    });
});

// ============ Mobile Menu Responsiveness ============
window.addEventListener('resize', function() {
    const navHighlight = document.querySelector('.nav-highlight');
    const activeLink = document.querySelector('.nav-link.active');
    if (activeLink) {
        navHighlight.style.left = activeLink.offsetLeft + 'px';
        navHighlight.style.width = activeLink.offsetWidth + 'px';
    }
});

// ============ Page Transition Animations ============
document.addEventListener('DOMContentLoaded', function() {
    document.body.style.opacity = '1';
});

// ============ Add Stagger Animation to Grid Items ============
const gridItems = document.querySelectorAll('.objek-card, .fasilitas-item, .galeri-item, .paket-card');
gridItems.forEach((item, index) => {
    item.style.animation = `fadeInUp 0.6s ease forwards`;
    item.style.animationDelay = `${index * 0.1}s`;
    item.style.opacity = '0';
});

// ============ Scroll Animation Trigger ============
const elementsToAnimate = document.querySelectorAll('.section-header, .tentang-text, .tentang-image');

const revealOptions = {
    threshold: 0.15,
    rootMargin: '0px 0px -100px 0px'
};

const revealOnScroll = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
        }
    });
}, revealOptions);

elementsToAnimate.forEach(element => {
    revealOnScroll.observe(element);
});

// ============ Smooth Scroll to Top ============
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Create scroll-to-top button if needed
if (window.innerHeight < 768) {
    const scrollBtn = document.createElement('button');
    scrollBtn.id = 'scrollToTopBtn';
    scrollBtn.innerHTML = '↑';
    scrollBtn.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 10px 15px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 20px;
        display: none;
        z-index: 999;
    `;
    document.body.appendChild(scrollBtn);

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollBtn.style.display = 'block';
        } else {
            scrollBtn.style.display = 'none';
        }
    });

    scrollBtn.addEventListener('click', scrollToTop);
}

// ============ Add CSS Variables ============
document.documentElement.style.setProperty('--primary-color', '#c41e3a');
document.documentElement.style.setProperty('--secondary-color', '#ffc857');
