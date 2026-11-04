<?php
/** @var array $pagination */
$items = $pagination['data'] ?? [];
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
?>
<div>
    <div class="flex items-center justify-between gap-4 flex-wrap" style="margin-bottom: 1.5rem;">
        <form method="GET" action="<?= base_url('admin/placements') ?>" class="flex items-center gap-2">
            <input type="text" name="search" placeholder="Search student or company..." value="<?= e($search ?? '') ?>" class="form-input" style="width: 250px; padding: 0.4rem 0.75rem;">
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin/placements') ?>" class="btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <button type="button" onclick="openModal('create-placement-modal')" class="btn-secondary btn-sm">
            + Add Placed Student
        </button>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Photo</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Placed Company</th>
                    <th>Designation</th>
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
                                    <img src="<?= upload_url($item['image_url']) ?>" alt="Student" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 45px; height: 45px; background: var(--color-navy); color:#fff; display:flex; align-items:center; justify-content:center; border-radius:4px; font-weight:bold;">
                                        <?= e(get_initials($item['student_name'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= e($item['student_name']) ?></strong></td>
                            <td><?= e($item['course']) ?></td>
                            <td><span class="badge-gold">🏢 <?= e($item['company']) ?></span></td>
                            <td><?= e($item['designation']) ?></td>
                            <td>
                                <button type="button" class="btn-toggle-status" data-url="<?= base_url('admin/placements/' . $item['id'] . '/toggle') ?>" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <?php if ($item['status'] === 'active'): ?>
                                        <span class="status-badge badge-green">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-red">Inactive</span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" onclick="editPlacement(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline btn-sm">Edit</button>
                                <button type="button" onclick="confirmDelete('<?= base_url('admin/placements/' . $item['id'] . '/delete') ?>', '<?= e($item['student_name']) ?>')" class="btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2.5rem; color: #6B7280;">
                            No placed students records found. Click "+ Add Placed Student" to add one.
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
                    <a href="<?= base_url('admin/placements?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">&larr; Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?= base_url('admin/placements?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div id="create-placement-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Add Placed Student</h3>
            <button type="button" onclick="closeModal('create-placement-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="<?= base_url('admin/placements') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Student Name <span style="color:red;">*</span></label>
                    <input type="text" name="student_name" class="form-input" placeholder="e.g. Priya Sharma" required>
                </div>
                <div>
                    <label class="form-label">Course Taken <span style="color:red;">*</span></label>
                    <input type="text" name="course" class="form-input" placeholder="e.g. Full Stack Development" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Hiring Company <span style="color:red;">*</span></label>
                    <input type="text" name="company" class="form-input" placeholder="e.g. Amazon / Infosys" required>
                </div>
                <div>
                    <label class="form-label">Designation <span style="color:red;">*</span></label>
                    <input type="text" name="designation" class="form-input" placeholder="e.g. Frontend Engineer" required>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Student Photo</label>
                <input type="file" name="image" accept="image/*" class="form-input" data-preview="create-placement-preview">
                <img id="create-placement-preview" src="" alt="Preview" style="display:none; width: 80px; height: 80px; object-fit: cover; margin-top: 0.5rem; border-radius: 4px;">
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
                <button type="button" onclick="closeModal('create-placement-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Save Placement</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-placement-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Edit Placed Student</h3>
            <button type="button" onclick="closeModal('edit-placement-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="edit-placement-form" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Student Name <span style="color:red;">*</span></label>
                    <input type="text" id="edit-placement-name" name="student_name" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Course Taken <span style="color:red;">*</span></label>
                    <input type="text" id="edit-placement-course" name="course" class="form-input" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Hiring Company <span style="color:red;">*</span></label>
                    <input type="text" id="edit-placement-company" name="company" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Designation <span style="color:red;">*</span></label>
                    <input type="text" id="edit-placement-designation" name="designation" class="form-input" required>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Replace Student Photo</label>
                <input type="file" name="image" accept="image/*" class="form-input" data-preview="edit-placement-preview">
                <img id="edit-placement-preview" src="" alt="Photo" style="width: 80px; height: 80px; object-fit: cover; margin-top: 0.5rem; border-radius: 4px;">
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Display Order</label>
                    <input type="number" id="edit-placement-order" name="display_order" class="form-input">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select id="edit-placement-status" name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('edit-placement-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Update Record</button>
            </div>
        </form>
    </div>
</div>

<script>
function editPlacement(item) {
    const form = document.getElementById('edit-placement-form');
    form.action = '<?= base_url("admin/placements/") ?>' + item.id;

    document.getElementById('edit-placement-name').value = item.student_name || '';
    document.getElementById('edit-placement-course').value = item.course || '';
    document.getElementById('edit-placement-company').value = item.company || '';
    document.getElementById('edit-placement-designation').value = item.designation || '';
    document.getElementById('edit-placement-order').value = item.display_order || 0;
    document.getElementById('edit-placement-status').value = item.status || 'active';

    const prev = document.getElementById('edit-placement-preview');
    if (item.image_url) {
        prev.src = '<?= base_url() ?>' + '/' + item.image_url.replace(/^\//, '');
        prev.style.display = 'block';
    } else {
        prev.style.display = 'none';
    }

    openModal('edit-placement-modal');
}
</script>
