/**
 * Meridian Academy — Public Interactive Client JS (Vanilla JS)
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
});
