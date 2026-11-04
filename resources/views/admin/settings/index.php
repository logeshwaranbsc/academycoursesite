<?php
/** @var array $settings */
$academyName = $settings['academy_name'] ?? 'Meridian Academy';
$logoUrl = $settings['logo_url'] ?? '';
$email = $settings['email'] ?? '';
$phone = $settings['phone'] ?? '';
$whatsapp = $settings['whatsapp'] ?? '';
$address = $settings['address'] ?? '';
$facebook = $settings['facebook_url'] ?? '';
$instagram = $settings['instagram_url'] ?? '';
$linkedin = $settings['linkedin_url'] ?? '';
$youtube = $settings['youtube_url'] ?? '';
?>
<div class="card" style="max-width: 800px; padding: 2.25rem;">
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; color: var(--color-navy); margin-bottom: 0.35rem;">Academy & Website Settings</h3>
        <p style="font-size: 0.875rem; color: #6B7280; margin: 0;">
            Updates made here immediately reflect on the public website header, announcement bar, footer, and contact desk.
        </p>
    </div>

    <form action="<?= base_url('admin/settings') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- Brand Details -->
        <div style="margin-bottom: 1.5rem;">
            <label class="form-label">Academy Name <span style="color:red;">*</span></label>
            <input type="text" name="academy_name" value="<?= e($academyName) ?>" class="form-input" required>
        </div>

        <!-- Logo Upload -->
        <div style="margin-bottom: 1.5rem; padding: 1.25rem; background: #f9fafb; border: 1px dashed var(--color-gray-300); border-radius: 6px;">
            <label class="form-label">Academy Logo</label>
            <div class="flex items-center gap-6" style="margin-top: 0.5rem;">
                <div style="width: 120px; height: 50px; background: #fff; border: 1px solid var(--color-gray-200); border-radius: 4px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 0.25rem;">
                    <?php if (!empty($logoUrl)): ?>
                        <img id="logo-preview-img" src="<?= upload_url($logoUrl) ?>" alt="Logo" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                    <?php else: ?>
                        <span id="logo-placeholder" style="font-size: 0.75rem; color: #9CA3AF;">No logo</span>
                        <img id="logo-preview-img" src="" alt="Preview" style="display: none; max-height: 100%; max-width: 100%; object-fit: contain;">
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <input type="file" name="logo" accept="image/*" class="form-input" data-preview="logo-preview-img">
                    <p style="font-size: 0.75rem; color: #6B7280; margin-top: 0.25rem;">PNG, SVG, or WEBP recommended. Max 2MB.</p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <h4 style="font-size: 1rem; color: var(--color-navy); margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-gray-200);">
            Contact Coordinates
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" style="margin-bottom: 1rem;">
            <div>
                <label class="form-label">Official Email</label>
                <input type="email" name="email" value="<?= e($email) ?>" class="form-input" placeholder="info@academy.com">
            </div>
            <div>
                <label class="form-label">Contact Phone</label>
                <input type="text" name="phone" value="<?= e($phone) ?>" class="form-input" placeholder="+91 98765 43210">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" style="margin-bottom: 1rem;">
            <div>
                <label class="form-label">WhatsApp Number</label>
                <input type="text" name="whatsapp" value="<?= e($whatsapp) ?>" class="form-input" placeholder="+91 98765 43210">
            </div>
            <div>
                <label class="form-label">Physical Campus Address</label>
                <input type="text" name="address" value="<?= e($address) ?>" class="form-input" placeholder="Building, Street, City, Pincode">
            </div>
        </div>

        <!-- Social Media Links -->
        <h4 style="font-size: 1rem; color: var(--color-navy); margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-gray-200);">
            Social Media Handles
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" style="margin-bottom: 1rem;">
            <div>
                <label class="form-label">Facebook URL</label>
                <input type="url" name="facebook_url" value="<?= e($facebook) ?>" class="form-input" placeholder="https://facebook.com/...">
            </div>
            <div>
                <label class="form-label">Instagram URL</label>
                <input type="url" name="instagram_url" value="<?= e($instagram) ?>" class="form-input" placeholder="https://instagram.com/...">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" style="margin-bottom: 2rem;">
            <div>
                <label class="form-label">LinkedIn URL</label>
                <input type="url" name="linkedin_url" value="<?= e($linkedin) ?>" class="form-input" placeholder="https://linkedin.com/company/...">
            </div>
            <div>
                <label class="form-label">YouTube URL</label>
                <input type="url" name="youtube_url" value="<?= e($youtube) ?>" class="form-input" placeholder="https://youtube.com/@...">
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem;">
                Save Settings
            </button>
        </div>
    </form>
</div>
