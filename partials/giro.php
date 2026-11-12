<?php
/** giro.php — carte de la côte en SVG, 4 étapes, gozzo qui navigue, prix en direct. */
$giro = $config['giro'];
$save = $giro['price'] - $giro['bundle_price'];
?>
<section class="giro" id="giro" aria-labelledby="giro-title">
    <div class="wrap">
        <div class="giro__head">
            <p class="label" data-reveal="up"><?= t($config, 'giro.kicker') ?></p>
            <h2 class="h2" id="giro-title" data-reveal="up">
                <?= t($config, 'giro.title1') ?> <span class="ital"><?= t($config, 'giro.title2') ?></span>
            </h2>
            <p class="giro__intro" data-reveal="up"><?= t($config, 'giro.intro') ?></p>
        </div>

        <div class="giro__layout">
            <div class="giro__mapwrap" data-reveal="up">
                <svg class="giro__map" viewBox="0 0 1000 520" role="img" aria-labelledby="giro-map-title">
                    <title id="giro-map-title">Carta della costa: Positano, Amalfi, Ravello, Cetara</title>
                    <defs>
                        <linearGradient id="sea-grad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0" stop-color="#2E69C0"/><stop offset="1" stop-color="#123A78"/>
                        </linearGradient>
                        <pattern id="sea-lines" width="34" height="18" patternUnits="userSpaceOnUse">
                            <path d="M0 12c6 0 8-4 14-4s8 4 14 4" fill="none" stroke="#FBF6E9" stroke-width="1.4" opacity=".25"/>
                        </pattern>
                    </defs>

                    <rect width="1000" height="520" fill="url(#sea-grad)" rx="14"/>
                    <rect width="1000" height="520" fill="url(#sea-lines)" rx="14"/>

                    <!-- terre crème, ligne de côte irrégulière -->
                    <path class="giro__land" d="M0 0h1000v96c-60 6-96 22-140 20-42-2-60 18-96 22-46 5-70-14-108-6-46 9-62 34-96 44-40 12-72-4-110 4-44 9-64 34-108 40-52 7-86-14-128-10-56 6-88 30-128 26-32-3-56-18-86-16Z"
                          fill="var(--calce)"/>
                    <path d="M0 96c30-2 54 13 86 16 40 4 72-20 128-26 42-4 76 17 128 10 44-6 64-31 108-40 38-8 70 8 110-4 34-10 50-35 96-44 38-8 62 11 108 6 36-4 54-24 96-22 44 2 80-14 140-20"
                          fill="none" stroke="var(--notte)" stroke-width="3" opacity=".35"/>

                    <!-- route pointillée entre les étapes -->
                    <path id="giro-route" class="giro__route"
                          d="M200 135 C 300 205, 360 245, 440 250 S 560 145, 660 115 S 800 235, 860 302"
                          fill="none" stroke="#F2CF3A" stroke-width="4" stroke-dasharray="3 12" stroke-linecap="round"/>

                    <?php foreach ($giro['stops'] as $stop): ?>
                    <g class="giro__stop" data-stop="<?= e($stop['key']) ?>" transform="translate(<?= (int) $stop['x'] * 10 ?> <?= (int) $stop['y'] * 5.2 ?>)">
                        <circle class="giro__halo" r="30" fill="#F2CF3A" opacity="0"/>
                        <circle class="giro__dot" r="19" fill="var(--calce)" stroke="var(--notte)" stroke-width="3.5"/>
                        <text class="giro__num" y="6" text-anchor="middle"><?= (int) $stop['num'] ?></text>
                        <text class="giro__name" y="46" text-anchor="middle"><?= e($stop['name']) ?></text>
                    </g>
                    <?php endforeach; ?>

                    <!-- gozzo qui navigue -->
                    <g class="giro__boat" id="giro-boat" transform="translate(200 135)">
                        <g transform="translate(-60 -40)">
                            <use href="#i-gozzo" width="120" height="80" style="color:var(--notte)"/>
                        </g>
                    </g>

                    <!-- petits poissons décoratifs -->
                    <g opacity=".5" stroke="var(--calce)" fill="none" stroke-width="2">
                        <use href="#i-fish" x="90" y="330" width="46" height="46"/>
                        <use href="#i-fish" x="740" y="390" width="34" height="34"/>
                        <use href="#i-shell" x="520" y="430" width="38" height="38"/>
                    </g>
                </svg>
                <p class="giro__mapnote label"><?= t($config, 'giro.choose') ?> · <?= count($giro['stops']) ?> / <?= count($giro['stops']) ?></p>
            </div>

            <div class="giro__panel" data-reveal="up">
                <h3 class="giro__panel-title"><?= t($config, 'giro.your_route') ?></h3>
                <ol class="giro__route-list" id="giro-route-list">
                    <?php foreach ($giro['stops'] as $stop): ?>
                    <li data-route="<?= e($stop['key']) ?>">
                        <span class="giro__route-num"><?= (int) $stop['num'] ?></span>
                        <span class="giro__route-stop"><?= e($stop['name']) ?> <em>(<?= bi($stop['course']) ?>)</em></span>
                        <span class="giro__route-dish" data-route-dish>—</span>
                    </li>
                    <?php endforeach; ?>
                </ol>

                <div class="giro__price">
                    <p class="giro__old"><span aria-hidden="true">€ 45</span><span class="visually-hidden">45</span></p>
                    <p class="giro__now"><span class="giro__now-cur">€</span> <span data-giro-price><?= (int) $giro['bundle_price'] ?></span></p>
                    <p class="giro__save"><?= t($config, 'giro.save') ?> € <?= (int) $save ?></p>
                </div>

                <button class="btn btn--outline btn--lg giro__cta" type="button" id="giro-cta" disabled>
                    <span data-giro-cta-text><?= t($config, 'giro.cta') ?></span>
                    <small class="giro__missing" data-giro-missing></small>
                </button>
            </div>
        </div>

        <div class="giro__steps">
            <?php foreach ($giro['stops'] as $stop): ?>
            <fieldset class="giro__step" data-step="<?= e($stop['key']) ?>" data-reveal="up">
                <legend>
                    <span class="giro__step-num"><?= (int) $stop['num'] ?></span>
                    <span class="giro__step-name"><?= e($stop['name']) ?></span>
                    <span class="giro__step-course"><?= bi($stop['course']) ?></span>
                </legend>
                <div class="giro__options">
                    <?php foreach ($stop['options'] as $opt): ?>
                    <label class="giro-opt">
                        <input type="radio" name="giro-<?= e($stop['key']) ?>"
                               value="<?= e($opt['slug']) ?>"
                               data-giro-pick
                               data-stop="<?= e($stop['key']) ?>"
                               data-opt-name="<?= bi($opt['name']) ?>"
                               data-opt-price="<?= (int) $opt['price'] ?>">
                        <span class="giro-opt__box">
                            <span class="giro-opt__thumb">
                                <picture>
                                    <source srcset="<?= img($opt['slug'], 'piatti') ?>.webp" type="image/webp">
                                    <img src="<?= img($opt['slug'], 'piatti') ?>.jpg" alt="" width="120" height="120" loading="lazy">
                                </picture>
                            </span>
                            <span class="giro-opt__text">
                                <span class="giro-opt__name"><?= bi($opt['name']) ?></span>
                                <span class="giro-opt__price">€ <?= (int) $opt['price'] ?></span>
                            </span>
                            <svg class="giro-opt__check" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-check"/></svg>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endforeach; ?>
        </div>
    </div>

    <?php wave('var(--calce)', false, 'wave--bottom'); ?>
</section>
