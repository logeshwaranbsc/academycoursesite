<?php
/** @var array $cultureVideos */
$videos = $cultureVideos ?? [];
$limit = $limit ?? null;
if ($limit !== null) {
    $videos = array_slice($videos, 0, $limit);
}
?>
<section class="section" id="culture-videos" style="background-color: var(--color-white);">
    <div class="container-main">
        <div style="margin-bottom: 3rem; text-align: center;">
            <span class="section-label">Beyond The Books</span>
            <h2 class="section-title">Life & Culture At Digital Teenz Academy</h2>
            <div class="section-divider-center"></div>
            <p style="color: #4B5563; max-width: 650px; margin: 0 auto;">
                Experience our vibrant campus energy: hackathons, industry project showcases, technical meetups, and graduation ceremonies.
            </p>
        </div>

        <?php if (!empty($videos)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($videos as $v):
                    $embed = youtube_embed_url($v['video_url']);
                    $thumb = !empty($v['thumbnail_url']) ? upload_url($v['thumbnail_url']) : youtube_thumbnail($v['video_url']);
                ?>
                    <article class="card flex flex-col" style="padding: 0; overflow: hidden;">
                        <div class="video-container" style="position: relative; aspect-ratio: 16/9; background-color: #000; overflow: hidden;">
                            <?php if (!empty($thumb)): ?>
                                <img src="<?= e($thumb) ?>" alt="<?= e($v['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php endif; ?>
                            <button class="video-play-btn" data-embed="<?= e($embed) ?>" style="position: absolute; inset: 0; background: rgba(0,0,0,0.35); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 3rem; transition: background 0.2s ease;">
                                <span style="width: 4rem; height: 4rem; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--color-navy); box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                                    ▶
                                </span>
                            </button>
                        </div>

                        <div style="padding: 1.5rem;" class="flex-1 flex flex-col">
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--color-navy); margin-bottom: 0.5rem;">
                                <?= e($v['title']) ?>
                            </h3>
                            <?php if (!empty($v['description'])): ?>
                                <p style="font-size: 0.875rem; color: #4B5563; line-height: 1.7; flex: 1;">
                                    <?= e($v['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: var(--color-cream); border-radius: 6px;">
                <p style="color: #6B7280; margin: 0;">Campus life videos will be uploaded soon.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
