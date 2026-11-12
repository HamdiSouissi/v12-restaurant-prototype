<?php
/** preloader.php — citron qui roule, vague bleue qui monte et se retire. Une fois par session. */
?>
<div class="preloader" id="preloader" role="status" aria-live="polite" aria-label="<?= e($config['i18n']['it']['preloader']) ?>">
    <div class="preloader__inner">
        <svg class="preloader__scene" viewBox="0 0 320 160" aria-hidden="true">
            <path class="preloader__horizon" d="M20 108h280" fill="none" stroke="var(--maiolica)" stroke-width="2.5" stroke-linecap="round"/>
            <g class="preloader__lemon">
                <g transform="translate(-24 -24) scale(.48)">
                    <use href="#i-lemon-slice"/>
                </g>
            </g>
            <path class="preloader__wave preloader__wave--back" d="M-40 108c40 0 60-22 100-22s60 22 100 22 60-22 100-22 60 22 100 22v90H-40Z" fill="#1B4B9B" opacity=".35"/>
            <path class="preloader__wave preloader__wave--front" d="M-40 120c40 0 60-26 100-26s60 26 100 26 60-26 100-26 60 26 100 26v90H-40Z" fill="#1B4B9B"/>
        </svg>
        <p class="preloader__label">
            <span><?= t($config, 'preloader') ?></span>
            <span class="preloader__pct" data-preloader-pct>0%</span>
        </p>
        <div class="preloader__bar" aria-hidden="true"><span data-preloader-bar></span></div>
    </div>
</div>
