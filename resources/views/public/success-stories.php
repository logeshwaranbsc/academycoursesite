<?php
use App\Core\View;
?>
<!-- Page Hero Banner -->
<div style="background-color: var(--color-navy); color: #fff; padding: 4.5rem 0 3.5rem;">
    <div class="container-main">
        <span class="section-label" style="color: var(--color-gold);">Inspiring Journeys</span>
        <h1 style="color: #fff; margin-bottom: 1rem;">Real Student Transformations & Success Stories</h1>
        <div class="section-divider" style="background-color: var(--color-gold);"></div>
        <p style="color: #D1D5DB; font-size: 1.125rem; max-width: 750px; line-height: 1.8;">
            Read inspiring stories and reviews from Meridian Academy alumni who transformed their careers and achieved life-changing goals through hard work and focused mentorship.
        </p>
    </div>
</div>

<!-- Success Stories Grid -->
<?= View::component('success-stories-grid', [
    'successStories' => $successStories,
    'showAllLink' => false,
]) ?>

<!-- Testimonials Grid -->
<?= View::component('testimonials-grid', [
    'testimonials' => $testimonials,
]) ?>

<!-- Enquiry Section -->
<?= View::component('enquiry-form', ['courses' => $courses]) ?>
