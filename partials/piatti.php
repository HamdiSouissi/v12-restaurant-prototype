<?php
/** piatti.php — carrousel Swiper centré, cartes en arche sur une ligne ondulée. */
?>
<section class="piatti" id="piatti" aria-labelledby="piatti-title">
    <div class="wrap piatti__head">
        <p class="label" data-reveal="up"><?= t($config, 'piatti.kicker') ?></p>
        <h2 class="h2" id="piatti-title" data-reveal="up">
            <?= t($config, 'piatti.title1') ?> <span class="ital"><?= t($config, 'piatti.title2') ?></span>
        </h2>
    </div>

    <div class="piatti__carousel" data-reveal="up">
        <div class="swiper piatti-swiper" id="piatti-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($config['signature'] as $i => $dish): ?>
                <article class="swiper-slide piatti__slide" data-wave-index="<?= $i ?>" data-slug="<?= e($dish['slug']) ?>">
                    <div class="piatti__card piatti__card--<?= e($dish['tone']) ?>" data-tilt>
                        <div class="piatti__photo">
                            <picture>
                                <source srcset="<?= img($dish['slug'], 'piatti') ?>.webp" type="image/webp">
                                <img src="<?= img($dish['slug'], 'piatti') ?>.jpg" alt="<?= bi($dish['name']) ?>"
                                     width="460" height="460" loading="lazy" decoding="async"
                                     data-fallback="<?= bi($dish['name']) ?>">
                            </picture>
                            <span class="piatti__glare" aria-hidden="true"></span>
                        </div>
                        <div class="piatti__content">
                            <h3 class="piatti__name" data-it="<?= e($dish['name']['it']) ?>" data-en="<?= e($dish['name']['en'] ?? $dish['name']['it']) ?>"><?= bi($dish['name']) ?></h3>
                            <p class="piatti__desc" data-it="<?= e($dish['desc']['it']) ?>" data-en="<?= e($dish['desc']['en'] ?? $dish['desc']['it']) ?>"><?= bi($dish['desc']) ?></p>
                            <div class="piatti__row">
                                <p class="price price--lg"><span class="price__cur">€</span> <?= (int) $dish['price'] ?></p>
                                <button class="btn btn--sm piatti__add <?= $dish['tone'] === 'maiolica' ? 'btn--cream' : 'btn--limone' ?>"
                                        type="button"
                                        data-add="<?= e($dish['slug']) ?>"
                                        data-name="<?= bi($dish['name']) ?>"
                                        data-price="<?= (int) $dish['price'] ?>">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-plus"/></svg>
                                    <span><?= t($config, 'piatti.add') ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="piatti__controls">
            <button class="round-btn" type="button" data-piatti-prev aria-label="<?= t($config, 'piatti.prev') ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-arrow-left"/></svg>
            </button>
            <div class="piatti__pagination" data-piatti-pagination></div>
            <button class="round-btn" type="button" data-piatti-next aria-label="<?= t($config, 'piatti.next') ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-arrow-right"/></svg>
            </button>
        </div>
    </div>
</section>
