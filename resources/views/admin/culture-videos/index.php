<?php
/** @var array $pagination */
$items = $pagination['data'] ?? [];
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
?>
<div>
    <div class="flex items-center justify-between gap-4 flex-wrap" style="margin-bottom: 1.5rem;">
        <form method="GET" action="<?= base_url('admin/culture-videos') ?>" class="flex items-center gap-2">
            <input type="text" name="search" placeholder="Search videos..." value="<?= e($search ?? '') ?>" class="form-input" style="width: 250px; padding: 0.4rem 0.75rem;">
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin/culture-videos') ?>" class="btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <button type="button" onclick="openModal('create-video-modal')" class="btn-secondary btn-sm">
            + Add Culture Video
        </button>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Thumbnail</th>
                    <th>Video Title</th>
                    <th>YouTube URL</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item):
                        $thumb = !empty($item['thumbnail_url']) ? upload_url($item['thumbnail_url']) : youtube_thumbnail($item['video_url']);
                    ?>
                        <tr>
                            <td>
                                <?php if (!empty($thumb)): ?>
                                    <img src="<?= e($thumb) ?>" alt="Thumb" style="width: 70px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 70px; height: 40px; background: #000; color:#fff; display:flex; align-items:center; justify-content:center; border-radius:4px;">▶</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($item['title']) ?></strong>
                                <?php if (!empty($item['description'])): ?>
                                    <div style="font-size: 0.75rem; color: #6B7280;"><?= e(mb_strimwidth($item['description'], 0, 70, '...')) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= e($item['video_url']) ?>" target="_blank" rel="noopener" style="color: var(--color-navy); font-size: 0.8125rem;">
                                    Watch Video &rarr;
                                </a>
                            </td>
                            <td>
                                <button type="button" class="btn-toggle-status" data-url="<?= base_url('admin/culture-videos/' . $item['id'] . '/toggle') ?>" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <?php if ($item['status'] === 'active'): ?>
                                        <span class="status-badge badge-green">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-red">Inactive</span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" onclick="editVideo(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline btn-sm">Edit</button>
                                <button type="button" onclick="confirmDelete('<?= base_url('admin/culture-videos/' . $item['id'] . '/delete') ?>', '<?= e($item['title']) ?>')" class="btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2.5rem; color: #6B7280;">
                            No culture videos found. Click "+ Add Culture Video" to add one.
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
                    <a href="<?= base_url('admin/culture-videos?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">&larr; Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?= base_url('admin/culture-videos?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div id="create-video-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Add Culture Video</h3>
            <button type="button" onclick="closeModal('create-video-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="<?= base_url('admin/culture-videos') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Video Title <span style="color:red;">*</span></label>
                <input type="text" name="title" class="form-input" placeholder="e.g. Annual Hackathon 2025 Highlights" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">YouTube Video URL <span style="color:red;">*</span></label>
                <input type="url" name="video_url" class="form-input" placeholder="https://www.youtube.com/watch?v=..." required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-input" rows="2" placeholder="Brief summary of the video"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Custom Thumbnail (Optional)</label>
                    <input type="file" name="thumbnail" accept="image/*" class="form-input">
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
                <button type="button" onclick="closeModal('create-video-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Save Video</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-video-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Edit Culture Video</h3>
            <button type="button" onclick="closeModal('edit-video-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="edit-video-form" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Video Title <span style="color:red;">*</span></label>
                <input type="text" id="edit-vid-title" name="title" class="form-input" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">YouTube Video URL <span style="color:red;">*</span></label>
                <input type="url" id="edit-vid-url" name="video_url" class="form-input" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Description</label>
                <textarea id="edit-vid-desc" name="description" class="form-input" rows="2"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Replace Thumbnail (Optional)</label>
                    <input type="file" name="thumbnail" accept="image/*" class="form-input">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select id="edit-vid-status" name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('edit-video-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Update Video</button>
            </div>
        </form>
    </div>
</div>

<script>
function editVideo(item) {
    const form = document.getElementById('edit-video-form');
    form.action = '<?= base_url("admin/culture-videos/") ?>' + item.id;

    document.getElementById('edit-vid-title').value = item.title || '';
    document.getElementById('edit-vid-url').value = item.video_url || '';
    document.getElementById('edit-vid-desc').value = item.description || '';
    document.getElementById('edit-vid-status').value = item.status || 'active';

    openModal('edit-video-modal');
}
</script>
