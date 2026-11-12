<?php
/** info.php — carte illustrée en SVG (aucune iframe), horaires live, contacts. */
$tz    = new DateTimeZone($config['contact']['timezone']);
$now   = new DateTimeImmutable('now', $tz);
$today = (int) $now->format('N');

$dayNames = [1 => ['Lun', 'Lun'], 2 => ['Mar', 'Tue'], 3 => ['Mer', 'Wed'], 4 => ['Gio', 'Thu'], 5 => ['Ven', 'Fri'], 6 => ['Sab', 'Sat'], 7 => ['Dom', 'Sun']];
$formatRanges = static function (array $ranges): string {
    if ($ranges === []) {
        return '';
    }
    return implode(' · ', array_map(static fn (array $r): string => $r[0] . '–' . $r[1], $ranges));
};
?>
<section class="info" id="dove" aria-labelledby="info-title">
    <?php wave('var(--limone)', true, 'wave--top'); ?>

    <div class="wrap">
        <div class="info__head">
            <p class="label" data-reveal="up"><?= t($config, 'info.kicker') ?></p>
            <h2 class="h2" id="info-title" data-reveal="up">
                <?= t($config, 'info.title1') ?> <span class="ital"><?= t($config, 'info.title2') ?></span>
            </h2>
        </div>

        <div class="info__grid">
            <!-- carte illustrée -->
            <div class="info__map" data-reveal="up">
                <svg viewBox="0 0 600 480" role="img" aria-labelledby="map-title">
                    <title id="map-title"><?= t($config, 'info.map_alt') ?></title>
                    <rect width="600" height="480" fill="#F7E8C6" rx="10"/>
                    <!-- terre -->
                    <path d="M0 0h600v250c-40 12-70 40-110 44-44 4-60 30-104 34-48 4-64 26-108 22-46-4-70-28-116-24-52 4-86 26-162 20Z" fill="var(--calce)"/>
                    <!-- mer -->
                    <path d="M0 250c76 6 110-16 162-20 46-4 70 20 116 24 44 4 60-18 108-22 44-4 60-30 104-34 40-4 70-32 110-44v326H0Z" fill="var(--cielo)"/>
                    <path d="M0 268c76 6 110-16 162-20 46-4 70 20 116 24 44 4 60-18 108-22 44-4 60-30 104-34 40-4 70-32 110-44" fill="none" stroke="var(--maiolica)" stroke-width="4" opacity=".5"/>
                    <!-- vagues -->
                    <g fill="none" stroke="var(--maiolica)" stroke-width="2.5" opacity=".35">
                        <path d="M60 330c14 0 20-8 34-8s20 8 34 8 20-8 34-8"/>
                        <path d="M380 372c14 0 20-8 34-8s20 8 34 8 20-8 34-8"/>
                        <path d="M200 410c14 0 20-8 34-8s20 8 34 8 20-8 34-8"/>
                    </g>
                    <!-- rues -->
                    <g stroke="var(--maiolica)" stroke-width="5" fill="none" stroke-linecap="round">
                        <path d="M-10 180c90 10 140-10 220 0s150-26 220-14 120 30 190 22"/>
                        <path d="M300 -10v180M180 40l40 140M440 20l-30 160M120 250l-20-90M500 250l20-80"/>
                    </g>
                    <g stroke="var(--maiolica)" stroke-width="2" fill="none" opacity=".45">
                        <path d="M0 110h600M0 220h600M60 0v250M540 0v250M240 0v220"/>
                    </g>
                    <!-- parc des citronniers -->
                    <g fill="var(--foglia)" opacity=".8">
                        <circle cx="120" cy="120" r="20"/><circle cx="150" cy="136" r="15"/><circle cx="96" cy="140" r="13"/>
                        <circle cx="470" cy="150" r="18"/><circle cx="500" cy="136" r="14"/>
                    </g>
                    <!-- bouches de métro / repères -->
                    <g fill="var(--notte)" opacity=".55">
                        <circle cx="230" cy="76" r="5"/><circle cx="360" cy="150" r="5"/><circle cx="452" cy="52" r="5"/>
                    </g>
                    <text x="34" y="60" class="info__maplabel">PARMA</text>
                    <text x="380" y="300" class="info__maplabel info__maplabel--sea">MARE</text>
                    <!-- pin citron qui oscille -->
                    <g class="info__pin" id="info-pin" transform="translate(300 196)">
                        <circle r="42" fill="var(--limone)" opacity=".35"/>
                        <g transform="translate(-24 -58) scale(.48)">
                            <use href="#i-pin"/>
                        </g>
                        <text y="34" text-anchor="middle" class="info__pinlabel"><?= e($brand['name']) ?></text>
                    </g>
                </svg>
                <a class="btn btn--outline btn--sm info__maplink" href="<?= e($config['contact']['maps']) ?>" target="_blank" rel="noopener">
                    <?= t($config, 'info.take_me') ?>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                </a>
            </div>

            <!-- coordonnées -->
            <div class="info__body">
                <div class="info__block" data-reveal="up">
                    <h3 class="info__subtitle"><?= t($config, 'info.where') ?></h3>
                    <address class="info__address">
                        <?= e($config['contact']['street']) ?><br>
                        <?= e($config['contact']['cap']) ?> <?= e($config['contact']['city']) ?> (<?= e($config['contact']['province']) ?>)
                    </address>
                </div>

                <div class="info__block" data-reveal="up">
                    <h3 class="info__subtitle">
                        <?= t($config, 'info.when') ?>
                        <span class="status-pill status-pill--inline" data-open-inline>
                            <span class="status-pill__dot" aria-hidden="true"></span>
                            <span data-open-text><?= t($config, 'nav.open') ?></span>
                        </span>
                    </h3>
                    <table class="hours">
                        <caption class="visually-hidden"><?= t($config, 'info.when') ?></caption>
                        <tbody>
                        <?php foreach ($config['hours'] as $day => $ranges): ?>
                            <tr class="<?= $day === $today ? 'is-today' : '' ?>" <?= $day === $today ? 'aria-current="date"' : '' ?>>
                                <th scope="row"><?= e($dayNames[$day][0]) ?><?= $day === $today ? ' <span class="hours__today">· ' . t($config, 'info.today') . '</span>' : '' ?></th>
                                <td><?= $ranges === [] ? t($config, 'info.closed_day') : e($formatRanges($ranges)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="info__block" data-reveal="up">
                    <h3 class="info__subtitle"><?= t($config, 'info.contacts') ?></h3>
                    <ul class="info__contacts">
                        <li>
                            <svg viewBox="0 0 100 100" aria-hidden="true"><use href="#i-phone"/></svg>
                            <a href="tel:<?= e($config['contact']['phone_raw']) ?>"><?= e($config['contact']['phone']) ?></a>
                        </li>
                        <li>
                            <svg viewBox="0 0 100 100" aria-hidden="true"><use href="#i-mail"/></svg>
                            <a href="mailto:<?= e($config['contact']['email']) ?>"><?= e($config['contact']['email']) ?></a>
                        </li>
                        <li>
                            <svg viewBox="0 0 100 100" aria-hidden="true"><use href="#i-insta"/></svg>
                            <a href="<?= e($config['socials'][0]['url']) ?>" target="_blank" rel="noopener"><?= e($config['contact']['handle']) ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
