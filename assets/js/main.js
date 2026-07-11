// ============================================
// PROFESSIONAL INSTITUTIONAL WEBSITE JS
// ============================================

document.addEventListener('DOMContentLoaded', function () {

    // ============================================
    // SMOOTH SCROLLING
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const target = document.querySelector(targetId);
            if (target) {
                const offset = 80; // Account for fixed nav
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ============================================
    // STICKY NAVIGATION EFFECT
    // ============================================
    const nav = document.getElementById('mainNav');
    let lastScrollY = window.scrollY;

    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        if (currentScrollY > 100) {
            nav.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
        } else {
            nav.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        }

        lastScrollY = currentScrollY;

        // Active navigation based on scroll position
        updateActiveNavLink();
    });

    // ============================================
    // ACTIVE NAV LINK HIGHLIGHT
    // ============================================
    function updateActiveNavLink() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.main-nav ul li a');

        let currentSection = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.clientHeight;

            if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                currentSection = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + currentSection) {
                link.classList.add('active');
            }
        });
    }

    // ============================================
    // NOTICE BOARD ANIMATIONS
    // ============================================
    const noticeItems = document.querySelectorAll('.notice-item');

    const noticeObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateX(0)';
            }
        });
    }, {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
    });

    noticeItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        item.style.transition = `all 0.5s ease ${index * 0.1}s`;
        noticeObserver.observe(item);
    });

    // ============================================
    // STATISTICS COUNTER ANIMATION
    // ============================================
    const statNumbers = document.querySelectorAll('.stat-number');

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                animateCounter(target);
                statsObserver.unobserve(target);
            }
        });
    }, {
        threshold: 0.5
    });

    statNumbers.forEach(stat => statsObserver.observe(stat));

    function animateCounter(element) {
        const text = element.textContent;
        const hasPlus = text.includes('+');
        const hasPercent = text.includes('%');
        let targetNumber = parseInt(text.replace(/[^0-9]/g, ''));
        const duration = 2000;
        const steps = 60;
        const increment = targetNumber / steps;
        let current = 0;
        const stepTime = duration / steps;

        const counter = setInterval(() => {
            current += increment;

            if (current >= targetNumber) {
                clearInterval(counter);
                element.textContent = text;
            } else {
                let displayText = Math.floor(current).toLocaleString();
                if (hasPlus) displayText += '+';
                if (hasPercent) displayText += '%';
                element.textContent = displayText;
            }
        }, stepTime);
    }

    // ============================================
    // DEPARTMENT CARDS HOVER EFFECT
    // ============================================
    const deptCards = document.querySelectorAll('.department-card');

    deptCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-8px)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });

    // ============================================
    // MOBILE MENU TOGGLE (Optional Enhancement)
    // ============================================
    // If needed for mobile, you can add a hamburger menu toggle

    console.log('College Website Professional Interface Loaded Successfully');
});