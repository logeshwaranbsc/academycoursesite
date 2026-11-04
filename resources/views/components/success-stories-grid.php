<?php
/** @var array $successStories */
$stories = $successStories ?? [];
$limit = $limit ?? null;
if ($limit !== null) {
    $stories = array_slice($stories, 0, $limit);
}
?>
<section class="section-alt" id="success-stories">
    <div class="container-main">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6" style="margin-bottom: 3rem;">
            <div>
                <span class="section-label">Real Transformations</span>
                <h2 class="section-title">Student Success Stories</h2>
                <div class="section-divider"></div>
                <p style="max-width: 600px; color: #4B5563;">
                    From non-technical backgrounds and career gaps to engineering leads at high-growth tech firms.
                </p>
            </div>
            <?php if (!empty($showAllLink)): ?>
                <a href="<?= base_url('success-stories') ?>" class="btn-outline btn-sm flex-shrink-0">
                    View All Stories &rarr;
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($stories)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($stories as $story): ?>
                    <article class="card flex flex-col" style="padding: 0; overflow: hidden;">
                        <!-- Story Image / Thumbnail -->
                        <div style="position: relative; height: 14rem; background-color: var(--color-navy); overflow: hidden;">
                            <?php if (!empty($story['image_url'])): ?>
                                <img src="<?= upload_url($story['image_url']) ?>" alt="<?= e($story['student_name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2.5rem; font-weight: bold;">
                                    <?= e(get_initials($story['student_name'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($story['video_url'])): ?>
                                <a href="<?= e($story['video_url']) ?>" target="_blank" rel="noopener" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); color: #fff; font-size: 2.5rem; text-decoration: none;">
                                    ▶️
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Story Content -->
                        <div class="flex-1 flex flex-col" style="padding: 1.5rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--color-gold); margin-bottom: 0.5rem; letter-spacing: 0.05em;">
                                <?= e($story['student_name']) ?>
                            </span>
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--color-navy); margin-bottom: 0.75rem;">
                                <?= e($story['title']) ?>
                            </h3>
                            <p style="font-size: 0.875rem; color: #4B5563; line-height: 1.7; flex: 1;">
                                <?= e($story['description']) ?>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: #fff; border-radius: 6px;">
                <p style="color: #6B7280; margin: 0;">Success stories will be shared soon.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
