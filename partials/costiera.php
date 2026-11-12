<?php
/** costiera.php — fond bleu maiolique, photo en arche qui respire, compteurs, sceau cotto. */
?>
<section class="costiera" id="costiera" aria-labelledby="costiera-title">
    <?php wave('var(--calce)', true, 'wave--top'); ?>

    <div class="wrap costiera__grid">
        <figure class="costiera__media" data-reveal="arch">
            <picture>
                <source srcset="<?= img('arco-01') ?>.webp" type="image/webp">
                <img src="<?= img('arco-01') ?>.jpg" alt="<?= t($config, 'costiera.img_alt') ?>"
                     width="1080" height="1394" loading="lazy" decoding="async">
            </picture>
            <figcaption class="costiera__caption label"><?= e($brand['tagline']) ?></figcaption>
        </figure>

        <div class="costiera__body">
            <p class="label label--limone" data-reveal="up"><?= t($config, 'costiera.kicker') ?></p>
            <h2 class="h2 h2--light" id="costiera-title" data-reveal="up">
                <?= t($config, 'costiera.title1') ?> <span class="ital"><?= t($config, 'costiera.title2') ?></span>
            </h2>
            <p class="costiera__text" data-reveal="up"><?= t($config, 'costiera.text') ?></p>

            <dl class="stats" data-stats>
                <?php foreach ($config['stats'] as $stat): ?>
                <div class="stats__item">
                    <dt class="stats__value" aria-hidden="true" data-count="<?= (int) $stat['value'] ?>">0</dt>
                    <dd class="stats__label">
                        <span class="visually-hidden"><?= (int) $stat['value'] ?> </span><?= bi($stat['label']) ?>
                    </dd>
                </div>
                <?php endforeach; ?>
            </dl>
        </div>

        <div class="costiera__seal" aria-hidden="true">
            <svg viewBox="0 0 120 120" class="costiera__seal-svg">
                <use href="#i-seal"/>
                <text class="costiera__seal-text">
                    <textPath href="#seal-path" startOffset="0"><?= t($config, 'costiera.seal') ?> · <?= t($config, 'costiera.seal') ?> · </textPath>
                </text>
            </svg>
            <svg viewBox="0 0 100 100" class="costiera__seal-lemon"><use href="#i-lemon-solid"/></svg>
        </div>
    </div>

    <?php wave('var(--calce)', false, 'wave--bottom'); ?>
</section>
