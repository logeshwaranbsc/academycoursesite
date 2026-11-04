<?php
/** @var array $pagination */
$items = $pagination['data'] ?? [];
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
?>
<div>
    <div class="flex items-center justify-between gap-4 flex-wrap" style="margin-bottom: 1.5rem;">
        <form method="GET" action="<?= base_url('admin/success-stories') ?>" class="flex items-center gap-2">
            <input type="text" name="search" placeholder="Search stories..." value="<?= e($search ?? '') ?>" class="form-input" style="width: 250px; padding: 0.4rem 0.75rem;">
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin/success-stories') ?>" class="btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <button type="button" onclick="openModal('create-story-modal')" class="btn-secondary btn-sm">
            + Add Success Story
        </button>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Thumbnail</th>
                    <th>Story Title</th>
                    <th>Student Name</th>
                    <th>Video URL</th>
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
                                    <img src="<?= upload_url($item['image_url']) ?>" alt="Story" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 40px; background: var(--color-navy); color:#fff; display:flex; align-items:center; justify-content:center; border-radius:4px; font-weight:bold;">
                                        <?= e(get_initials($item['student_name'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($item['title']) ?></strong>
                                <div style="font-size: 0.75rem; color: #6B7280;"><?= e(mb_strimwidth($item['description'], 0, 70, '...')) ?></div>
                            </td>
                            <td><?= e($item['student_name']) ?></td>
                            <td>
                                <?php if (!empty($item['video_url'])): ?>
                                    <a href="<?= e($item['video_url']) ?>" target="_blank" rel="noopener" style="color: var(--color-navy); font-weight: 600; font-size: 0.8125rem;">Watch Video &rarr;</a>
                                <?php else: ?>
                                    <span style="color: #9CA3AF; font-size: 0.75rem;">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn-toggle-status" data-url="<?= base_url('admin/success-stories/' . $item['id'] . '/toggle') ?>" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <?php if ($item['status'] === 'active'): ?>
                                        <span class="status-badge badge-green">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-red">Inactive</span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" onclick="editStory(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline btn-sm">Edit</button>
                                <button type="button" onclick="confirmDelete('<?= base_url('admin/success-stories/' . $item['id'] . '/delete') ?>', '<?= e($item['title']) ?>')" class="btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #6B7280;">
                            No success stories found. Click "+ Add Success Story" to publish one.
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
                    <a href="<?= base_url('admin/success-stories?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">&larr; Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?= base_url('admin/success-stories?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div id="create-story-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Add Success Story</h3>
            <button type="button" onclick="closeModal('create-story-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="<?= base_url('admin/success-stories') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Story Headline / Title <span style="color:red;">*</span></label>
                <input type="text" name="title" class="form-input" placeholder="e.g. Mechanical Graduate to Lead Full Stack Engineer at TechCorp" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Student Name <span style="color:red;">*</span></label>
                <input type="text" name="student_name" class="form-input" placeholder="e.g. Amit Kumar" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Story Narrative <span style="color:red;">*</span></label>
                <textarea name="description" class="form-input" rows="4" placeholder="Detail their journey, challenge, effort, and successful transformation..." required></textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Video URL (Optional, YouTube/Vimeo)</label>
                <input type="url" name="video_url" class="form-input" placeholder="https://www.youtube.com/watch?v=...">
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Student Photo / Thumbnail</label>
                    <input type="file" name="image" accept="image/*" class="form-input">
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
                <button type="button" onclick="closeModal('create-story-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Save Story</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-story-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Edit Success Story</h3>
            <button type="button" onclick="closeModal('edit-story-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="edit-story-form" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Story Headline / Title <span style="color:red;">*</span></label>
                <input type="text" id="edit-story-title" name="title" class="form-input" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Student Name <span style="color:red;">*</span></label>
                <input type="text" id="edit-story-name" name="student_name" class="form-input" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Story Narrative <span style="color:red;">*</span></label>
                <textarea id="edit-story-description" name="description" class="form-input" rows="4" required></textarea>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Video URL (Optional)</label>
                <input type="url" id="edit-story-video" name="video_url" class="form-input">
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Replace Photo (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="form-input">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select id="edit-story-status" name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('edit-story-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Update Story</button>
            </div>
        </form>
    </div>
</div>

<script>
function editStory(item) {
    const form = document.getElementById('edit-story-form');
    form.action = '<?= base_url("admin/success-stories/") ?>' + item.id;

    document.getElementById('edit-story-title').value = item.title || '';
    document.getElementById('edit-story-name').value = item.student_name || '';
    document.getElementById('edit-story-description').value = item.description || '';
    document.getElementById('edit-story-video').value = item.video_url || '';
    document.getElementById('edit-story-status').value = item.status || 'active';

    openModal('edit-story-modal');
}
</script>
