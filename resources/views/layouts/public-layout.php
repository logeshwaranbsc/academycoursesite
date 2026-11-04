<?php
/** @var array $settings */
$academyName = $settings['academy_name'] ?? 'Meridian Academy';
$logoUrl = $settings['logo_url'] ?? '';
$phone = $settings['phone'] ?? '+91 98765 43210';
$email = $settings['email'] ?? 'info@meridianacademy.com';
$address = $settings['address'] ?? '123, Education Hub, Koramangala, Bangalore – 560 095';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10243E">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">

    <title><?= e($title ?? "{$academyName} — Build Your Career With Confidence") ?></title>
    <meta name="description" content="<?= e($metaDescription ?? "{$academyName} offers industry-relevant professional courses with expert mentorship, practical training, and dedicated career support.") ?>">

    <!-- Google Fonts: Libre Baskerville + Source Sans 3 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">

    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="bg-white">

    <!-- Top Announcement Bar -->
    <div style="background-color: var(--color-navy); border-bottom: 1px solid rgba(255,255,255,0.1);" class="hidden lg:block">
        <div class="container-main flex items-center justify-between" style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
            <p style="font-size: 0.75rem; color: #D1D5DB; margin: 0;">
                <span style="color: var(--color-gold); font-weight: 600;">Address:</span> <?= e($address) ?>
            </p>
            <div class="flex items-center gap-6">
                <?php if (!empty($email)): ?>
                    <a href="mailto:<?= e($email) ?>" style="font-size: 0.75rem; color: #D1D5DB; display: flex; align-items: center; gap: 0.35rem;" class="hover:text-white">
                        <span>✉</span> <?= e($email) ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($phone)): ?>
                    <a href="tel:<?= e($phone) ?>" style="font-size: 0.75rem; color: #D1D5DB; display: flex; align-items: center; gap: 0.35rem;" class="hover:text-white">
                        <span>📞</span> <?= e($phone) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header style="position: sticky; top: 0; z-index: 50; background: rgba(255,255,255,0.98); backdrop-filter: blur(8px); border-bottom: 1px solid var(--color-gray-200); transition: all 0.3s ease;">
        <nav class="container-main flex items-center justify-between" style="height: 4.5rem;">
            <!-- Brand / Logo -->
            <a href="<?= base_url() ?>" class="flex items-center gap-3">
                <?php if (!empty($logoUrl)): ?>
                    <img src="<?= upload_url($logoUrl) ?>" alt="<?= e($academyName) ?>" style="height: 2.5rem; width: auto; object-fit: contain;">
                <?php else: ?>
                    <div class="flex items-center gap-2.5">
                        <div style="width: 2.25rem; height: 2.25rem; background-color: var(--color-navy); color: #fff; font-weight: bold; font-size: 1.125rem; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <?= e(mb_substr($academyName, 0, 1)) ?>
                        </div>
                        <span style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: var(--color-navy); letter-spacing: -0.01em;">
                            <?= e($academyName) ?>
                        </span>
                    </div>
                <?php endif; ?>
            </a>

            <!-- Desktop Navigation Links -->
            <div class="hidden lg:flex items-center gap-1">
                <?php
                $links = [
                    '/' => 'Home',
                    '/about' => 'About',
                    '/courses' => 'Courses',
                    '/placements' => 'Placements',
                    '/success-stories' => 'Success Stories',
                    '/gallery' => 'Gallery',
                    '/contact' => 'Contact',
                ];
                foreach ($links as $href => $label):
                    $isActive = ($href === '/' && $currentPath === '/') || ($href !== '/' && str_starts_with($currentPath, $href));
                ?>
                    <a href="<?= base_url(ltrim($href, '/')) ?>"
                       style="padding: 0.5rem 0.85rem; font-size: 0.875rem; font-weight: 600; color: <?= $isActive ? 'var(--color-gold)' : 'var(--color-charcoal)' ?>;"
                       class="hover:text-[var(--color-navy)]">
                        <?= e($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Header CTA & Mobile Button -->
            <div class="flex items-center gap-4">
                <a href="<?= base_url('contact') ?>" class="btn-secondary hidden sm:inline-flex btn-sm">
                    Enquire Now
                </a>

                <button id="mobile-menu-btn" class="lg:hidden p-2 text-gray-700 hover:text-gray-900 focus:outline-none" aria-label="Toggle menu" aria-expanded="false" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">
                    ☰
                </button>
            </div>
        </nav>

        <!-- Mobile Navigation Menu Dropdown -->
        <div id="mobile-menu" class="hidden lg:hidden" style="border-top: 1px solid var(--color-gray-200); background: #fff; padding: 1rem 1.25rem;">
            <div class="flex flex-col gap-2">
                <?php foreach ($links as $href => $label):
                    $isActive = ($href === '/' && $currentPath === '/') || ($href !== '/' && str_starts_with($currentPath, $href));
                ?>
                    <a href="<?= base_url(ltrim($href, '/')) ?>"
                       style="padding: 0.6rem 0.75rem; font-size: 0.9375rem; font-weight: 600; border-radius: 4px; color: <?= $isActive ? 'var(--color-gold)' : 'var(--color-charcoal)' ?>; background: <?= $isActive ? 'rgba(200,155,60,0.08)' : 'transparent' ?>;">
                        <?= e($label) ?>
                    </a>
                <?php endforeach; ?>
                <div style="padding-top: 0.75rem; margin-top: 0.5rem; border-top: 1px solid var(--color-gray-100);">
                    <a href="<?= base_url('contact') ?>" class="btn-secondary w-full text-center" style="display: block; width: 100%;">
                        Enquire Now
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Global Toast / Flash Messages -->
    <?php if (has_flash('success')): $f = get_flash('success'); ?>
        <div class="toast-container">
            <div class="toast toast-success"><?= e($f['message']) ?></div>
        </div>
    <?php endif; ?>
    <?php if (has_flash('error')): $f = get_flash('error'); ?>
        <div class="toast-container">
            <div class="toast toast-error"><?= e($f['message']) ?></div>
        </div>
    <?php endif; ?>

    <!-- Main Page Content -->
    <main>
        <?= $content ?>
    </main>

    <!-- Global Footer -->
    <footer style="background-color: var(--color-navy); color: #fff; padding-top: 4rem; padding-bottom: 2rem;">
        <div class="container-main">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10" style="margin-bottom: 3rem;">
                <!-- Column 1: Brand & Bio -->
                <div>
                    <div class="flex items-center gap-2.5" style="margin-bottom: 1.25rem;">
                        <div style="width: 2.25rem; height: 2.25rem; background-color: var(--color-gold); color: #fff; font-weight: bold; font-size: 1.125rem; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <?= e(mb_substr($academyName, 0, 1)) ?>
                        </div>
                        <span style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: #fff;">
                            <?= e($academyName) ?>
                        </span>
                    </div>
                    <p style="font-size: 0.875rem; color: #9CA3AF; line-height: 1.7; margin-bottom: 1.5rem;">
                        Building confident professionals through industry-relevant education, expert mentorship, and hands-on training.
                    </p>
                    <!-- Social Links -->
                    <div class="flex items-center gap-3">
                        <?php if (!empty($settings['facebook_url'])): ?>
                            <a href="<?= e($settings['facebook_url']) ?>" target="_blank" rel="noopener" style="width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #374151; color: #D1D5DB; border-radius: 4px;" aria-label="Facebook">fb</a>
                        <?php endif; ?>
                        <?php if (!empty($settings['instagram_url'])): ?>
                            <a href="<?= e($settings['instagram_url']) ?>" target="_blank" rel="noopener" style="width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #374151; color: #D1D5DB; border-radius: 4px;" aria-label="Instagram">ig</a>
                        <?php endif; ?>
                        <?php if (!empty($settings['linkedin_url'])): ?>
                            <a href="<?= e($settings['linkedin_url']) ?>" target="_blank" rel="noopener" style="width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #374151; color: #D1D5DB; border-radius: 4px;" aria-label="LinkedIn">in</a>
                        <?php endif; ?>
                        <?php if (!empty($settings['youtube_url'])): ?>
                            <a href="<?= e($settings['youtube_url']) ?>" target="_blank" rel="noopener" style="width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #374151; color: #D1D5DB; border-radius: 4px;" aria-label="YouTube">yt</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div>
                    <h4 style="color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem;">Quick Links</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.875rem; color: #9CA3AF;">
                        <li><a href="<?= base_url() ?>" class="hover:text-white">Home</a></li>
                        <li><a href="<?= base_url('about') ?>" class="hover:text-white">About Us</a></li>
                        <li><a href="<?= base_url('courses') ?>" class="hover:text-white">Course Catalog</a></li>
                        <li><a href="<?= base_url('placements') ?>" class="hover:text-white">Placement Record</a></li>
                        <li><a href="<?= base_url('success-stories') ?>" class="hover:text-white">Success Stories</a></li>
                        <li><a href="<?= base_url('gallery') ?>" class="hover:text-white">Campus Gallery</a></li>
                    </ul>
                </div>

                <!-- Column 3: Contact Details -->
                <div>
                    <h4 style="color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem;">Contact Us</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem; color: #9CA3AF;">
                        <li class="flex items-start gap-2.5">
                            <span style="color: var(--color-gold);">📍</span>
                            <span><?= e($address) ?></span>
                        </li>
                        <?php if (!empty($phone)): ?>
                            <li class="flex items-center gap-2.5">
                                <span style="color: var(--color-gold);">📞</span>
                                <a href="tel:<?= e($phone) ?>" class="hover:text-white"><?= e($phone) ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($email)): ?>
                            <li class="flex items-center gap-2.5">
                                <span style="color: var(--color-gold);">✉</span>
                                <a href="mailto:<?= e($email) ?>" class="hover:text-white"><?= e($email) ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($settings['whatsapp'])): ?>
                            <li class="flex items-center gap-2.5">
                                <span style="color: var(--color-gold);">💬</span>
                                <a href="https://wa.me/<?= preg_replace('/\D/', '', $settings['whatsapp']) ?>" target="_blank" rel="noopener" class="hover:text-white">WhatsApp Counselling</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Column 4: Admissions -->
                <div>
                    <h4 style="color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem;">Admissions Desk</h4>
                    <p style="font-size: 0.875rem; color: #9CA3AF; line-height: 1.7; margin-bottom: 1.25rem;">
                        New batches start every month. Speak with our career counselor to reserve your seat.
                    </p>
                    <a href="<?= base_url('contact') ?>" class="btn-outline-gold btn-sm" style="width: 100%; text-align: center; display: inline-block;">
                        Speak With An Advisor
                    </a>
                </div>
            </div>

            <!-- Bottom Copyright & Admin link -->
            <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; font-size: 0.8125rem; color: #6B7280;">
                <p>&copy; <?= date('Y') ?> <?= e($academyName) ?>. All rights reserved.</p>
                <a href="<?= base_url('admin/login') ?>" class="hover:text-gray-300" style="font-size: 0.75rem;">Admin Access</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
