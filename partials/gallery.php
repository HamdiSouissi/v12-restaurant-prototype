<?php
/** gallery.php — grille 3 colonnes en parallaxe, formes variées, lightbox Flip accessible. */
$cols = [[], [], []];
foreach ($config['gallery'] as $i => $photo) {
    $cols[$i % 3][] = ['index' => $i, 'photo' => $photo];
}
?>
<section class="gallery" id="galleria" aria-labelledby="gallery-title">
    <div class="wrap">
        <div class="gallery__head">
            <p class="label" data-reveal="up"><?= t($config, 'gallery.kicker') ?></p>
            <h2 class="h2" id="gallery-title" data-reveal="up">
                <?= t($config, 'gallery.title1') ?> <span class="ital"><?= t($config, 'gallery.title2') ?></span>
            </h2>
            <p class="gallery__hint label" aria-hidden="true"><?= t($config, 'gallery.hint') ?></p>
        </div>

        <div class="gallery__grid" data-gallery data-cursor="drag">
            <?php foreach ($cols as $c => $photos): ?>
            <div class="gallery__col" data-gallery-col="<?= $c ?>">
                <?php foreach ($photos as $entry): ?>
                <?php $p = $entry['photo']; $idx = $entry['index']; ?>
                <figure class="gallery__item gallery__item--<?= e($p['shape']) ?>">
                    <button class="gallery__btn" type="button"
                            data-lightbox="<?= $idx ?>" data-cursor="view"
                            aria-label="<?= bi($p['caption']) ?> — <?= t($config, 'gallery.hint') ?>">
                        <picture>
                            <source srcset="<?= img($p['img']) ?>.webp" type="image/webp">
                            <img src="<?= img($p['img']) ?>.jpg" alt="<?= bi($p['caption']) ?>"
                                 width="<?= (int) $p['w'] ?>" height="<?= (int) $p['h'] ?>" loading="lazy" decoding="async">
                        </picture>
                        <span class="gallery__glare" aria-hidden="true"></span>
                    </button>
                    <figcaption class="gallery__caption"><?= bi($p['caption']) ?></figcaption>
                </figure>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="<?= t($config, 'gallery.title1') ?> <?= t($config, 'gallery.title2') ?>" hidden>
        <div class="lightbox__tiles" aria-hidden="true"></div>
        <button class="lightbox__close" type="button" data-lightbox-close aria-label="<?= t($config, 'gallery.close') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-close"/></svg>
        </button>
        <button class="lightbox__nav lightbox__nav--prev" type="button" data-lightbox-prev aria-label="<?= t($config, 'gallery.prev') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-arrow-left"/></svg>
        </button>
        <figure class="lightbox__figure">
            <div class="lightbox__media" data-lightbox-media></div>
            <figcaption class="lightbox__caption">
                <span data-lightbox-caption></span>
                <span class="lightbox__count" data-lightbox-count>01 / 12</span>
            </figcaption>
        </figure>
        <button class="lightbox__nav lightbox__nav--next" type="button" data-lightbox-next aria-label="<?= t($config, 'gallery.next') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-arrow-right"/></svg>
        </button>
    </div>
</section>
