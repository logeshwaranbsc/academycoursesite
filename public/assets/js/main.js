/**
 * Digital Teenz Academy — Public Interactive Client JS (Vanilla JS)
 */

document.addEventListener('DOMContentLoaded', () => {
    // ── 1. Mobile Menu Toggle ──
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.contains('hidden');
            if (isOpen) {
                mobileMenu.classList.remove('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'true');
            } else {
                mobileMenu.classList.add('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ── 2. Header Scroll Shadow ──
    const header = document.querySelector('header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.add('scrolled', 'shadow-sm');
            } else {
                header.classList.remove('scrolled', 'shadow-sm');
            }
        }, { passive: true });
    }

    // ── 3. Hero Banner Slider ──
    const slides = document.querySelectorAll('.hero-slide');
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');

    if (slides.length > 1) {
        let currentSlide = 0;
        let slideInterval = null;

        const showSlide = (index) => {
            slides.forEach((s, i) => {
                s.classList.toggle('active', i === index);
            });
            currentSlide = index;
        };

        const nextSlide = () => {
            showSlide((currentSlide + 1) % slides.length);
        };

        const prevSlide = () => {
            showSlide((currentSlide - 1 + slides.length) % slides.length);
        };

        if (nextBtn) nextBtn.addEventListener('click', () => {
            nextSlide();
            resetTimer();
        });

        if (prevBtn) prevBtn.addEventListener('click', () => {
            prevSlide();
            resetTimer();
        });

        const resetTimer = () => {
            if (slideInterval) clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 5000);
        };

        resetTimer();
    }

    // ── 4. Gallery Category Filter & Lightbox ──
    const categoryBtns = document.querySelectorAll('.gallery-cat-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    const lightbox = document.getElementById('gallery-lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxTitle = document.getElementById('lightbox-title');
    const lightboxClose = document.getElementById('lightbox-close');

    if (categoryBtns.length > 0) {
        categoryBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                categoryBtns.forEach(b => {
                    b.classList.remove('active');
                    b.style.backgroundColor = 'transparent';
                    b.style.color = '#4B5563';
                });
                btn.classList.add('active');
                btn.style.backgroundColor = 'var(--color-navy)';
                btn.style.color = '#fff';

                const cat = btn.getAttribute('data-category');
                galleryItems.forEach(item => {
                    if (cat === 'All' || item.getAttribute('data-category') === cat) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    if (lightbox) {
        document.querySelectorAll('.gallery-trigger').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const src = trigger.getAttribute('data-img');
                const title = trigger.getAttribute('data-title');
                if (lightboxImg) lightboxImg.src = src;
                if (lightboxTitle) lightboxTitle.textContent = title;
                lightbox.classList.add('show');
            });
        });

        const closeLightbox = () => lightbox.classList.remove('show');
        if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) closeLightbox();
        });
    }

    // ── 5. Video Play Overlay (YouTube Embed) ──
    document.querySelectorAll('.video-play-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const container = btn.closest('.video-container');
            const embedUrl = btn.getAttribute('data-embed');
            if (container && embedUrl) {
                container.innerHTML = `<iframe src="${embedUrl}?autoplay=1" class="w-full h-full" style="width:100%;height:100%;border:none;" allow="autoplay" allowfullscreen></iframe>`;
            }
        });
    });

    // ── 6. AJAX Public Enquiry Form ──
    const enquiryForm = document.getElementById('public-enquiry-form');
    if (enquiryForm) {
        enquiryForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = enquiryForm.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : 'Submit';
            const feedbackEl = document.getElementById('enquiry-feedback');

            // Reset field errors
            enquiryForm.querySelectorAll('.form-input').forEach(i => i.classList.remove('error'));
            enquiryForm.querySelectorAll('.field-error').forEach(e => e.textContent = '');

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Submitting...';
            }

            try {
                const formData = new FormData(enquiryForm);
                const response = await fetch('/contact', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (!response.ok) {
                    if (result.errors) {
                        for (const [field, msg] of Object.entries(result.errors)) {
                            const input = enquiryForm.querySelector(`[name="${field}"]`);
                            if (input) input.classList.add('error');
                            const errSpan = enquiryForm.querySelector(`.error-${field}`);
                            if (errSpan) errSpan.textContent = msg;
                        }
                    }
                    showToast(result.error || 'Please correct the errors and try again.', 'error');
                } else {
                    enquiryForm.reset();
                    showToast(result.message || 'Enquiry submitted successfully! We will contact you soon.', 'success');
                    if (feedbackEl) {
                        feedbackEl.innerHTML = `<div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded font-medium text-sm mt-4">${result.message}</div>`;
                    }
                }
            } catch (err) {
                showToast('Failed to submit enquiry. Please check your connection or call us.', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        });
    }

    // ── 7. Global Toast Notification ──
    window.showToast = (message, type = 'success') => {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;

        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

    // ── 8. Freaky Highlights Interactive 3D Physics & Canvas ──
    const highlightsSection = document.getElementById('highlights');
    const freakyCards = document.querySelectorAll('.freaky-card');

    if (highlightsSection && freakyCards.length > 0) {
        // A. Scroll-Triggered Staggered In-View Reveal
        if ('IntersectionObserver' in window) {
            const cardObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        freakyCards.forEach((card, idx) => {
                            setTimeout(() => {
                                card.classList.add('is-revealed');
                                card.classList.remove('reveal-init');
                            }, idx * 120);
                        });
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            const cardsContainer = document.getElementById('freaky-cards-container');
            if (cardsContainer) cardObserver.observe(cardsContainer);
        } else {
            freakyCards.forEach(c => {
                c.classList.add('is-revealed');
                c.classList.remove('reveal-init');
            });
        }

        // B. Laser Border Beam Rotation
        let beamAngle = 0;
        let isSectionVisible = true;

        const animateLaserBeam = () => {
            if (isSectionVisible) {
                beamAngle = (beamAngle + 1.2) % 360;
                highlightsSection.style.setProperty('--beam-angle', `${beamAngle}deg`);
            }
            requestAnimationFrame(animateLaserBeam);
        };
        requestAnimationFrame(animateLaserBeam);

        // C. 3D Magnetic Tilt & Dynamic Mouse Glare
        freakyCards.forEach(card => {
            let bounds = null;

            const onMouseEnter = () => {
                bounds = card.getBoundingClientRect();
                card.style.transition = 'transform 0.12s ease-out, box-shadow 0.3s ease';
            };

            const onMouseMove = (e) => {
                if (!bounds) bounds = card.getBoundingClientRect();
                const mouseX = e.clientX - bounds.left;
                const mouseY = e.clientY - bounds.top;

                const centerX = bounds.width / 2;
                const centerY = bounds.height / 2;

                const rotateX = -((mouseY - centerY) / centerY) * 14;
                const rotateY = ((mouseX - centerX) / centerX) * 14;

                card.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale3d(1.04, 1.04, 1.04)`;
                card.style.setProperty('--mouse-x', `${mouseX}px`);
                card.style.setProperty('--mouse-y', `${mouseY}px`);
            };

            const onMouseLeave = () => {
                card.style.transition = 'transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.5s ease';
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            };

            card.addEventListener('mouseenter', onMouseEnter);
            card.addEventListener('mousemove', onMouseMove);
            card.addEventListener('mouseleave', onMouseLeave);
        });

        // D. Lightweight Ambient Canvas Dust Particles
        const canvas = document.getElementById('freaky-particle-canvas');
        if (canvas && canvas.getContext) {
            const ctx = canvas.getContext('2d');
            let particles = [];

            const resizeCanvas = () => {
                canvas.width = highlightsSection.offsetWidth;
                canvas.height = highlightsSection.offsetHeight;
            };
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas, { passive: true });

            const particleColors = [
                'rgba(200, 155, 60, ',   // Meridian Warm Gold
                'rgba(16, 36, 62, ',     // Deep Navy
                'rgba(179, 131, 40, ',   // Amber Bronze
                'rgba(3, 105, 161, '     // Sapphire Blue
            ];

            const count = Math.min(30, Math.floor(window.innerWidth / 40));
            for (let i = 0; i < count; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    radius: Math.random() * 2 + 0.8,
                    colorBase: particleColors[i % particleColors.length],
                    alpha: Math.random() * 0.25 + 0.1,
                    vx: (Math.random() - 0.5) * 0.35,
                    vy: -Math.random() * 0.5 - 0.15,
                    pulseSpeed: Math.random() * 0.02 + 0.01,
                    pulseOffset: Math.random() * Math.PI * 2
                });
            }

            let tick = 0;
            const drawParticles = () => {
                if (isSectionVisible) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    tick += 0.02;

                    for (let p of particles) {
                        p.x += p.vx;
                        p.y += p.vy;

                        if (p.y < -10) p.y = canvas.height + 10;
                        if (p.x < -10) p.x = canvas.width + 10;
                        if (p.x > canvas.width + 10) p.x = -10;

                        const currentAlpha = p.alpha + Math.sin(tick + p.pulseOffset) * 0.08;
                        ctx.beginPath();
                        ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                        ctx.fillStyle = `${p.colorBase}${Math.max(0.04, Math.min(0.4, currentAlpha))})`;
                        ctx.shadowBlur = 6;
                        ctx.shadowColor = `${p.colorBase}0.4)`;
                        ctx.fill();
                    }
                }
                requestAnimationFrame(drawParticles);
            };

            // Pause animations when section is offscreen to preserve battery & CPU
            if ('IntersectionObserver' in window) {
                const visObserver = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        isSectionVisible = entry.isIntersecting;
                    });
                }, { threshold: 0.05 });
                visObserver.observe(highlightsSection);
            }

            drawParticles();
        }
    }
});
