<?php
/** @var array $pagination */
$items = $pagination['data'] ?? [];
$currentPage = $pagination['current_page'] ?? 1;
$lastPage = $pagination['last_page'] ?? 1;
$selectedStatus = $selectedStatus ?? '';
?>
<div>
    <!-- Status Filter Badges & Search -->
    <div class="flex items-center justify-between gap-4 flex-wrap" style="margin-bottom: 1.5rem;">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="<?= base_url('admin/enquiries') ?>" class="btn-sm <?= empty($selectedStatus) ? 'btn-primary' : 'btn-outline' ?>">
                All (<?= $pagination['total'] ?? 0 ?>)
            </a>
            <a href="<?= base_url('admin/enquiries?status=new') ?>" class="btn-sm <?= $selectedStatus === 'new' ? 'btn-danger' : 'btn-outline' ?>" style="<?= $selectedStatus !== 'new' ? 'border-color: #fecaca; color: #dc2626;' : '' ?>">
                New (<?= (int)($newCount ?? 0) ?>)
            </a>
            <a href="<?= base_url('admin/enquiries?status=contacted') ?>" class="btn-sm <?= $selectedStatus === 'contacted' ? 'btn-secondary' : 'btn-outline' ?>">
                Contacted (<?= (int)($contactedCount ?? 0) ?>)
            </a>
            <a href="<?= base_url('admin/enquiries?status=closed') ?>" class="btn-sm <?= $selectedStatus === 'closed' ? 'btn-primary' : 'btn-outline' ?>">
                Closed (<?= (int)($closedCount ?? 0) ?>)
            </a>
        </div>

        <form method="GET" action="<?= base_url('admin/enquiries') ?>" class="flex items-center gap-2">
            <?php if (!empty($selectedStatus)): ?>
                <input type="hidden" name="status" value="<?= e($selectedStatus) ?>">
            <?php endif; ?>
            <input type="text" name="search" placeholder="Search by name, phone, email..." value="<?= e($search ?? '') ?>" class="form-input" style="width: 250px; padding: 0.4rem 0.75rem;">
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin/enquiries' . (!empty($selectedStatus) ? '?status=' . urlencode($selectedStatus) : '')) ?>" class="btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Data Table -->
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Prospect Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Interested Program</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $enq): ?>
                        <tr>
                            <td>
                                <div><?= format_date($enq['created_at']) ?></div>
                                <div style="font-size: 0.75rem; color: #6B7280;"><?= format_time($enq['created_at']) ?></div>
                            </td>
                            <td>
                                <strong><?= e($enq['name']) ?></strong>
                            </td>
                            <td>
                                <a href="tel:<?= e($enq['phone']) ?>" style="color: var(--color-navy); font-weight: 600;">
                                    📞 <?= e($enq['phone']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($enq['email'])): ?>
                                    <a href="mailto:<?= e($enq['email']) ?>" style="color: #4B5563;">
                                        <?= e($enq['email']) ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #9CA3AF; font-size: 0.75rem;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($enq['course'])): ?>
                                    <span class="badge-gold"><?= e($enq['course']) ?></span>
                                <?php else: ?>
                                    <span class="badge-navy">General</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="<?= base_url('admin/enquiries/' . $enq['id'] . '/status') ?>" method="POST" style="margin: 0;">
                                    <?= csrf_field() ?>
                                    <select name="status" onchange="this.form.submit()" style="font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.5rem; border-radius: 4px; border: 1px solid var(--color-gray-300); cursor: pointer; background-color: <?= $enq['status'] === 'new' ? '#fee2e2' : ($enq['status'] === 'contacted' ? '#fef3c7' : '#dcfce7') ?>; color: <?= $enq['status'] === 'new' ? '#991b1b' : ($enq['status'] === 'contacted' ? '#92400e' : '#166534') ?>;">
                                        <option value="new" <?= $enq['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                        <option value="contacted" <?= $enq['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                        <option value="closed" <?= $enq['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                    </select>
                                </form>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button type="button" onclick="viewEnquiry(<?= htmlspecialchars(json_encode($enq), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline btn-sm">
                                    View Message
                                </button>
                                <button type="button" onclick="confirmDelete('<?= base_url('admin/enquiries/' . $enq['id'] . '/delete') ?>', 'this enquiry from <?= e($enq['name']) ?>')" class="btn-danger btn-sm">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2.5rem; color: #6B7280;">
                            No enquiries found matching criteria.
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
                    <a href="<?= base_url('admin/enquiries?page=' . ($currentPage - 1) . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($selectedStatus) ? '&status=' . urlencode($selectedStatus) : '')) ?>" class="btn-outline btn-sm">&larr; Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $lastPage): ?>
                    <a href="<?= base_url('admin/enquiries?page=' . ($currentPage + 1) . (!empty($search) ? '&search=' . urlencode($search) : '') . (!empty($selectedStatus) ? '&status=' . urlencode($selectedStatus) : '')) ?>" class="btn-outline btn-sm">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- View Enquiry Details Modal -->
<div id="view-enquiry-modal" class="modal-backdrop">
    <div class="modal-box" style="padding: 2rem;">
        <div class="flex items-center justify-between" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--color-navy);">Enquiry Details</h3>
            <button type="button" onclick="closeModal('view-enquiry-modal')" style="background:none; border:none; font-size: 1.5rem; cursor:pointer;">&times;</button>
        </div>

        <div style="margin-bottom: 1rem;">
            <span style="font-size: 0.75rem; color: #6B7280; font-weight: 600; text-transform: uppercase;">Candidate Information</span>
            <div id="view-enq-name" style="font-size: 1.125rem; font-weight: 700; color: var(--color-navy); margin-top: 0.25rem;"></div>
            <div style="font-size: 0.875rem; color: #4B5563; margin-top: 0.25rem;">
                <span id="view-enq-phone"></span> &bull; <span id="view-enq-email"></span>
            </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
            <span style="font-size: 0.75rem; color: #6B7280; font-weight: 600; text-transform: uppercase;">Selected Course</span>
            <div id="view-enq-course" style="font-size: 0.9375rem; font-weight: 600; color: var(--color-gold-dark); margin-top: 0.25rem;"></div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <span style="font-size: 0.75rem; color: #6B7280; font-weight: 600; text-transform: uppercase;">Message / Questions</span>
            <div id="view-enq-msg" style="background: #f9fafb; padding: 1rem; border-radius: 4px; border: 1px solid var(--color-gray-200); font-size: 0.875rem; color: #374151; line-height: 1.6; margin-top: 0.25rem; white-space: pre-wrap;"></div>
        </div>

        <div class="flex items-center justify-end">
            <button type="button" onclick="closeModal('view-enquiry-modal')" class="btn-primary btn-sm">Close</button>
        </div>
    </div>
</div>

<script>
function viewEnquiry(enq) {
    document.getElementById('view-enq-name').textContent = enq.name || '';
    document.getElementById('view-enq-phone').textContent = enq.phone ? 'Phone: ' + enq.phone : '';
    document.getElementById('view-enq-email').textContent = enq.email ? 'Email: ' + enq.email : 'No email provided';
    document.getElementById('view-enq-course').textContent = enq.course || 'General Enquiry';
    document.getElementById('view-enq-msg').textContent = enq.message || 'No additional message submitted.';

    openModal('view-enquiry-modal');
}
</script>
