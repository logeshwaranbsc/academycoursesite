<?php
use App\Core\View;
?>
<!-- Page Hero Banner -->
<div style="background-color: var(--color-navy); color: #fff; padding: 4.5rem 0 3.5rem;">
    <div class="container-main">
        <span class="section-label" style="color: var(--color-gold);">Campus Culture</span>
        <h1 style="color: #fff; margin-bottom: 1rem;">Life & Community at Digital Teenz Academy</h1>
        <div class="section-divider" style="background-color: var(--color-gold);"></div>
        <p style="color: #D1D5DB; font-size: 1.125rem; max-width: 750px; line-height: 1.8;">
            Explore photos and video moments of workshops, hackathons, team coding jams, graduation ceremonies, and student celebrations.
        </p>
    </div>
</div>

<!-- Campus Gallery Grid -->
<?= View::component('gallery-grid', [
    'galleryItems' => $galleryItems,
    'galleryCategories' => $galleryCategories,
]) ?>

<!-- Culture Videos Grid -->
<?= View::component('culture-videos-grid', [
    'cultureVideos' => $cultureVideos,
]) ?>
