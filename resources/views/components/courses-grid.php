<?php
/** @var array $courses */
$courseList = $courses ?? [];
$limit = $limit ?? null;
if ($limit !== null) {
    $courseList = array_slice($courseList, 0, $limit);
}
?>
<section class="section" id="courses">
    <div class="container-main">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6" style="margin-bottom: 3rem;">
            <div>
                <span class="section-label">Curated Curriculum</span>
                <h2 class="section-title">Industry-Aligned Professional Courses</h2>
                <div class="section-divider"></div>
                <p style="max-width: 600px; color: #4B5563;">
                    Master real-world skills through comprehensive programs structured in direct partnership with leading technology firms.
                </p>
            </div>
            <?php if (!empty($showAllLink)): ?>
                <a href="<?= base_url('courses') ?>" class="btn-outline btn-sm flex-shrink-0">
                    View All Courses &rarr;
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($courseList)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($courseList as $course): ?>
                    <article class="card flex flex-col" style="padding: 0; overflow: hidden;">
                        <!-- Course Image Container -->
                        <div style="position: relative; height: 13rem; background-color: var(--color-navy); overflow: hidden;">
                            <?php if (!empty($course['image_url'])): ?>
                                <img src="<?= upload_url($course['image_url']) ?>" alt="<?= e($course['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff; padding: 1.5rem; text-align: center;">
                                    <span style="font-size: 2.5rem; margin-bottom: 0.5rem;">💻</span>
                                    <span style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-gold);">
                                        <?= e($course['mode']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- Fee Badge -->
                            <div style="position: absolute; top: 0.75rem; right: 0.75rem; background-color: var(--color-gold); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.65rem; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                <?= format_price($course['price']) ?>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="flex-1 flex flex-col" style="padding: 1.5rem;">
                            <span class="badge-gold" style="align-self: flex-start; margin-bottom: 0.75rem; font-size: 0.6875rem;">
                                <?= e($course['mode']) ?> Program
                            </span>
                            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--color-navy);">
                                <?= e($course['title']) ?>
                            </h3>
                            <p style="font-size: 0.875rem; color: #4B5563; line-height: 1.7; margin-bottom: 1.25rem; flex: 1;">
                                <?= e(mb_strimwidth(strip_tags($course['description']), 0, 120, '...')) ?>
                            </p>

                            <!-- Meta Details -->
                            <div class="flex items-center justify-between" style="padding-top: 1rem; border-top: 1px solid var(--color-gray-200); font-size: 0.8125rem; color: #6B7280; margin-bottom: 1.25rem;">
                                <span>⏱ Duration: <strong><?= e($course['duration']) ?></strong></span>
                                <span>🎓 Placement Assistance</span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-3">
                                <a href="<?= base_url('courses/' . $course['id']) ?>" class="btn-primary btn-sm flex-1 text-center" style="justify-content: center;">
                                    View Syllabus
                                </a>
                                <a href="<?= base_url('contact?course=' . urlencode($course['title'])) ?>" class="btn-outline-gold btn-sm text-center" style="justify-content: center;">
                                    Enquire
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: var(--color-cream); border-radius: 6px;">
                <p style="color: #6B7280; margin: 0;">No active courses available at this time. Please check back shortly.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
