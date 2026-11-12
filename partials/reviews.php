<?php
/** reviews.php — fond bleu maiolique, marquee de carreaux-carreaux, note 4,8/5. */
$reviews = $config['reviews'];
?>
<section class="reviews" id="recensioni" aria-labelledby="reviews-title">
    <?php wave('var(--calce)', true, 'wave--top'); ?>

    <div class="wrap reviews__head">
        <div>
            <p class="label label--limone" data-reveal="up"><?= t($config, 'reviews.kicker') ?></p>
            <h2 class="h2 h2--light" id="reviews-title" data-reveal="up">
                <?= t($config, 'reviews.title1') ?> <span class="ital"><?= t($config, 'reviews.title2') ?></span>
            </h2>
        </div>
        <div class="reviews__score" data-reveal="up">
            <p class="reviews__rating"><?= e($config['reviews_meta']['rating']) ?> <span>/ 5</span></p>
            <p class="reviews__stars" aria-hidden="true">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <svg viewBox="0 0 100 100"><use href="#i-lemon-solid"/></svg>
                <?php endfor; ?>
            </p>
            <p class="reviews__count"><?= bi($config['reviews_meta']['count']) ?></p>
        </div>
    </div>

    <div class="reviews__marquee" data-marquee aria-label="<?= t($config, 'reviews.kicker') ?>">
        <div class="reviews__track" data-marquee-track>
            <?php for ($pass = 0; $pass < 2; $pass++): ?>
                <?php foreach ($reviews as $review): ?>
                <figure class="review-tile" <?= $pass === 1 ? 'aria-hidden="true"' : '' ?>>
                    <svg class="review-tile__frame" viewBox="0 0 100 100" aria-hidden="true"><use href="#i-tile-1"/></svg>
                    <blockquote class="review-tile__quote">«&nbsp;<?= bi($review['quote']) ?>&nbsp;»</blockquote>
                    <figcaption class="review-tile__author">
                        <span><?= e($review['author']) ?></span>
                        <small><?= bi($review['when']) ?></small>
                    </figcaption>
                </figure>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>

    <?php wave('var(--limone)', false, 'wave--bottom'); ?>
</section>
