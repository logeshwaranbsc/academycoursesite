<?php
use App\Core\View;
?>
<!-- Page Hero Banner -->
<div style="background-color: var(--color-navy); color: #fff; padding: 4.5rem 0 3.5rem;">
    <div class="container-main">
        <span class="section-label" style="color: var(--color-gold);">Program Catalog</span>
        <h1 style="color: #fff; margin-bottom: 1rem;">Industry-Aligned Professional Courses</h1>
        <div class="section-divider" style="background-color: var(--color-gold);"></div>
        <p style="color: #D1D5DB; font-size: 1.125rem; max-width: 750px; line-height: 1.8;">
            Choose from comprehensive training programs designed with real-world technologies, personal mentorship, hands-on capstone projects, and dedicated placement support.
        </p>
    </div>
</div>

<!-- Full Course Catalog Grid -->
<?= View::component('courses-grid', [
    'courses' => $courses,
    'showAllLink' => false,
]) ?>

<!-- Why Choose Us -->
<?= View::component('why-choose-us') ?>

<!-- Enquiry Section -->
<?= View::component('enquiry-form', ['courses' => $courses]) ?>
