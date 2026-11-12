<?php
/**
 * Bibliothèque de symboles SVG dessinés à la main.
 * Aucune image externe, aucun emoji : citrons, vagues, gozzo, tuiles de maiolique.
 * Trait net, currentColor ; les tuiles utilisent les couleurs de la palette.
 */
$tiles = [
    'tile-1' => 'sole', 'tile-2' => 'onda', 'tile-3' => 'croce',
    'tile-4' => 'reticolo', 'tile-5' => 'foglia', 'tile-6' => 'squame',
];
?>
<svg class="svg-sprite" aria-hidden="true" focusable="false" width="0" height="0" style="position:absolute;overflow:hidden">
    <defs>
        <linearGradient id="grad-sea" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#7FB6D8"/><stop offset="1" stop-color="#1B4B9B"/>
        </linearGradient>
        <linearGradient id="grad-lemon" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#FBE58C"/><stop offset="1" stop-color="#F2CF3A"/>
        </linearGradient>
        <radialGradient id="grad-glare" cx="0.3" cy="0.25" r="0.75">
            <stop offset="0" stop-color="#FFFDF4" stop-opacity=".95"/>
            <stop offset="1" stop-color="#FFFDF4" stop-opacity="0"/>
        </radialGradient>
    </defs>

    <!-- ============================================================ CITRONS -->
    <symbol id="i-lemon" viewBox="0 0 100 100">
        <path d="M50 16c7 0 9 4 13 8 7 6 12 13 12 21 0 15-11 27-25 27S25 60 25 45c0-8 5-15 12-21 4-4 6-8 13-8Z"
              fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round"/>
        <path d="M50 16c-2-4 0-7 3-8M50 72c2 4 0 7-3 8" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        <path d="M38 32c-4 5-6 11-6 16" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" opacity=".45"/>
    </symbol>

    <symbol id="i-lemon-solid" viewBox="0 0 100 100">
        <path d="M50 15c7 0 9 4 13 8 7 6 12 14 12 22 0 15-11 27-25 27S25 60 25 45c0-8 5-16 12-22 4-4 6-8 13-8Z"
              fill="url(#grad-lemon)" stroke="#0F2547" stroke-width="3"/>
        <path d="M50 15c-2-4 0-7 3-8M50 72c2 4 0 7-3 8" fill="none" stroke="#3E6B3A" stroke-width="3" stroke-linecap="round"/>
        <path d="M39 30c-4 5-6 10-6 15" fill="none" stroke="#FFFDF4" stroke-width="3" stroke-linecap="round" opacity=".55"/>
    </symbol>

    <symbol id="i-lemon-slice" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="36" fill="url(#grad-lemon)" stroke="#0F2547" stroke-width="3.6"/>
        <circle cx="50" cy="50" r="30" fill="none" stroke="#FFFDF4" stroke-width="3" opacity=".9"/>
        <?php for ($i = 0; $i < 8; $i++): ?>
        <path d="M50 50 L50 22" fill="none" stroke="#FFFDF4" stroke-width="3.4" stroke-linecap="round"
              transform="rotate(<?= $i * 45 ?> 50 50)"/>
        <?php endfor; ?>
        <circle cx="50" cy="50" r="4" fill="#FFFDF4"/>
    </symbol>

    <symbol id="i-lemon-half" viewBox="0 0 100 100">
        <path d="M12 30a38 38 0 0 0 76 0Z" fill="url(#grad-lemon)" stroke="#0F2547" stroke-width="3.4"/>
        <path d="M20 32a30 30 0 0 0 60 0" fill="none" stroke="#FFFDF4" stroke-width="3"/>
        <?php for ($i = -3; $i <= 3; $i++): ?>
        <path d="M50 33 L<?= 50 + $i * 8 ?> 62" fill="none" stroke="#FFFDF4" stroke-width="2.6" stroke-linecap="round" opacity=".9"/>
        <?php endfor; ?>
    </symbol>

    <!-- branche de citronnier : tige, feuilles, deux fruits -->
    <symbol id="i-branch" viewBox="0 0 200 140">
        <path d="M4 12C40 24 74 34 104 52c22 13 44 22 92 26" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
        <path d="M60 30c2 14-6 26-20 30 4-14 9-24 20-30Z" fill="#3E6B3A"/>
        <path d="M96 46c-6 12-2 26 10 34 4-14 2-26-10-34Z" fill="#3E6B3A"/>
        <path d="M140 66c-10 8-12 22-6 34 10-8 14-22 6-34Z" fill="#3E6B3A"/>
        <path d="M40 22c-8-8-20-10-30-6 8 8 20 10 30 6Z" fill="#3E6B3A"/>
        <path d="M112 56c-8-10-20-14-30-12 6 10 20 14 30 12Z" fill="#3E6B3A"/>
        <g fill="url(#grad-lemon)" stroke="#0F2547" stroke-width="2.6">
            <path d="M74 62c9 0 16 8 16 17s-7 17-16 17-16-8-16-17 7-17 16-17Z"/>
            <path d="M158 82c8 0 14 7 14 15s-6 15-14 15-14-7-14-15 6-15 14-15Z"/>
        </g>
        <path d="M78 40l10-6" stroke="#3E6B3A" stroke-width="3" stroke-linecap="round"/>
    </symbol>

    <symbol id="i-leaf" viewBox="0 0 100 100">
        <path d="M22 78C22 44 46 20 80 20c0 34-24 58-58 58Z" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linejoin="round"/>
        <path d="M22 78C40 58 58 42 76 30" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>
    </symbol>

    <!-- ============================================================ CIEL -->
    <symbol id="i-sun" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="17" fill="url(#grad-lemon)" stroke="#0F2547" stroke-width="3.2"/>
        <g stroke="#F2CF3A" stroke-width="3.6" stroke-linecap="round">
            <?php for ($i = 0; $i < 12; $i++): ?>
            <path d="M50 6v12" transform="rotate(<?= $i * 30 ?> 50 50)"/>
            <?php endfor; ?>
        </g>
    </symbol>

    <symbol id="i-sun-rays" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="14" fill="none" stroke="currentColor" stroke-width="3"/>
        <g stroke="currentColor" stroke-width="2.6" stroke-linecap="round">
            <?php for ($i = 0; $i < 16; $i++): ?>
            <path d="M50 <?= $i % 2 ? 20 : 12 ?>v<?= $i % 2 ? 6 : 10 ?>" transform="rotate(<?= $i * 22.5 ?> 50 50)" opacity="<?= $i % 2 ? '.55' : '1' ?>"/>
            <?php endfor; ?>
        </g>
    </symbol>

    <symbol id="i-wave" viewBox="0 0 240 40" preserveAspectRatio="none">
        <path d="M0 22c20 0 30-12 50-12s30 12 50 12 30-12 50-12 30 12 50 12 20-6 40-6v24H0Z" fill="currentColor"/>
    </symbol>

    <symbol id="i-wave-line" viewBox="0 0 240 40" preserveAspectRatio="none">
        <path d="M0 20c20 0 30-12 50-12s30 12 50 12 30-12 50-12 30 12 50 12 20-6 40-6"
              fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
    </symbol>

    <!-- ============================================================ MER -->
    <symbol id="i-fish" viewBox="0 0 100 100">
        <path d="M14 50c14-16 32-24 48-24 12 0 20 6 24 14-6 8-6 12 0 20-4 8-12 14-24 14-16 0-34-8-48-24Z"
              fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
        <path d="M86 40c6-6 10-8 14-6-2 6-2 10 0 16-4 2-8 0-14-6" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
        <circle cx="36" cy="45" r="3.4" fill="currentColor"/>
        <path d="M58 38c2 8 2 16 0 24M48 36c2 9 2 19 0 28" fill="none" stroke="currentColor" stroke-width="2.2" opacity=".55"/>
    </symbol>

    <symbol id="i-shell" viewBox="0 0 100 100">
        <path d="M50 82C30 82 14 68 14 52 14 34 30 18 50 18s36 16 36 34C86 68 70 82 50 82Z"
              fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
        <path d="M50 18v64M34 22c-4 20-4 40 0 58M66 22c4 20 4 40 0 58M20 32c-2 18-2 34 2 46M80 32c2 18 2 34-2 46"
              fill="none" stroke="currentColor" stroke-width="2.2" opacity=".7"/>
        <path d="M38 84h24" stroke="currentColor" stroke-width="3.4" stroke-linecap="round"/>
    </symbol>

    <symbol id="i-starfish" viewBox="0 0 100 100">
        <path d="M50 12l10 24 26 2-20 17 6 26-22-14-22 14 6-26-20-17 26-2Z"
              fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
        <circle cx="50" cy="48" r="4" fill="currentColor"/>
    </symbol>

    <!-- gozzo : barque de la côte amalfitaine, proue haute, œil peint -->
    <symbol id="i-gozzo" viewBox="0 0 120 80">
        <path d="M10 54c10 12 24 16 50 16s40-4 50-16c-16 3-30 4-50 4s-34-1-50-4Z"
              fill="currentColor" stroke="none"/>
        <path d="M10 54c10 12 24 16 50 16s40-4 50-16" fill="none" stroke="#0F2547" stroke-width="2.6" stroke-linejoin="round"/>
        <path d="M10 54 26 40h68l16 14" fill="none" stroke="#0F2547" stroke-width="2.6" stroke-linejoin="round"/>
        <path d="M18 40V24c0-2 2-3 4-2l8 5v13" fill="none" stroke="#0F2547" stroke-width="2.6" stroke-linejoin="round"/>
        <path d="M58 40V14m0 0 -14 10m14-10 14 10" fill="none" stroke="#0F2547" stroke-width="2.6" stroke-linecap="round"/>
        <circle cx="86" cy="47" r="3.4" fill="#FBF6E9" stroke="#0F2547" stroke-width="1.8"/>
        <circle cx="34" cy="47" r="2.4" fill="#FBF6E9" stroke="#0F2547" stroke-width="1.6"/>
    </symbol>

    <!-- ============================================================ ARCHITECTURE -->
    <symbol id="i-arch" viewBox="0 0 100 120">
        <path d="M8 112V50a42 42 0 0 1 84 0v62" fill="none" stroke="currentColor" stroke-width="3.4"/>
        <path d="M18 112V50a32 32 0 0 1 64 0v62" fill="none" stroke="currentColor" stroke-width="2" opacity=".5"/>
    </symbol>

    <symbol id="i-pin" viewBox="0 0 100 120">
        <path d="M50 10c19 0 32 14 32 32 0 22-24 44-32 60-8-16-32-38-32-60 0-18 13-32 32-32Z"
              fill="url(#grad-lemon)" stroke="#0F2547" stroke-width="3.4"/>
        <circle cx="50" cy="42" r="11" fill="none" stroke="#0F2547" stroke-width="3"/>
        <path d="M44 30c-5 3-8 8-8 13" fill="none" stroke="#FFFDF4" stroke-width="3" stroke-linecap="round" opacity=".7"/>
    </symbol>

    <!-- ============================================================ TUILES MAIOLIQUE -->
    <?php // six carreaux composés, chacun dans un cadre de 6 -->
    foreach ($tiles as $id => $kind): ?>
    <symbol id="i-<?= $id ?>" viewBox="0 0 100 100">
        <rect x="3" y="3" width="94" height="94" fill="none" stroke="currentColor" stroke-width="4"/>
        <rect x="10" y="10" width="80" height="80" fill="none" stroke="currentColor" stroke-width="1.4" opacity=".55"/>
        <?php if ($kind === 'sole'): ?>
            <circle cx="50" cy="50" r="9" fill="currentColor"/>
            <?php for ($i = 0; $i < 8; $i++): ?>
            <path d="M50 34c4 0 6 4 4 8-2 4-8 4-8-1 0-4 2-7 4-7Z" fill="currentColor" opacity=".85"
                  transform="rotate(<?= $i * 45 ?> 50 50)"/>
            <?php endfor; ?>
            <path d="M10 10h16M10 10v16M90 10H74M90 10v16M10 90h16M10 90V74M90 90H74M90 90V74"
                  stroke="currentColor" stroke-width="2.6" fill="none"/>
        <?php elseif ($kind === 'onda'): ?>
            <path d="M16 54c0-11 8-19 18-19 7 0 12 5 12 11 0 5-4 9-9 9-4 0-7-3-7-6" fill="none" stroke="currentColor" stroke-width="3.6" stroke-linecap="round"/>
            <path d="M84 46c0 11-8 19-18 19-7 0-12-5-12-11 0-5 4-9 9-9 4 0 7 3 7 6" fill="none" stroke="currentColor" stroke-width="3.6" stroke-linecap="round"/>
            <circle cx="50" cy="50" r="3.6" fill="currentColor"/>
            <path d="M22 78h56" stroke="currentColor" stroke-width="2.4" stroke-dasharray="6 6"/>
        <?php elseif ($kind === 'croce'): ?>
            <path d="M50 22l28 28-28 28-28-28Z" fill="none" stroke="currentColor" stroke-width="3.4"/>
            <circle cx="50" cy="50" r="7" fill="currentColor"/>
            <path d="M10 10l14 14M90 10L76 24M10 90l14-14M90 90L76 76" stroke="currentColor" stroke-width="2.6"/>
            <circle cx="18" cy="18" r="2.6" fill="currentColor"/><circle cx="82" cy="18" r="2.6" fill="currentColor"/>
            <circle cx="18" cy="82" r="2.6" fill="currentColor"/><circle cx="82" cy="82" r="2.6" fill="currentColor"/>
        <?php elseif ($kind === 'reticolo'): ?>
            <g stroke="currentColor" stroke-width="1.8" opacity=".8">
                <path d="M10 30h80M10 50h80M10 70h80M30 10v80M50 10v80M70 10v80"/>
            </g>
            <g fill="currentColor">
                <circle cx="30" cy="30" r="3"/><circle cx="70" cy="30" r="3"/>
                <circle cx="50" cy="50" r="5"/><circle cx="30" cy="70" r="3"/><circle cx="70" cy="70" r="3"/>
            </g>
            <path d="M10 10h80v80H10Z" fill="none" stroke="currentColor" stroke-width="2.4"/>
        <?php elseif ($kind === 'foglia'): ?>
            <path d="M50 82C34 70 28 54 50 22c22 32 16 48 0 60Z" fill="none" stroke="currentColor" stroke-width="3.2"/>
            <path d="M50 74V30" stroke="currentColor" stroke-width="2.2"/>
            <path d="M50 46c-8 0-12 4-14 10M50 60c8 0 12-4 14-10" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
            <path d="M10 10h80v80H10Z" fill="none" stroke="currentColor" stroke-width="2.2"/>
        <?php else: ?>
            <g fill="none" stroke="currentColor" stroke-width="2.6">
                <?php for ($r = 0; $r < 3; $r++): ?>
                    <?php for ($c = 0; $c < 3; $c++): ?>
                <path d="M<?= 22 + $c * 28 ?> <?= 28 + $r * 24 ?>a14 14 0 0 1 28 0" opacity=".85"/>
                    <?php endfor; ?>
                <?php endfor; ?>
            </g>
            <path d="M10 10h80v80H10Z" fill="none" stroke="currentColor" stroke-width="3"/>
        <?php endif; ?>
    </symbol>
    <?php endforeach; ?>

    <!-- ============================================================ UI -->
    <symbol id="i-crate" viewBox="0 0 100 100">
        <path d="M12 34h76l-6 44H18Z" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linejoin="round"/>
        <path d="M20 50h60M18 66h64M36 34l4 44M64 34l-4 44" fill="none" stroke="currentColor" stroke-width="2.2" opacity=".65"/>
        <path d="M22 34c0-10 12-16 28-16s28 6 28 16" fill="none" stroke="currentColor" stroke-width="3.4"/>
        <circle cx="50" cy="74" r="7" fill="url(#grad-lemon)" stroke="currentColor" stroke-width="2.4"/>
    </symbol>

    <symbol id="i-cart" viewBox="0 0 100 100">
        <path d="M14 26h10l10 40h44l8-28H32" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="42" cy="80" r="5" fill="currentColor"/><circle cx="72" cy="80" r="5" fill="currentColor"/>
    </symbol>

    <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/></symbol>
    <symbol id="i-minus" viewBox="0 0 24 24"><path d="M5 12h14" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/></symbol>
    <symbol id="i-close" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M4 13l5 5L20 6" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-arrow-right" viewBox="0 0 24 24"><path d="M4 12h15M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-arrow-left" viewBox="0 0 24 24"><path d="M20 12H5M11 6l-6 6 6 6" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-arrow-up" viewBox="0 0 24 24"><path d="M12 20V5M6 11l6-6 6 6" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-chevron-left" viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-chevron-right" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-star" viewBox="0 0 24 24"><path d="M12 3l2.7 5.8 6.3.8-4.7 4.3 1.3 6.2-5.6-3.2-5.6 3.2 1.3-6.2L3 9.6l6.3-.8Z" fill="currentColor"/></symbol>
    <symbol id="i-star-line" viewBox="0 0 100 100"><path d="M50 12l10 24 26 2-20 17 6 26-22-14-22 14 6-26-20-17 26-2Z" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/></symbol>

    <symbol id="i-glutine" viewBox="0 0 100 100">
        <path d="M50 14v72M26 28c14 6 24 16 30 28M74 28c-14 6-24 16-30 28M42 20c-6 4-8 10-8 16M58 20c6 4 8 10 8 16" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        <path d="M50 14a6 6 0 0 1 3 11" fill="none" stroke="currentColor" stroke-width="3"/>
    </symbol>
    <symbol id="i-latte" viewBox="0 0 100 100">
        <path d="M26 34h48v34a20 20 0 0 1-20 20H46a20 20 0 0 1-20-20Z" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
        <path d="M26 34l10-18h28l10 18" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
        <path d="M38 54c4 6 20 6 24 0" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>
        <path d="M50 20v8" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>
    </symbol>
    <symbol id="i-uova" viewBox="0 0 100 100">
        <path d="M50 16c14 0 26 24 26 40S64 86 50 86 24 72 24 56s12-40 26-40Z" fill="none" stroke="currentColor" stroke-width="3.2"/>
        <path d="M50 46c5 0 9 6 9 12s-4 10-9 10-9-4-9-10 4-12 9-12Z" fill="none" stroke="currentColor" stroke-width="2.4"/>
    </symbol>
    <symbol id="i-crostacei" viewBox="0 0 100 100">
        <path d="M34 34c-8 8-10 20-4 28 4 6 12 8 20 8s16-2 20-8c6-8 4-20-4-28" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        <path d="M50 70c-4 6-2 14 6 16M34 34c0-8 6-14 16-14s16 6 16 14" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        <path d="M26 44l-12-6M28 54l-14 2M74 44l12-6M72 54l14 2M50 22v-8" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>
        <circle cx="42" cy="44" r="2.4" fill="currentColor"/><circle cx="58" cy="44" r="2.4" fill="currentColor"/>
    </symbol>
    <symbol id="i-pesce" viewBox="0 0 100 100">
        <path d="M14 50c14-16 32-24 48-24 12 0 20 6 24 14-6 8-6 12 0 20-4 8-12 14-24 14-16 0-34-8-48-24Z" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
        <path d="M86 40c6-6 10-8 14-6-2 6-2 10 0 16-4 2-8 0-14-6" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
        <circle cx="36" cy="45" r="3" fill="currentColor"/>
    </symbol>

    <symbol id="i-clock" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="34" fill="none" stroke="currentColor" stroke-width="3.2"/>
        <path d="M50 28v24l16 10" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"/>
    </symbol>
    <symbol id="i-phone" viewBox="0 0 100 100">
        <path d="M32 14c6-2 10 2 12 8l4 10c2 5 0 8-4 11l-4 3c4 10 12 18 22 22l3-4c3-4 6-6 11-4l10 4c6 2 10 6 8 12-2 8-10 14-20 12C48 82 18 52 12 30c-2-8 4-14 10-16Z"
              fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
    </symbol>
    <symbol id="i-mail" viewBox="0 0 100 100">
        <rect x="12" y="24" width="76" height="52" rx="4" fill="none" stroke="currentColor" stroke-width="3.2"/>
        <path d="M14 28l36 28 36-28" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
    </symbol>
    <symbol id="i-insta" viewBox="0 0 100 100">
        <rect x="16" y="16" width="68" height="68" rx="20" fill="none" stroke="currentColor" stroke-width="3.4"/>
        <circle cx="50" cy="50" r="16" fill="none" stroke="currentColor" stroke-width="3.4"/>
        <circle cx="70" cy="30" r="3.4" fill="currentColor"/>
    </symbol>
    <symbol id="i-tiktok" viewBox="0 0 100 100">
        <path d="M58 12v46a14 14 0 1 1-12-14" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round"/>
        <path d="M58 12c2 10 10 16 20 17" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round"/>
    </symbol>
    <symbol id="i-face" viewBox="0 0 100 100">
        <path d="M62 22h-8c-8 0-12 5-12 13v10H32v13h10v28h14V58h10l3-13H56V37c0-3 2-4 5-4h7Z"
              fill="none" stroke="currentColor" stroke-width="3.2" stroke-linejoin="round"/>
    </symbol>
    <symbol id="i-calendar" viewBox="0 0 100 100">
        <rect x="14" y="20" width="72" height="66" rx="4" fill="none" stroke="currentColor" stroke-width="3.2"/>
        <path d="M14 38h72M32 10v18M68 10v18" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"/>
        <g fill="currentColor"><circle cx="32" cy="54" r="3"/><circle cx="50" cy="54" r="3"/><circle cx="68" cy="54" r="3"/>
        <circle cx="32" cy="70" r="3"/><circle cx="50" cy="70" r="3"/></g>
    </symbol>
    <symbol id="i-pin-line" viewBox="0 0 100 120">
        <path d="M50 12c18 0 30 13 30 30 0 21-22 42-30 56-8-14-30-35-30-56 0-17 12-30 30-30Z" fill="none" stroke="currentColor" stroke-width="3.4"/>
        <circle cx="50" cy="42" r="11" fill="none" stroke="currentColor" stroke-width="3"/>
    </symbol>

    <!-- sceau rond « fatto a mano » : cercle de texte -->
    <symbol id="i-seal" viewBox="0 0 120 120">
        <circle cx="60" cy="60" r="56" fill="none" stroke="currentColor" stroke-width="2"/>
        <circle cx="60" cy="60" r="34" fill="none" stroke="currentColor" stroke-width="1.6"/>
        <path id="seal-path" d="M60 60m-45 0a45 45 0 1 1 90 0a45 45 0 1 1-90 0" fill="none"/>
        <g fill="currentColor">
            <path d="M60 40c6 0 10 5 10 11s-4 11-10 11-10-5-10-11 4-11 10-11Z" opacity=".9"/>
            <path d="M58 36c-1-4 1-6 4-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </g>
    </symbol>
</svg>
