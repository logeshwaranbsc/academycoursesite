<?php
/** @var array $pagination */
/** @var array $categories */
$items = $pagination['data'] ?? [];
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
$selectedCategory = $selectedCategory ?? '';
?>
<div>
    <div class="flex items-center justify-between gap-4 flex-wrap" style="margin-bottom: 1.5rem;">
        <form method="GET" action="<?= base_url('admin/gallery') ?>" class="flex items-center gap-2 flex-wrap">
            <input type="text" name="search" placeholder="Search title..." value="<?= e($search ?? '') ?>" class="form-input" style="width: 200px; padding: 0.4rem 0.75rem;">

            <select name="category" class="form-input" style="width: 160px; padding: 0.4rem 0.75rem;">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= $selectedCategory === $cat ? 'selected' : '' ?>>
                        <?= e($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-primary btn-sm">Filter</button>
            <?php if (!empty($search) || !empty($selectedCategory)): ?>
                <a href="<?= base_url('admin/gallery') ?>" class="btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <button type="button" onclick="openModal('create-gallery-modal')" class="btn-secondary btn-sm">
            + Upload Photo
        </button>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Photo</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Display Order</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= upload_url($item['image_url']) ?>" alt="Gallery" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><strong><?= e($item['title']) ?></strong></td>
                            <td>
                                <?php if (!empty($item['category'])): ?>
                                    <span class="badge-gold"><?= e($item['category']) ?></span>
                                <?php else: ?>
                                    <span style="color: #9CA3AF; font-size: 0.75rem;">General</span>
                                <?php endif; ?>
                            </td>
                            <td><?= (int)$item['display_order'] ?></td>
                            <td>
                                <button type="button" class="btn-toggle-status" data-url="<?= base_url('admin/gallery/' . $item['id'] . '/toggle') ?>" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <?php if ($item['status'] === 'active'): ?>
                                        <span class="status-badge badge-green">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-red">Inactive</span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" onclick="editGallery(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline btn-sm">Edit</button>
                                <button type="button" onclick="confirmDelete('<?= base_url('admin/gallery/' . $item['id'] . '/delete') ?>', '<?= e($item['title']) ?>')" class="btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #6B7280;">
                            No gallery photos found. Click "+ Upload Photo" to add images.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($lastPage > 1): ?>
        <div class="flex items-center justify-between" style="margin-top: 1.5rem; font-size: 0.875rem;">
            <span style="color: #6B7280;">Page <?= $currentPage ?> of <?= $lastPage ?></span>
            <div class="flex items-center gap-2">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= base_url('admin/gallery?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '')) ?>" class="btn-outline btn-sm">&larr; Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?= base_url('admin/gallery?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '')) ?>" class="btn-outline btn-sm">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div id="create-gallery-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Upload Photo</h3>
            <button type="button" onclick="closeModal('create-gallery-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="<?= base_url('admin/gallery') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Photo <span style="color:red;">*</span></label>
                <input type="file" name="image" accept="image/*" class="form-input" data-preview="create-gal-preview" required>
                <img id="create-gal-preview" src="" alt="Preview" style="display:none; width: 100%; height: 140px; object-fit: cover; margin-top: 0.5rem; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Title <span style="color:red;">*</span></label>
                <input type="text" name="title" class="form-input" placeholder="e.g. Hackathon Team 2025" required>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-input" placeholder="e.g. Events / Workshops / Graduation">
                </div>
                <div>
                    <label class="form-label">Display Order</label>
                    <input type="number" name="display_order" class="form-input" value="0">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('create-gallery-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Save Photo</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-gallery-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Edit Photo Details</h3>
            <button type="button" onclick="closeModal('edit-gallery-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="edit-gallery-form" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Replace Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="form-input" data-preview="edit-gal-preview">
                <img id="edit-gal-preview" src="" alt="Photo" style="width: 100%; height: 140px; object-fit: cover; margin-top: 0.5rem; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Title <span style="color:red;">*</span></label>
                <input type="text" id="edit-gal-title" name="title" class="form-input" required>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Category</label>
                    <input type="text" id="edit-gal-category" name="category" class="form-input">
                </div>
                <div>
                    <label class="form-label">Display Order</label>
                    <input type="number" id="edit-gal-order" name="display_order" class="form-input">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Status</label>
                <select id="edit-gal-status" name="status" class="form-input">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('edit-gallery-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Update Photo</button>
            </div>
        </form>
    </div>
</div>

<script>
function editGallery(item) {
    const form = document.getElementById('edit-gallery-form');
    form.action = '<?= base_url("admin/gallery/") ?>' + item.id;

    document.getElementById('edit-gal-title').value = item.title || '';
    document.getElementById('edit-gal-category').value = item.category || '';
    document.getElementById('edit-gal-order').value = item.display_order || 0;
    document.getElementById('edit-gal-status').value = item.status || 'active';

    const prev = document.getElementById('edit-gal-preview');
    if (item.image_url) {
        prev.src = '<?= base_url() ?>' + '/' + item.image_url.replace(/^\//, '');
        prev.style.display = 'block';
    } else {
        prev.style.display = 'none';
    }

    openModal('edit-gallery-modal');
}
</script>
