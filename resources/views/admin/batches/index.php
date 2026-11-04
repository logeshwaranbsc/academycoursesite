<?php
/** @var array $pagination */
/** @var array $courses */
$items = $pagination['data'] ?? [];
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
?>
<div>
    <div class="flex items-center justify-between gap-4 flex-wrap" style="margin-bottom: 1.5rem;">
        <form method="GET" action="<?= base_url('admin/batches') ?>" class="flex items-center gap-2">
            <input type="text" name="search" placeholder="Search batches..." value="<?= e($search ?? '') ?>" class="form-input" style="width: 250px; padding: 0.4rem 0.75rem;">
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin/batches') ?>" class="btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <button type="button" onclick="openModal('create-batch-modal')" class="btn-secondary btn-sm">
            + Add New Batch
        </button>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Batch Name</th>
                    <th>Start Date & Time</th>
                    <th>Duration & Location</th>
                    <th>Seats Left</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $batch): ?>
                        <tr>
                            <td>
                                <strong><?= e($batch['course']['title'] ?? $batch['course_title'] ?? 'Course #' . $batch['course_id']) ?></strong>
                            </td>
                            <td><?= e($batch['batch_name']) ?></td>
                            <td>
                                <div>📅 <?= format_date($batch['start_date']) ?></div>
                                <div style="font-size: 0.75rem; color: #6B7280;">⏰ <?= format_time($batch['start_time']) ?></div>
                            </td>
                            <td>
                                <div><?= e($batch['duration']) ?></div>
                                <div style="font-size: 0.75rem; color: #6B7280;">📍 <?= e($batch['location']) ?></div>
                            </td>
                            <td>
                                <span class="badge-navy"><?= (int)$batch['available_seats'] ?> seats</span>
                            </td>
                            <td>
                                <button type="button" class="btn-toggle-status" data-url="<?= base_url('admin/batches/' . $batch['id'] . '/toggle') ?>" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    <?php if ($batch['status'] === 'active'): ?>
                                        <span class="status-badge badge-green">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-red">Inactive</span>
                                    <?php endif; ?>
                                </button>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" onclick="editBatch(<?= htmlspecialchars(json_encode($batch), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline btn-sm">Edit</button>
                                <button type="button" onclick="confirmDelete('<?= base_url('admin/batches/' . $batch['id'] . '/delete') ?>', '<?= e($batch['batch_name']) ?>')" class="btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2.5rem; color: #6B7280;">
                            No batches found. Click "+ Add New Batch" to schedule one.
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
                    <a href="<?= base_url('admin/batches?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">&larr; Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?= base_url('admin/batches?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="btn-outline btn-sm">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Batch Modal -->
<div id="create-batch-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Add New Cohort Batch</h3>
            <button type="button" onclick="closeModal('create-batch-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="<?= base_url('admin/batches') ?>" method="POST">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Course Program <span style="color:red;">*</span></label>
                <select name="course_id" class="form-input" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Batch Name <span style="color:red;">*</span></label>
                <input type="text" name="batch_name" class="form-input" placeholder="e.g. Batch 15 — Morning Cohort" required>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Start Date <span style="color:red;">*</span></label>
                    <input type="date" name="start_date" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Start Time <span style="color:red;">*</span></label>
                    <input type="time" name="start_time" class="form-input" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Duration <span style="color:red;">*</span></label>
                    <input type="text" name="duration" class="form-input" placeholder="e.g. 3 Months" required>
                </div>
                <div>
                    <label class="form-label">Location / Mode <span style="color:red;">*</span></label>
                    <input type="text" name="location" class="form-input" placeholder="e.g. Bangalore Campus / Online" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Available Seats</label>
                    <input type="number" name="available_seats" class="form-input" value="30" min="0">
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
                <button type="button" onclick="closeModal('create-batch-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Save Batch</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Batch Modal -->
<div id="edit-batch-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Edit Batch</h3>
            <button type="button" onclick="closeModal('edit-batch-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="edit-batch-form" method="POST">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Course Program <span style="color:red;">*</span></label>
                <select id="edit-batch-course" name="course_id" class="form-input" required>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Batch Name <span style="color:red;">*</span></label>
                <input type="text" id="edit-batch-name" name="batch_name" class="form-input" required>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Start Date <span style="color:red;">*</span></label>
                    <input type="date" id="edit-batch-date" name="start_date" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Start Time <span style="color:red;">*</span></label>
                    <input type="time" id="edit-batch-time" name="start_time" class="form-input" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Duration <span style="color:red;">*</span></label>
                    <input type="text" id="edit-batch-duration" name="duration" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Location / Mode <span style="color:red;">*</span></label>
                    <input type="text" id="edit-batch-location" name="location" class="form-input" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Available Seats</label>
                    <input type="number" id="edit-batch-seats" name="available_seats" class="form-input" min="0">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select id="edit-batch-status" name="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('edit-batch-modal')" class="btn-outline btn-sm">Cancel</button>
                <button type="submit" class="btn-primary btn-sm">Update Batch</button>
            </div>
        </form>
    </div>
</div>

<script>
function editBatch(batch) {
    const form = document.getElementById('edit-batch-form');
    form.action = '<?= base_url("admin/batches/") ?>' + batch.id;

    document.getElementById('edit-batch-course').value = batch.course_id;
    document.getElementById('edit-batch-name').value = batch.batch_name;
    document.getElementById('edit-batch-date').value = batch.start_date;
    document.getElementById('edit-batch-time').value = batch.start_time;
    document.getElementById('edit-batch-duration').value = batch.duration;
    document.getElementById('edit-batch-location').value = batch.location;
    document.getElementById('edit-batch-seats').value = batch.available_seats;
    document.getElementById('edit-batch-status').value = batch.status;

    openModal('edit-batch-modal');
}
</script>
