<?php
/** @var array $pagination */
$items = $pagination['data'] ?? [];
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
?>
<div>
    <div class="flex items-center justify-between gap-4 flex-wrap" style="margin-bottom: 1.5rem;">
        <form method="GET" action="<?= base_url('admin/testimonials') ?>" class="flex items-center gap-2">
            <input type="text" name="search" placeholder="Search testimonials..." value="<?= e($search ?? '') ?>" class="form-input" style="width: 250px; padding: 0.4rem 0.75rem;">
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin/testimonials') ?>" class="btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <button type="button" onclick="openModal('create-testimonial-modal')" class="btn-secondary btn-sm">
            + Add Testimonial
        </button>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Photo</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Rating</th>
                    <th>Review Quote</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['student_image'])): ?>
                                    <img src="<?= upload_url($item['student_image']) ?>" alt="Student" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--color-navy); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.75rem;">
                                        <?= e(get_initials($item['student_name'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= e($item['student_name']) ?></strong></td>
                            <td><?= e($item['course']) ?></td>
                            <td>
                                <span style="color: var(--color-gold);">
                                    <?= str_repeat('★', (int)$item['rating']) ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.8125rem; color: #4B5563;"><?= e(mb_strimwidth($item['testimonial'], 0, 75, '...')) ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn-toggle-status" data-url="<?= base_url('admin/testimonials/' . $item['id'] . '/toggle') ?>" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <?php if ($item['status'] === 'active'): ?>
                                        <span class="status-badge badge-green">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-red">Inactive</span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" onclick="editTestimonial(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline btn-sm">Edit</button>
                                <button type="button" onclick="confirmDelete('<?= base_url('admin/testimonials/' . $item['id'] . '/delete') ?>', '<?= e($item['student_name']) ?>')" class="btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2.5rem; color: #6B7280;">
                            No testimonials found. Click "+ Add Testimonial" to create one.
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
                    <a href="<?= base_url('admin/testimonials?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">&larr; Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?= base_url('admin/testimonials?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div id="create-testimonial-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Add Student Review</h3>
            <button type="button" onclick="closeModal('create-testimonial-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="<?= base_url('admin/testimonials') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Student Name <span style="color:red;">*</span></label>
                    <input type="text" name="student_name" class="form-input" placeholder="e.g. Ananya Roy" required>
                </div>
                <div>
                    <label class="form-label">Course Taken <span style="color:red;">*</span></label>
                    <input type="text" name="course" class="form-input" placeholder="e.g. Data Analytics" required>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Review / Testimonial <span style="color:red;">*</span></label>
                <textarea name="testimonial" class="form-input" rows="3" placeholder="What did they appreciate about the mentors, projects, and placement support?" required></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Star Rating (1-5)</label>
                    <select name="rating" class="form-input">
                        <option value="5">★★★★★ (5 Stars)</option>
                        <option value="4">★★★★☆ (4 Stars)</option>
                        <option value="3">★★★☆☆ (3 Stars)</option>
                        <option value="2">★★☆☆☆ (2 Stars)</option>
                        <option value="1">★☆☆☆☆ (1 Star)</option>
                    </select>
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
                <label class="form-label">Student Photo</label>
                <input type="file" name="image" accept="image/*" class="form-input">
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('create-testimonial-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Save Review</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-testimonial-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Edit Review</h3>
            <button type="button" onclick="closeModal('edit-testimonial-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="edit-testimonial-form" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Student Name <span style="color:red;">*</span></label>
                    <input type="text" id="edit-test-name" name="student_name" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Course Taken <span style="color:red;">*</span></label>
                    <input type="text" id="edit-test-course" name="course" class="form-input" required>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Review / Testimonial <span style="color:red;">*</span></label>
                <textarea id="edit-test-quote" name="testimonial" class="form-input" rows="3" required></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Star Rating</label>
                    <select id="edit-test-rating" name="rating" class="form-input">
                        <option value="5">★★★★★ (5 Stars)</option>
                        <option value="4">★★★★☆ (4 Stars)</option>
                        <option value="3">★★★☆☆ (3 Stars)</option>
                        <option value="2">★★☆☆☆ (2 Stars)</option>
                        <option value="1">★☆☆☆☆ (1 Star)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select id="edit-test-status" name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Replace Student Photo</label>
                <input type="file" name="image" accept="image/*" class="form-input">
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('edit-testimonial-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Update Review</button>
            </div>
        </form>
    </div>
</div>

<script>
function editTestimonial(item) {
    const form = document.getElementById('edit-testimonial-form');
    form.action = '<?= base_url("admin/testimonials/") ?>' + item.id;

    document.getElementById('edit-test-name').value = item.student_name || '';
    document.getElementById('edit-test-course').value = item.course || '';
    document.getElementById('edit-test-quote').value = item.testimonial || '';
    document.getElementById('edit-test-rating').value = item.rating || 5;
    document.getElementById('edit-test-status').value = item.status || 'active';

    openModal('edit-testimonial-modal');
}
</script>
