<?php
/** @var array $testimonials */
$testimonialList = $testimonials ?? [];
$limit = $limit ?? null;
if ($limit !== null) {
    $testimonialList = array_slice($testimonialList, 0, $limit);
}
?>
<section class="section-navy" id="testimonials">
    <div class="container-main">
        <div style="margin-bottom: 3rem;">
            <span class="section-label" style="color: var(--color-gold);">Student Feedback</span>
            <h2 class="section-title-white">What Our Learners Say</h2>
            <div class="section-divider" style="background-color: var(--color-gold);"></div>
            <p style="color: #D1D5DB; max-width: 600px;">
                Direct reviews from professionals and students who advanced their careers through our training programs.
            </p>
        </div>

        <?php if (!empty($testimonialList)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($testimonialList as $t): ?>
                    <article style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 6px; padding: 1.75rem; display: flex; flex-direction: column;">
                        <!-- Star Rating -->
                        <div class="stars" style="margin-bottom: 1rem;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span style="font-size: 1.1rem; color: <?= $i <= (int)$t['rating'] ? 'var(--color-gold)' : 'rgba(255,255,255,0.2)' ?>;">★</span>
                            <?php endfor; ?>
                        </div>

                        <!-- Review Text -->
                        <p style="font-size: 0.9375rem; color: #E5E7EB; line-height: 1.75; font-style: italic; margin-bottom: 1.5rem; flex: 1;">
                            &ldquo;<?= e($t['testimonial']) ?>&rdquo;
                        </p>

                        <!-- Student Meta -->
                        <div class="flex items-center gap-3" style="padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                            <?php if (!empty($t['student_image'])): ?>
                                <img src="<?= upload_url($t['student_image']) ?>" alt="<?= e($t['student_name']) ?>" style="width: 2.75rem; height: 2.75rem; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 2.75rem; height: 2.75rem; border-radius: 50%; background-color: var(--color-gold); color: #fff; font-weight: bold; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    <?= e(get_initials($t['student_name'])) ?>
                                </div>
                            <?php endif; ?>

                            <div>
                                <h4 style="font-size: 0.9375rem; font-weight: 700; color: #fff; margin-bottom: 0.15rem;">
                                    <?= e($t['student_name']) ?>
                                </h4>
                                <span style="font-size: 0.75rem; color: #9CA3AF;">
                                    <?= e($t['course']) ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: rgba(255,255,255,0.05); border-radius: 6px;">
                <p style="color: #9CA3AF; margin: 0;">Testimonials will appear shortly.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
