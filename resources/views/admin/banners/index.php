<?php
/** @var array $pagination */
$items = $pagination['data'] ?? [];
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
?>
<div>
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between gap-4 flex-wrap" style="margin-bottom: 1.5rem;">
        <form method="GET" action="<?= base_url('admin/banners') ?>" class="flex items-center gap-2">
            <input type="text" name="search" placeholder="Search banners..." value="<?= e($search ?? '') ?>" class="form-input" style="width: 250px; padding: 0.4rem 0.75rem;">
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin/banners') ?>" class="btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <button type="button" onclick="openModal('create-banner-modal')" class="btn-secondary btn-sm">
            + Add Hero Banner
        </button>
    </div>

    <!-- Data Table -->
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Banner</th>
                    <th>Title & Subtitle</th>
                    <th>CTA Button</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?= upload_url($item['image_url']) ?>" alt="Banner" style="width: 70px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($item['title'] ?: 'Untitled Banner') ?></strong>
                                <?php if (!empty($item['subtitle'])): ?>
                                    <div style="font-size: 0.75rem; color: #6B7280;"><?= e(mb_strimwidth($item['subtitle'], 0, 60, '...')) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($item['button_text'])): ?>
                                    <span class="badge-gold"><?= e($item['button_text']) ?></span>
                                <?php else: ?>
                                    <span style="color: #9CA3AF; font-size: 0.75rem;">None</span>
                                <?php endif; ?>
                            </td>
                            <td><?= (int)$item['display_order'] ?></td>
                            <td>
                                <button type="button" class="btn-toggle-status" data-url="<?= base_url('admin/banners/' . $item['id'] . '/toggle') ?>" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <?php if ($item['status'] === 'active'): ?>
                                        <span class="status-badge badge-green">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-red">Inactive</span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" onclick="editBanner(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline btn-sm">
                                    Edit
                                </button>
                                <button type="button" onclick="confirmDelete('<?= base_url('admin/banners/' . $item['id'] . '/delete') ?>', '<?= e($item['title'] ?: 'this banner') ?>')" class="btn-danger btn-sm">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #6B7280;">
                            No hero banners found. Click "+ Add Hero Banner" to create one.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($lastPage > 1): ?>
        <div class="flex items-center justify-between" style="margin-top: 1.5rem; font-size: 0.875rem;">
            <span style="color: #6B7280;">Page <?= $currentPage ?> of <?= $lastPage ?></span>
            <div class="flex items-center gap-2">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= base_url('admin/banners?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">&larr; Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?= base_url('admin/banners?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div id="create-banner-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Add Hero Banner</h3>
            <button type="button" onclick="closeModal('create-banner-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="<?= base_url('admin/banners') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Banner Image <span style="color:red;">*</span></label>
                <input type="file" name="image" accept="image/*" class="form-input" data-preview="create-banner-preview" required>
                <img id="create-banner-preview" src="" alt="Preview" style="display:none; width: 100%; height: 120px; object-fit: cover; margin-top: 0.5rem; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-input" placeholder="e.g. Build Your Career With Confidence">
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Subtitle</label>
                <textarea name="subtitle" class="form-input" rows="2" placeholder="Brief supporting description"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Button Text</label>
                    <input type="text" name="button_text" class="form-input" placeholder="e.g. Explore Courses">
                </div>
                <div>
                    <label class="form-label">Button Link</label>
                    <input type="text" name="button_link" class="form-input" placeholder="e.g. /courses">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Display Order</label>
                    <input type="number" name="display_order" class="form-input" value="0">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('create-banner-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Save Banner</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-banner-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Edit Hero Banner</h3>
            <button type="button" onclick="closeModal('edit-banner-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="edit-banner-form" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Replace Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="form-input" data-preview="edit-banner-preview">
                <img id="edit-banner-preview" src="" alt="Current Banner" style="width: 100%; height: 120px; object-fit: cover; margin-top: 0.5rem; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Title</label>
                <input type="text" id="edit-banner-title" name="title" class="form-input">
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Subtitle</label>
                <textarea id="edit-banner-subtitle" name="subtitle" class="form-input" rows="2"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Button Text</label>
                    <input type="text" id="edit-banner-button-text" name="button_text" class="form-input">
                </div>
                <div>
                    <label class="form-label">Button Link</label>
                    <input type="text" id="edit-banner-button-link" name="button_link" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Display Order</label>
                    <input type="number" id="edit-banner-order" name="display_order" class="form-input">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select id="edit-banner-status" name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('edit-banner-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Update Banner</button>
            </div>
        </form>
    </div>
</div>

<script>
function editBanner(item) {
    const form = document.getElementById('edit-banner-form');
    form.action = '<?= base_url("admin/banners/") ?>' + item.id;

    document.getElementById('edit-banner-title').value = item.title || '';
    document.getElementById('edit-banner-subtitle').value = item.subtitle || '';
    document.getElementById('edit-banner-button-text').value = item.button_text || '';
    document.getElementById('edit-banner-button-link').value = item.button_link || '';
    document.getElementById('edit-banner-order').value = item.display_order || 0;
    document.getElementById('edit-banner-status').value = item.status || 'active';

    const prev = document.getElementById('edit-banner-preview');
    if (item.image_url) {
        prev.src = '<?= base_url() ?>' + '/' + item.image_url.replace(/^\//, '');
        prev.style.display = 'block';
    } else {
        prev.style.display = 'none';
    }

    openModal('edit-banner-modal');
}
</script>
