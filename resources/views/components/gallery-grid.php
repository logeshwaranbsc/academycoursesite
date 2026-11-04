<?php
/** @var array $galleryItems */
/** @var array $galleryCategories */
$items = $galleryItems ?? [];
$categories = $galleryCategories ?? [];
$limit = $limit ?? null;
if ($limit !== null) {
    $items = array_slice($items, 0, $limit);
}
?>
<section class="section-alt" id="gallery">
    <div class="container-main">
        <div style="margin-bottom: 2.5rem; text-align: center;">
            <span class="section-label">Campus Snapshots</span>
            <h2 class="section-title">Academy Photo Gallery</h2>
            <div class="section-divider-center"></div>
            <p style="color: #4B5563; max-width: 600px; margin: 0 auto;">
                A visual journey through student life, high-tech labs, workshops, celebrations, and graduation ceremonies.
            </p>
        </div>

        <!-- Category Filter Tabs -->
        <?php if (!empty($categories)): ?>
            <div class="flex items-center justify-center gap-2 flex-wrap" style="margin-bottom: 2.5rem;">
                <button type="button" class="gallery-cat-btn active btn-sm" data-category="All" style="background-color: var(--color-navy); color: #fff; border: 1px solid transparent; border-radius: 4px; cursor: pointer; padding: 0.4rem 1rem;">
                    All Photos
                </button>
                <?php foreach ($categories as $cat): ?>
                    <button type="button" class="gallery-cat-btn btn-sm" data-category="<?= e($cat) ?>" style="background-color: transparent; color: #4B5563; border: 1px solid var(--color-gray-300); border-radius: 4px; cursor: pointer; padding: 0.4rem 1rem;">
                        <?= e($cat) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($items)): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($items as $item): ?>
                    <div class="gallery-item" data-category="<?= e($item['category'] ?? 'Other') ?>">
                        <button type="button" class="gallery-trigger" data-img="<?= upload_url($item['image_url']) ?>" data-title="<?= e($item['title']) ?>" style="display: block; width: 100%; aspect-ratio: 1/1; border: none; padding: 0; background-color: var(--color-navy); border-radius: 6px; overflow: hidden; cursor: pointer; position: relative;">
                            <img src="<?= upload_url($item['image_url']) ?>" alt="<?= e($item['title']) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                            <div style="position: absolute; inset: 0; background: rgba(16,36,62,0.4); opacity: 0; transition: opacity 0.2s ease; display: flex; align-items: flex-end; padding: 1rem;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                                <span style="color: #fff; font-size: 0.8125rem; font-weight: 600; text-align: left;"><?= e($item['title']) ?></span>
                            </div>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: #fff; border-radius: 6px;">
                <p style="color: #6B7280; margin: 0;">Photos will be added shortly.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox Modal -->
<div id="gallery-lightbox" class="modal-backdrop">
    <div style="position: relative; max-width: 900px; width: 100%; text-align: center;">
        <button id="lightbox-close" style="position: absolute; top: -2.5rem; right: 0; background: none; border: none; color: #fff; font-size: 2rem; cursor: pointer;">
            &times;
        </button>
        <img id="lightbox-img" src="" alt="Gallery Preview" style="max-height: 80vh; width: auto; margin: 0 auto; border-radius: 6px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
        <p id="lightbox-title" style="color: #fff; margin-top: 1rem; font-size: 1rem; font-weight: 600;"></p>
    </div>
</div>
