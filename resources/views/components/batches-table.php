<?php
/** @var array $upcomingBatches */
$batches = $upcomingBatches ?? [];
?>
<section class="section" id="batches" style="background-color: #fff;">
    <div class="container-main">
        <div style="margin-bottom: 2.5rem;">
            <span class="section-label">Enrol Today</span>
            <h2 class="section-title">Upcoming Course Batches</h2>
            <div class="section-divider"></div>
            <p style="color: #4B5563; max-width: 600px;">
                Secure your seat in our next cohorts. All batches maintain small sizes to guarantee individual mentorship.
            </p>
        </div>

        <?php if (!empty($batches)): ?>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Batch Name</th>
                            <th>Start Date</th>
                            <th>Timing</th>
                            <th>Duration</th>
                            <th>Location / Mode</th>
                            <th>Availability</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($batches as $b): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--color-navy); font-size: 0.9375rem;">
                                        <?= e($b['course']['title'] ?? $b['course_title'] ?? 'Course') ?>
                                    </strong>
                                </td>
                                <td>
                                    <span style="font-weight: 500; color: #374151;"><?= e($b['batch_name']) ?></span>
                                </td>
                                <td>
                                    <span style="color: #4B5563;">📅 <?= format_date($b['start_date']) ?></span>
                                </td>
                                <td>
                                    <span style="color: #4B5563;">⏰ <?= format_time($b['start_time']) ?></span>
                                </td>
                                <td>
                                    <span class="badge-navy"><?= e($b['duration']) ?></span>
                                </td>
                                <td>
                                    <span style="color: #4B5563;">📍 <?= e($b['location']) ?></span>
                                </td>
                                <td>
                                    <?php if ($b['available_seats'] > 5): ?>
                                        <span class="badge-green"><?= e($b['available_seats']) ?> seats left</span>
                                    <?php elseif ($b['available_seats'] > 0): ?>
                                        <span class="badge-gold"><?= e($b['available_seats']) ?> seats left (Fast Filling)</span>
                                    <?php else: ?>
                                        <span class="badge-red">Batch Full</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('contact?batch=' . urlencode($b['batch_name'])) ?>" class="btn-secondary btn-sm" style="white-space: nowrap;">
                                        Reserve Seat
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: var(--color-cream); border-radius: 6px;">
                <p style="color: #6B7280; margin: 0;">No scheduled batches at the moment. Please contact us for custom schedules.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
