<?php
/** @var array $pagination */
$items = $pagination['data'] ?? [];
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
?>
<div>
    <div class="flex items-center justify-between gap-4 flex-wrap" style="margin-bottom: 1.5rem;">
        <form method="GET" action="<?= base_url('admin/courses') ?>" class="flex items-center gap-2">
            <input type="text" name="search" placeholder="Search courses..." value="<?= e($search ?? '') ?>" class="form-input" style="width: 250px; padding: 0.4rem 0.75rem;">
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin/courses') ?>" class="btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <button type="button" onclick="openModal('create-course-modal')" class="btn-secondary btn-sm">
            + Add New Course
        </button>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Image</th>
                    <th>Course Title</th>
                    <th>Duration & Mode</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $course): ?>
                        <tr>
                            <td>
                                <?php if (!empty($course['image_url'])): ?>
                                    <img src="<?= upload_url($course['image_url']) ?>" alt="Course" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 40px; background: var(--color-navy); color:#fff; display:flex; align-items:center; justify-content:center; border-radius:4px; font-size:0.75rem;">📚</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($course['title']) ?></strong>
                                <div style="font-size: 0.75rem; color: #6B7280;"><?= e(mb_strimwidth(strip_tags($course['description']), 0, 70, '...')) ?></div>
                            </td>
                            <td>
                                <span class="badge-navy"><?= e($course['duration']) ?></span>
                                <span class="badge-gold" style="margin-left: 0.25rem;"><?= e($course['mode']) ?></span>
                            </td>
                            <td>
                                <strong><?= format_price($course['price']) ?></strong>
                            </td>
                            <td>
                                <button type="button" class="btn-toggle-status" data-url="<?= base_url('admin/courses/' . $course['id'] . '/toggle') ?>" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <?php if ($course['status'] === 'active'): ?>
                                        <span class="status-badge badge-green">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-red">Inactive</span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="<?= base_url('courses/' . $course['id']) ?>" target="_blank" class="btn-outline btn-sm">Preview</a>
                                <button type="button" onclick="editCourse(<?= htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline btn-sm">Edit</button>
                                <button type="button" onclick="confirmDelete('<?= base_url('admin/courses/' . $course['id'] . '/delete') ?>', '<?= e($course['title']) ?>')" class="btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #6B7280;">
                            No courses found. Click "+ Add New Course" to get started.
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
                    <a href="<?= base_url('admin/courses?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">&larr; Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?= base_url('admin/courses?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Course Modal -->
<div id="create-course-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Add New Course</h3>
            <button type="button" onclick="closeModal('create-course-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="<?= base_url('admin/courses') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Course Title <span style="color:red;">*</span></label>
                <input type="text" name="title" class="form-input" placeholder="e.g. Full Stack Web Development" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Description <span style="color:red;">*</span></label>
                <textarea name="description" class="form-input" rows="4" placeholder="Course syllabus, outcomes, prerequisites..." required></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Duration <span style="color:red;">*</span></label>
                    <input type="text" name="duration" class="form-input" placeholder="e.g. 3 Months / 12 Weeks" required>
                </div>
                <div>
                    <label class="form-label">Delivery Mode</label>
                    <select name="mode" class="form-input">
                        <option value="Classroom">Classroom</option>
                        <option value="Online">Online</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Course Fee (₹)</label>
                    <input type="text" name="price" class="form-input" placeholder="e.g. 25000">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Course Cover Image</label>
                <input type="file" name="image" accept="image/*" class="form-input" data-preview="create-course-preview">
                <img id="create-course-preview" src="" alt="Preview" style="display:none; width: 100%; height: 120px; object-fit: cover; margin-top: 0.5rem; border-radius: 4px;">
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('create-course-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Save Course</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Course Modal -->
<div id="edit-course-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Edit Course</h3>
            <button type="button" onclick="closeModal('edit-course-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="edit-course-form" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Course Title <span style="color:red;">*</span></label>
                <input type="text" id="edit-course-title" name="title" class="form-input" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Description <span style="color:red;">*</span></label>
                <textarea id="edit-course-description" name="description" class="form-input" rows="4" required></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Duration <span style="color:red;">*</span></label>
                    <input type="text" id="edit-course-duration" name="duration" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Delivery Mode</label>
                    <select id="edit-course-mode" name="mode" class="form-input">
                        <option value="Classroom">Classroom</option>
                        <option value="Online">Online</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Course Fee (₹)</label>
                    <input type="text" id="edit-course-price" name="price" class="form-input">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select id="edit-course-status" name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Replace Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="form-input" data-preview="edit-course-preview">
                <img id="edit-course-preview" src="" alt="Course Preview" style="width: 100%; height: 120px; object-fit: cover; margin-top: 0.5rem; border-radius: 4px;">
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('edit-course-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Update Course</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCourse(course) {
    const form = document.getElementById('edit-course-form');
    form.action = '<?= base_url("admin/courses/") ?>' + course.id;

    document.getElementById('edit-course-title').value = course.title || '';
    document.getElementById('edit-course-description').value = course.description || '';
    document.getElementById('edit-course-duration').value = course.duration || '';
    document.getElementById('edit-course-mode').value = course.mode || 'Classroom';
    document.getElementById('edit-course-price').value = course.price || '';
    document.getElementById('edit-course-status').value = course.status || 'active';

    const prev = document.getElementById('edit-course-preview');
    if (course.image_url) {
        prev.src = '<?= base_url() ?>' + '/' + course.image_url.replace(/^\//, '');
        prev.style.display = 'block';
    } else {
        prev.style.display = 'none';
    }

    openModal('edit-course-modal');
}
</script>
