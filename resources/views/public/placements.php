<?php
use App\Core\View;
?>
<!-- Page Hero Banner -->
<div style="background-color: var(--color-navy); color: #fff; padding: 4.5rem 0 3.5rem;">
    <div class="container-main">
        <span class="section-label" style="color: var(--color-gold);">Proven Track Record</span>
        <h1 style="color: #fff; margin-bottom: 1rem;">Student Placements & Hiring Partners</h1>
        <div class="section-divider" style="background-color: var(--color-gold);"></div>
        <p style="color: #D1D5DB; font-size: 1.125rem; max-width: 750px; line-height: 1.8;">
            Over 94% placement rate across top technology startups, Fortune 500 corporations, and digital agencies. See where Digital Teenz Academy graduates build rewarding careers.
        </p>
    </div>
</div>

<!-- Placements Grid -->
<?= View::component('placements-grid', [
    'placements' => $placements,
    'showAllLink' => false,
]) ?>

<!-- Why Choose Us -->
<?= View::component('why-choose-us') ?>

<!-- Enquiry Section -->
<?= View::component('enquiry-form', ['courses' => $courses]) ?>
