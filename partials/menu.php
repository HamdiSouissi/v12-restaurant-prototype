<?php
/** menu.php — onglets en carreaux de maiolique, filtres, cartes horizontales, stepper +/–, allergènes. */
$allergenIcons = ['glutine' => 'i-glutine', 'latte' => 'i-latte', 'uova' => 'i-uova', 'pesce' => 'i-pesce', 'crostacei' => 'i-crostacei'];
$catKeys = array_keys($config['menu']);
?>
<section class="menu-sec" id="menu" aria-labelledby="menu-title">
    <?php wave('var(--cielo)', true, 'wave--top'); ?>

    <div class="wrap">
        <div class="menu-sec__head">
            <p class="label" data-reveal="up"><?= t($config, 'menu.kicker') ?></p>
            <h2 class="h2" id="menu-title" data-reveal="up">
                <?= t($config, 'menu.title1') ?> <span class="ital"><?= t($config, 'menu.title2') ?></span>
            </h2>
        </div>

        <div class="menu-tabs" role="tablist" aria-label="<?= e($brand['subtitle']) ?>" data-reveal="up">
            <span class="menu-tabs__indicator" aria-hidden="true" data-tab-indicator></span>
            <?php foreach ($config['menu'] as $key => $cat): ?>
            <button class="menu-tab" type="button" role="tab"
                    id="tab-<?= e($key) ?>"
                    aria-controls="panel-<?= e($key) ?>"
                    aria-selected="<?= $key === $catKeys[0] ? 'true' : 'false' ?>"
                    tabindex="<?= $key === $catKeys[0] ? '0' : '-1' ?>"
                    data-tab="<?= e($key) ?>">
                <svg class="menu-tab__tile" viewBox="0 0 100 100" aria-hidden="true"><use href="#i-<?= e($cat['tile']) ?>"/></svg>
                <span class="menu-tab__label" data-i18n-cat="<?= e($key) ?>" data-it="<?= e($cat['label']['it']) ?>" data-en="<?= e($cat['label']['en'] ?? $cat['label']['it']) ?>"><?= bi($cat['label']) ?></span>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="menu-filters" role="group" aria-label="<?= t($config, 'menu.kicker') ?>">
            <?php foreach ($config['filters'] as $key => $label): ?>
            <button class="chip <?= $key === 'tutti' ? 'is-active' : '' ?>" type="button"
                    data-filter="<?= e($key) ?>" aria-pressed="<?= $key === 'tutti' ? 'true' : 'false' ?>"
                    data-it="<?= e($label['it']) ?>" data-en="<?= e($label['en'] ?? $label['it']) ?>"><?= bi($label) ?></button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($config['menu'] as $key => $cat): ?>
        <div class="menu-panel" role="tabpanel" id="panel-<?= e($key) ?>" aria-labelledby="tab-<?= e($key) ?>"
             data-panel="<?= e($key) ?>" <?= $key === $catKeys[0] ? '' : 'hidden' ?>>
            <ul class="menu-list">
                <?php foreach ($cat['items'] as $item): ?>
                <li class="dish" data-tags="<?= e(implode(' ', $item['tags'])) ?>" data-slug="<?= e($item['slug']) ?>">
                    <div class="dish__thumb">
                        <picture>
                            <source srcset="<?= img($item['slug'], 'piatti') ?>.webp" type="image/webp">
                            <img src="<?= img($item['slug'], 'piatti') ?>.jpg" alt="<?= bi($item['name']) ?>"
                                 width="460" height="460" loading="lazy" decoding="async"
                                 data-fallback="<?= bi($item['name']) ?>">
                        </picture>
                        <span class="dish__glare" aria-hidden="true"></span>
                        <span class="dish__corner" aria-hidden="true">
                            <svg viewBox="0 0 100 100"><use href="#i-<?= e($cat['tile']) ?>"/></svg>
                        </span>
                    </div>

                    <div class="dish__body">
                        <h3 class="dish__name" data-it="<?= e($item['name']['it']) ?>" data-en="<?= e($item['name']['en'] ?? $item['name']['it']) ?>"><?= bi($item['name']) ?></h3>
                        <p class="dish__desc" data-it="<?= e($item['desc']['it']) ?>" data-en="<?= e($item['desc']['en'] ?? $item['desc']['it']) ?>"><?= bi($item['desc']) ?></p>
                        <ul class="dish__allergens" aria-label="<?= t($config, 'menu.allergens') ?>">
                            <?php foreach ($item['allergens'] as $a): ?>
                            <li class="dish__allergen" title="<?= t($config, 'menu.' . $a) ?>">
                                <svg viewBox="0 0 100 100" aria-hidden="true"><use href="#<?= e($allergenIcons[$a]) ?>"/></svg>
                                <span class="visually-hidden"><?= t($config, 'menu.' . $a) ?></span>
                            </li>
                            <?php endforeach; ?>
                            <?php foreach ($item['tags'] as $tag): ?>
                            <?php $filterKey = $tag === 'gf' ? 'senza-glutine' : ($tag === 'lf' ? 'senza-lattosio' : $tag); ?>
                            <li class="dish__tag"><?= bi($config['filters'][$filterKey]) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="dish__side">
                        <p class="price"><span class="price__cur">€</span> <?= (int) $item['price'] ?></p>
                        <div class="stepper" data-stepper>
                            <button class="stepper__btn stepper__add" type="button"
                                    data-add="<?= e($item['slug']) ?>"
                                    data-name="<?= bi($item['name']) ?>"
                                    data-price="<?= (int) $item['price'] ?>"
                                    aria-label="<?= t($config, 'menu.add') ?>: <?= bi($item['name']) ?>">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-plus"/></svg>
                            </button>
                            <span class="stepper__qty" aria-hidden="true">1</span>
                            <button class="stepper__btn stepper__minus" type="button"
                                    data-remove="<?= e($item['slug']) ?>"
                                    aria-label="<?= t($config, 'menu.remove') ?>: <?= bi($item['name']) ?>">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-minus"/></svg>
                            </button>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <p class="menu-empty" hidden><?= t($config, 'menu.empty') ?></p>
        </div>
        <?php endforeach; ?>

        <p class="menu-note"><?= t($config, 'menu.note') ?></p>
    </div>

    <?php wave('var(--limone)', false, 'wave--bottom'); ?>
</section>
