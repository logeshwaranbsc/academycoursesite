<?php
use App\Core\View;
?>
<!-- Page Hero Banner -->
<div style="background-color: var(--color-navy); color: #fff; padding: 4.5rem 0 3.5rem;">
    <div class="container-main">
        <span class="section-label" style="color: var(--color-gold);">About Meridian Academy</span>
        <h1 style="color: #fff; margin-bottom: 1rem;">Empowering Careers Through Practical Excellence</h1>
        <div class="section-divider" style="background-color: var(--color-gold);"></div>
        <p style="color: #D1D5DB; font-size: 1.125rem; max-width: 750px; line-height: 1.8;">
            Meridian Academy was founded with a singular mission: to bridge the gap between academic theory and real enterprise standards by equipping ambitious learners with battle-tested technical skills and direct employer pathways.
        </p>
    </div>
</div>

<!-- Detailed Narrative -->
<section class="section" style="background-color: #fff;">
    <div class="container-main">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 style="font-size: 2rem; color: var(--color-navy); margin-bottom: 1.25rem;">
                    Our Philosophy: Learn by Building
                </h2>
                <p style="font-size: 1rem; color: #4B5563; line-height: 1.8; margin-bottom: 1.25rem;">
                    Traditional academic courses often focus on obsolete syllabi and rote memorization. At Meridian Academy, every curriculum module is co-designed with engineering leaders from modern software companies.
                </p>
                <p style="font-size: 1rem; color: #4B5563; line-height: 1.8; margin-bottom: 1.5rem;">
                    Students participate in daily standups, code reviews, git workflows, architectural walkthroughs, and mock technical screenings. When you graduate, you don’t merely hold a certificate — you possess an authentic track record of engineering accomplishment.
                </p>

                <div class="flex items-center gap-4 flex-wrap">
                    <a href="<?= base_url('courses') ?>" class="btn-primary">
                        Browse Our Programs
                    </a>
                    <a href="<?= base_url('placements') ?>" class="btn-outline">
                        View Placed Alumni
                    </a>
                </div>
            </div>

            <div style="background: var(--color-cream); padding: 2.5rem; border-radius: 8px; border: 1px solid var(--color-gray-200);">
                <h3 style="font-size: 1.25rem; color: var(--color-navy); margin-bottom: 1.25rem;">
                    Academy Milestones
                </h3>
                <div class="flex flex-col gap-5">
                    <div class="flex items-start gap-4">
                        <span style="font-size: 1.5rem;">🎯</span>
                        <div>
                            <strong style="color: var(--color-navy); font-size: 1rem;">100% Practical Orientation</strong>
                            <p style="font-size: 0.875rem; color: #6B7280; margin: 0.25rem 0 0;">Zero redundant theory. More than 80% of student hours are spent writing and refining real code.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span style="font-size: 1.5rem;">🤝</span>
                        <div>
                            <strong style="color: var(--color-navy); font-size: 1rem;">Dedicated Career Mentorship</strong>
                            <p style="font-size: 0.875rem; color: #6B7280; margin: 0.25rem 0 0;">Personal coaching from industry professionals helping you craft standout portfolios and ace interviews.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span style="font-size: 1.5rem;">🚀</span>
                        <div>
                            <strong style="color: var(--color-navy); font-size: 1rem;">Direct Corporate Placement Network</strong>
                            <p style="font-size: 0.875rem; color: #6B7280; margin: 0.25rem 0 0;">Over 120+ hiring partner companies regularly recruit fresh talent directly from our graduating batches.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Highlights Pillars -->
<?= View::component('highlights') ?>

<!-- Why Choose Us -->
<?= View::component('why-choose-us') ?>

<!-- Enquiry Section -->
<?= View::component('enquiry-form', ['courses' => $courses ?? []]) ?>
