<?php
/** @var array $course */
/** @var array $batches */
use App\Core\View;
?>
<!-- Course Header Banner -->
<div style="background-color: var(--color-navy); color: #fff; padding: 4rem 0 3.5rem;">
    <div class="container-main">
        <a href="<?= base_url('courses') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--color-gold); font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem;" class="hover:underline">
            &larr; Back to All Courses
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            <div class="lg:col-span-2">
                <span class="badge-gold" style="margin-bottom: 1rem; display: inline-block;">
                    <?= e($course['mode']) ?> Program
                </span>
                <h1 style="color: #fff; margin-bottom: 1rem;">
                    <?= e($course['title']) ?>
                </h1>
                <p style="color: #D1D5DB; font-size: 1.125rem; line-height: 1.8; margin-bottom: 2rem;">
                    <?= nl2br(e($course['description'])) ?>
                </p>

                <!-- Program Highlights Tags -->
                <div class="flex items-center gap-6 flex-wrap" style="font-size: 0.875rem; color: #E5E7EB; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    <div class="flex items-center gap-2">
                        <span style="color: var(--color-gold);">⏱</span>
                        <span>Duration: <strong><?= e($course['duration']) ?></strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span style="color: var(--color-gold);">💻</span>
                        <span>Delivery: <strong><?= e($course['mode']) ?></strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span style="color: var(--color-gold);">🎓</span>
                        <span>Placement Assistance: <strong>Included</strong></span>
                    </div>
                </div>
            </div>

            <!-- Course Card & Fee Box -->
            <div class="card" style="padding: 1.75rem; border-radius: 8px;">
                <?php if (!empty($course['image_url'])): ?>
                    <img src="<?= upload_url($course['image_url']) ?>" alt="<?= e($course['title']) ?>" style="width: 100%; height: 12rem; object-fit: cover; border-radius: 6px; margin-bottom: 1.25rem;">
                <?php endif; ?>

                <div style="margin-bottom: 1.25rem;">
                    <span style="font-size: 0.75rem; color: #6B7280; font-weight: 600; text-transform: uppercase;">Course Fee</span>
                    <div style="font-family: var(--font-heading); font-size: 1.75rem; font-weight: 700; color: var(--color-navy); margin-top: 0.25rem;">
                        <?= format_price($course['price']) ?>
                    </div>
                </div>

                <a href="#course-enquiry" class="btn-secondary w-full" style="display: block; text-align: center; width: 100%; margin-bottom: 0.75rem;">
                    Enrol / Enquire Now
                </a>
                <p style="font-size: 0.75rem; color: #6B7280; text-align: center; margin: 0;">
                    Instalment plans and corporate scholarships available.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Batches for this Course -->
<section class="section" style="background-color: var(--color-cream);">
    <div class="container-main">
        <div style="margin-bottom: 2rem;">
            <span class="section-label">Enrollment Schedule</span>
            <h2 class="section-title">Upcoming Batches for <?= e($course['title']) ?></h2>
            <div class="section-divider"></div>
        </div>

        <?php if (!empty($batches)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($batches as $b): ?>
                    <div class="card" style="padding: 1.5rem; background: #fff;">
                        <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--color-navy); margin-bottom: 0.75rem;">
                            <?= e($b['batch_name']) ?>
                        </h4>
                        <div class="flex flex-col gap-2" style="font-size: 0.875rem; color: #4B5563; margin-bottom: 1.25rem;">
                            <div>📅 Starts: <strong><?= format_date($b['start_date']) ?></strong> at <?= format_time($b['start_time']) ?></div>
                            <div>⏱ Duration: <?= e($b['duration']) ?></div>
                            <div>📍 Location: <?= e($b['location']) ?></div>
                        </div>

                        <div class="flex items-center justify-between" style="padding-top: 1rem; border-top: 1px solid var(--color-gray-200);">
                            <?php if ($b['available_seats'] > 5): ?>
                                <span class="badge-green"><?= e($b['available_seats']) ?> seats</span>
                            <?php else: ?>
                                <span class="badge-gold"><?= e($b['available_seats']) ?> seats left</span>
                            <?php endif; ?>

                            <a href="#course-enquiry" class="btn-primary btn-sm">
                                Reserve Seat
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 2.5rem; background: #fff; border-radius: 6px;">
                <p style="color: #6B7280; margin-bottom: 1rem;">No public scheduled batches right now for this program.</p>
                <a href="#course-enquiry" class="btn-secondary btn-sm">Request Batch Schedule</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Course Enquiry Form -->
<div id="course-enquiry">
    <?= View::component('enquiry-form', [
        'courses' => [$course['title']],
        'defaultCourse' => $course['title'],
    ]) ?>
</div>
