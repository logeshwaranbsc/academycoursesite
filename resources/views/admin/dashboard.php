<?php
/** @var array $stats */
?>
<div class="space-y-8">
    <!-- Top Stats Overview Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" style="margin-bottom: 2rem;">
        <!-- Card 1: Courses -->
        <div class="card" style="padding: 1.5rem;">
            <div class="flex items-center justify-between" style="margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: #6B7280; text-transform: uppercase;">Courses</span>
                <span style="font-size: 1.5rem;">📚</span>
            </div>
            <div style="font-size: 1.875rem; font-weight: 700; color: var(--color-navy); line-height: 1;">
                <?= (int)($stats['total_courses'] ?? 0) ?>
            </div>
            <span style="font-size: 0.75rem; color: #166534; font-weight: 600; margin-top: 0.5rem; display: block;">
                <?= (int)($stats['active_courses'] ?? 0) ?> Active Courses
            </span>
        </div>

        <!-- Card 2: Placements -->
        <div class="card" style="padding: 1.5rem;">
            <div class="flex items-center justify-between" style="margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: #6B7280; text-transform: uppercase;">Placements</span>
                <span style="font-size: 1.5rem;">🎓</span>
            </div>
            <div style="font-size: 1.875rem; font-weight: 700; color: var(--color-navy); line-height: 1;">
                <?= (int)($stats['total_placements'] ?? 0) ?>
            </div>
            <span style="font-size: 0.75rem; color: #6B7280; margin-top: 0.5rem; display: block;">
                Placed Students Records
            </span>
        </div>

        <!-- Card 3: Enquiries -->
        <div class="card" style="padding: 1.5rem;">
            <div class="flex items-center justify-between" style="margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: #6B7280; text-transform: uppercase;">Total Enquiries</span>
                <span style="font-size: 1.5rem;">✉️</span>
            </div>
            <div style="font-size: 1.875rem; font-weight: 700; color: var(--color-navy); line-height: 1;">
                <?= (int)($stats['total_enquiries'] ?? 0) ?>
            </div>
            <span style="font-size: 0.75rem; color: #dc2626; font-weight: 700; margin-top: 0.5rem; display: block;">
                <?= (int)($stats['new_enquiries'] ?? 0) ?> New / Uncontacted
            </span>
        </div>

        <!-- Card 4: Upcoming Batches -->
        <div class="card" style="padding: 1.5rem;">
            <div class="flex items-center justify-between" style="margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: #6B7280; text-transform: uppercase;">Upcoming Batches</span>
                <span style="font-size: 1.5rem;">📅</span>
            </div>
            <div style="font-size: 1.875rem; font-weight: 700; color: var(--color-gold); line-height: 1;">
                <?= count($stats['upcoming_batches'] ?? []) ?>
            </div>
            <span style="font-size: 0.75rem; color: #6B7280; margin-top: 0.5rem; display: block;">
                Scheduled Future Cohorts
            </span>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="card" style="padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1rem; color: var(--color-navy); margin-bottom: 1rem;">Quick Actions</h3>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="<?= base_url('admin/courses') ?>" class="btn-primary btn-sm">+ Manage Courses</a>
            <a href="<?= base_url('admin/batches') ?>" class="btn-secondary btn-sm">+ Add New Batch</a>
            <a href="<?= base_url('admin/placements') ?>" class="btn-outline btn-sm">+ Add Placement</a>
            <a href="<?= base_url('admin/banners') ?>" class="btn-outline btn-sm">Manage Hero Slider</a>
            <a href="<?= base_url('admin/settings') ?>" class="btn-outline btn-sm">Website Settings</a>
        </div>
    </div>

    <!-- 2 Column Tables: Recent Enquiries & Upcoming Batches -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Enquiries -->
        <div class="card" style="padding: 1.5rem;">
            <div class="flex items-center justify-between" style="margin-bottom: 1.25rem;">
                <h3 style="font-size: 1.125rem; color: var(--color-navy); margin: 0;">Recent Form Submissions</h3>
                <a href="<?= base_url('admin/enquiries') ?>" style="font-size: 0.8125rem; color: var(--color-gold-dark); font-weight: 600;">
                    View All &rarr;
                </a>
            </div>

            <?php if (!empty($stats['recent_enquiries'])): ?>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Program</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['recent_enquiries'] as $enq): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($enq['name']) ?></strong>
                                    </td>
                                    <td>
                                        <a href="tel:<?= e($enq['phone']) ?>" style="color: var(--color-navy);"><?= e($enq['phone']) ?></a>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.8125rem;"><?= e($enq['course'] ?? 'General') ?></span>
                                    </td>
                                    <td>
                                        <?php if ($enq['status'] === 'new'): ?>
                                            <span class="badge-red">New</span>
                                        <?php elseif ($enq['status'] === 'contacted'): ?>
                                            <span class="badge-gold">Contacted</span>
                                        <?php else: ?>
                                            <span class="badge-green">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #6B7280; font-size: 0.875rem; margin: 0;">No enquiries submitted yet.</p>
            <?php endif; ?>
        </div>

        <!-- Upcoming Batches -->
        <div class="card" style="padding: 1.5rem;">
            <div class="flex items-center justify-between" style="margin-bottom: 1.25rem;">
                <h3 style="font-size: 1.125rem; color: var(--color-navy); margin: 0;">Upcoming Cohorts</h3>
                <a href="<?= base_url('admin/batches') ?>" style="font-size: 0.8125rem; color: var(--color-gold-dark); font-weight: 600;">
                    Manage Batches &rarr;
                </a>
            </div>

            <?php if (!empty($stats['upcoming_batches'])): ?>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Start Date</th>
                                <th>Seats</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['upcoming_batches'] as $b): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($b['course']['title'] ?? $b['course_title'] ?? 'Course') ?></strong>
                                    </td>
                                    <td><?= e($b['batch_name']) ?></td>
                                    <td><?= format_date($b['start_date']) ?></td>
                                    <td>
                                        <span class="badge-navy"><?= e($b['available_seats']) ?> left</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #6B7280; font-size: 0.875rem; margin: 0;">No upcoming batches scheduled.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
