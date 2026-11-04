<?php
/** @var array $heroBanners */
$banners = $heroBanners ?? [];
?>
<section class="hero-slider">
    <!-- Slides Container -->
    <div style="position: absolute; inset: 0; z-index: 1;">
        <?php if (!empty($banners)): ?>
            <?php foreach ($banners as $index => $banner): ?>
                <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>">
                    <?php if (!empty($banner['image_url'])): ?>
                        <img src="<?= upload_url($banner['image_url']) ?>" alt="<?= e($banner['title'] ?? 'Hero Banner') ?>">
                    <?php else: ?>
                        <div style="position: absolute; inset: 0; background-color: var(--color-navy); z-index: 1;"></div>
                    <?php endif; ?>

                    <div class="hero-overlay"></div>

                    <div class="container-main" style="position: relative; z-index: 20; padding-top: 6rem; padding-bottom: 6rem; width: 100%;">
                        <div style="max-width: 720px;">
                            <span class="section-label" style="display: inline-block; padding: 0.35rem 0.85rem; background: rgba(200,155,60,0.15); border: 1px solid rgba(200,155,60,0.3); border-radius: 4px; margin-bottom: 1.25rem;">
                                Premier Career Training
                            </span>
                            <h1 style="color: #fff; margin-bottom: 1.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.4);">
                                <?= e(!empty($banner['title']) ? $banner['title'] : 'Build Your Career With Confidence') ?>
                            </h1>
                            <p style="font-size: 1.125rem; color: #E5E7EB; margin-bottom: 2.25rem; line-height: 1.8; text-shadow: 0 1px 2px rgba(0,0,0,0.4);">
                                <?= e(!empty($banner['subtitle']) ? $banner['subtitle'] : 'Industry-relevant curriculum, 1-on-1 expert mentors, and guaranteed career placement assistance designed to transition ambitious students into high-impact roles.') ?>
                            </p>
                            <div class="flex items-center gap-4 flex-wrap">
                                <?php if (!empty($banner['button_text'])): ?>
                                    <a href="<?= !empty($banner['button_link']) ? e($banner['button_link']) : base_url('courses') ?>" class="btn-secondary">
                                        <?= e($banner['button_text']) ?> &rarr;
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('courses') ?>" class="btn-secondary">
                                        Explore Programmes &rarr;
                                    </a>
                                <?php endif; ?>
                                <a href="<?= base_url('contact') ?>" class="btn-outline-gold">
                                    Book Free Counselling
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="hero-slide active">
                <div style="position: absolute; inset: 0; background-color: var(--color-navy); z-index: 1;"></div>
                <div class="hero-overlay"></div>
                <div class="container-main" style="position: relative; z-index: 20; padding-top: 6rem; padding-bottom: 6rem; width: 100%;">
                    <div style="max-width: 720px;">
                        <span class="section-label" style="display: inline-block; padding: 0.35rem 0.85rem; background: rgba(200,155,60,0.15); border: 1px solid rgba(200,155,60,0.3); border-radius: 4px; margin-bottom: 1.25rem;">
                            Premier Career Training
                        </span>
                        <h1 style="color: #fff; margin-bottom: 1.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.4);">
                            Build Your Career With Confidence
                        </h1>
                        <p style="font-size: 1.125rem; color: #E5E7EB; margin-bottom: 2.25rem; line-height: 1.8; text-shadow: 0 1px 2px rgba(0,0,0,0.4);">
                            Industry-relevant curriculum, 1-on-1 expert mentors, and guaranteed career placement assistance designed to transition ambitious students into high-impact roles.
                        </p>
                        <div class="flex items-center gap-4 flex-wrap">
                            <a href="<?= base_url('courses') ?>" class="btn-secondary">
                                Explore Programmes &rarr;
                            </a>
                            <a href="<?= base_url('contact') ?>" class="btn-outline-gold">
                                Book Free Counselling
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Prev / Next Slider Controls -->
    <?php if (count($banners) > 1): ?>
        <button id="hero-prev" style="position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%); z-index: 30; width: 2.75rem; height: 2.75rem; border-radius: 50%; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); color: #fff; font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);" aria-label="Previous Slide">
            &lsaquo;
        </button>
        <button id="hero-next" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); z-index: 30; width: 2.75rem; height: 2.75rem; border-radius: 50%; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); color: #fff; font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);" aria-label="Next Slide">
            &rsaquo;
        </button>
    <?php endif; ?>
</section>
