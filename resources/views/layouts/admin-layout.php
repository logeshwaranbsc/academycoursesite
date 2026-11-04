<?php
$currentUser = current_user();
$adminName = $currentUser['name'] ?? 'Administrator';
$adminEmail = $currentUser['email'] ?? 'admin@gmail.com';
$activeNav = $currentNav ?? 'dashboard';

$navItems = [
    'dashboard' => ['label' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => '📊'],
    'hero-banners' => ['label' => 'Hero Banners', 'url' => '/admin/banners', 'icon' => '🖼️'],
    'courses' => ['label' => 'Courses', 'url' => '/admin/courses', 'icon' => '📚'],
    'batches' => ['label' => 'Batches', 'url' => '/admin/batches', 'icon' => '📅'],
    'placements' => ['label' => 'Placed Students', 'url' => '/admin/placements', 'icon' => '🎓'],
    'success-stories' => ['label' => 'Success Stories', 'url' => '/admin/success-stories', 'icon' => '🌟'],
    'testimonials' => ['label' => 'Testimonials', 'url' => '/admin/testimonials', 'icon' => '💬'],
    'culture-videos' => ['label' => 'Culture Videos', 'url' => '/admin/culture-videos', 'icon' => '🎬'],
    'gallery' => ['label' => 'Gallery', 'url' => '/admin/gallery', 'icon' => '📷'],
    'enquiries' => ['label' => 'Enquiries', 'url' => '/admin/enquiries', 'icon' => '✉️'],
    'settings' => ['label' => 'Website Settings', 'url' => '/admin/settings', 'icon' => '⚙️'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Admin Dashboard — Digital Teenz Academy') ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .admin-main-content {
            padding: 2rem;
            flex: 1;
        }
    </style>
</head>
<body class="bg-gray-50">

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <div style="width: 2.25rem; height: 2.25rem; background-color: var(--color-gold); color: #fff; font-weight: bold; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                    M
                </div>
                <div>
                    <h3 style="color: #fff; font-size: 1rem; margin: 0; font-family: var(--font-heading);">Meridian</h3>
                    <span style="font-size: 0.75rem; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.05em;">Admin Portal</span>
                </div>
            </div>

            <nav style="padding: 1rem 0; flex: 1; overflow-y: auto;">
                <?php foreach ($navItems as $key => $item): ?>
                    <a href="<?= base_url(ltrim($item['url'], '/')) ?>" class="<?= $activeNav === $key ? 'active' : '' ?>">
                        <span><?= $item['icon'] ?></span>
                        <span><?= e($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div style="padding: 1.25rem; border-top: 1px solid rgba(255,255,255,0.08); font-size: 0.8125rem;">
                <div style="color: #D1D5DB; font-weight: 600;"><?= e($adminName) ?></div>
                <div style="color: #9CA3AF; font-size: 0.75rem;"><?= e($adminEmail) ?></div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-content">
            <!-- TopBar -->
            <header class="admin-topbar">
                <div class="flex items-center gap-4">
                    <h2 style="font-size: 1.25rem; margin: 0; color: var(--color-navy);"><?= e($title ?? 'Dashboard') ?></h2>
                </div>

                <div class="flex items-center gap-4">
                    <a href="<?= base_url() ?>" target="_blank" class="btn-outline btn-sm flex items-center gap-1.5" style="border-radius: 4px;">
                        <span>🌐 View Website</span>
                    </a>

                    <form action="<?= base_url('admin/logout') ?>" method="POST" style="margin: 0;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-danger btn-sm" style="border-radius: 4px; cursor: pointer;">
                            Sign Out
                        </button>
                    </form>
                </div>
            </header>

            <!-- Flash Toast Alerts -->
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

            <!-- Page Body -->
            <main class="admin-main-content">
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Global Delete Confirmation Modal -->
    <div id="delete-confirm-modal" class="modal-backdrop">
        <div class="modal-box" style="max-width: 440px; padding: 2rem;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 3.5rem; height: 3.5rem; background-color: #fee2e2; color: #dc2626; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-bottom: 1rem;">
                    ⚠️
                </div>
                <h3 style="font-size: 1.25rem; color: #111827; margin-bottom: 0.5rem;">Confirm Deletion</h3>
                <p style="font-size: 0.875rem; color: #6B7280; margin: 0;">
                    Are you sure you want to permanently delete <strong id="delete-item-name">this item</strong>? This action cannot be undone.
                </p>
            </div>

            <form id="delete-confirm-form" method="POST" class="flex items-center justify-end gap-3">
                <?= csrf_field() ?>
                <button type="button" onclick="closeModal('delete-confirm-modal')" class="btn-outline btn-sm">
                    Cancel
                </button>
                <button type="submit" class="btn-danger btn-sm">
                    Confirm Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= asset('js/main.js') ?>"></script>
    <script src="<?= base_url('admin-assets/admin.js') ?>"></script>
</body>
</html>
