<?php
/** @var array $placements */
$placementList = $placements ?? [];
$limit = $limit ?? null;
if ($limit !== null) {
    $placementList = array_slice($placementList, 0, $limit);
}
?>
<section class="section-alt" id="placements">
    <div class="container-main">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6" style="margin-bottom: 3rem;">
            <div>
                <span class="section-label">Proven Track Record</span>
                <h2 class="section-title">Our Students, Placed With Confidence</h2>
                <div class="section-divider"></div>
                <p style="max-width: 600px; color: #4B5563;">
                    Our alumni build outstanding careers at leading technology companies, startups, and multinational corporations worldwide.
                </p>
            </div>
            <?php if (!empty($showAllLink)): ?>
                <a href="<?= base_url('placements') ?>" class="btn-outline btn-sm flex-shrink-0">
                    View All Placements &rarr;
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($placementList)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <?php foreach ($placementList as $p): ?>
                    <article class="card flex items-start gap-4" style="padding: 1.25rem;">
                        <!-- Student Photo / Avatar Initials -->
                        <div class="flex-shrink-0">
                            <?php if (!empty($p['image_url'])): ?>
                                <img src="<?= upload_url($p['image_url']) ?>" alt="<?= e($p['student_name']) ?>" style="width: 3.5rem; height: 3.5rem; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <div style="width: 3.5rem; height: 3.5rem; background-color: var(--color-navy); color: #fff; font-weight: bold; font-size: 1.125rem; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                    <?= e(get_initials($p['student_name'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Placement Details -->
                        <div class="flex-1 min-w-0">
                            <h4 style="font-size: 0.9375rem; font-weight: 700; margin-bottom: 0.2rem; color: var(--color-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= e($p['student_name']) ?>
                            </h4>
                            <p style="font-size: 0.75rem; color: #6B7280; margin-bottom: 0.4rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= e($p['course']) ?>
                            </p>
                            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--color-gold-dark); display: flex; align-items: center; gap: 0.25rem;">
                                <span>🏢</span> <?= e($p['company']) ?>
                            </div>
                            <p style="font-size: 0.75rem; color: #4B5563; margin-top: 0.15rem;">
                                <?= e($p['designation']) ?>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: #fff; border-radius: 6px;">
                <p style="color: #6B7280; margin: 0;">Placement records will be updated shortly.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
