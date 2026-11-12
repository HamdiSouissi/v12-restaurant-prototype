<?php
/** booking.php — stepper de citrons, vues en arche, calendrier de carreaux, créneaux, cartolina. */
$booking = $config['booking'];
$today   = new DateTimeImmutable('now', new DateTimeZone($config['contact']['timezone']));
?>
<section class="booking" id="prenota" aria-labelledby="booking-title">
    <?php wave('var(--calce)', true, 'wave--top'); ?>

    <div class="wrap">
        <div class="booking__head">
            <p class="label" data-reveal="up"><?= t($config, 'book.kicker') ?></p>
            <h2 class="h2" id="booking-title" data-reveal="up">
                <?= t($config, 'book.title1') ?> <span class="ital"><?= t($config, 'book.title2') ?></span>
            </h2>
            <p class="booking__sub" data-reveal="up"><?= t($config, 'book.sub') ?></p>
        </div>

        <div class="booking__grid">
            <form class="booking__form" id="booking-form" novalidate data-reveal="up">

                <!-- 1. couverts : stepper de citrons dans une cassette -->
                <fieldset class="book-step" data-book-step="guests">
                    <legend class="book-step__legend"><span class="book-step__num">01</span><?= t($config, 'book.guests') ?></legend>
                    <div class="guests" data-guests>
                        <button class="round-btn round-btn--sm" type="button" data-guests-down aria-label="–">−</button>
                        <div class="guests__display">
                            <div class="guests__crate" aria-hidden="true">
                                <svg viewBox="0 0 100 100" class="guests__crate-svg"><use href="#i-crate"/></svg>
                                <div class="guests__lemons" data-guests-lemons></div>
                            </div>
                            <p class="guests__count">
                                <span data-guests-count aria-live="polite">2</span>
                                <small><?= t($config, 'book.guests_unit') ?></small>
                            </p>
                        </div>
                        <button class="round-btn round-btn--sm" type="button" data-guests-up aria-label="+">+</button>
                    </div>
                    <p class="guests__many" hidden data-guests-many>
                        <?= t($config, 'book.many') ?>
                        <a href="tel:<?= e($config['contact']['phone_raw']) ?>"><?= e($config['contact']['phone']) ?></a>
                    </p>
                </fieldset>

                <!-- 2. la vue -->
                <fieldset class="book-step" data-book-step="view">
                    <legend class="book-step__legend"><span class="book-step__num">02</span><?= t($config, 'book.where') ?></legend>
                    <div class="views">
                        <?php foreach ($booking['views'] as $i => $view): ?>
                        <label class="view-card">
                            <input type="radio" name="view" value="<?= e($view['key']) ?>" <?= $i === 0 ? 'checked' : '' ?>>
                            <span class="view-card__inner">
                                <span class="view-card__media">
                                    <picture>
                                        <source srcset="<?= img($view['img']) ?>.webp" type="image/webp">
                                        <img src="<?= img($view['img']) ?>.jpg" alt="" width="1000" height="1300" loading="lazy">
                                    </picture>
                                </span>
                                <span class="view-card__label"><?= bi($view['name']) ?></span>
                                <span class="view-card__desc"><?= bi($view['desc']) ?></span>
                                <span class="view-card__check" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><use href="#i-check"/></svg>
                                </span>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <!-- 3. le jour : calendrier de carreaux -->
                <fieldset class="book-step" data-book-step="date">
                    <legend class="book-step__legend"><span class="book-step__num">03</span><?= t($config, 'book.when') ?></legend>
                    <div class="calendar" data-calendar data-today="<?= e($today->format('Y-m-d')) ?>">
                        <div class="calendar__head">
                            <button class="round-btn round-btn--sm" type="button" data-cal-prev aria-label="<?= t($config, 'book.prev_month') ?>">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-chevron-left"/></svg>
                            </button>
                            <p class="calendar__month" data-cal-month aria-live="polite"></p>
                            <button class="round-btn round-btn--sm" type="button" data-cal-next aria-label="<?= t($config, 'book.next_month') ?>">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                            </button>
                        </div>
                        <div class="calendar__weekdays" aria-hidden="true"></div>
                        <div class="calendar__grid" role="grid" data-cal-grid></div>
                        <p class="calendar__legend">
                            <span><i class="dot dot--closed" aria-hidden="true"></i><?= t($config, 'book.closed') ?></span>
                            <span><i class="dot dot--sun" aria-hidden="true"></i><?= t($config, 'book.sunset') ?></span>
                        </p>
                    </div>
                </fieldset>

                <!-- 4. les créneaux -->
                <fieldset class="book-step" data-book-step="slots">
                    <legend class="book-step__legend"><span class="book-step__num">04</span><?= t($config, 'book.time') ?></legend>
                    <div class="slots" data-slots aria-live="polite">
                        <p class="slots__placeholder"><?= t($config, 'book.summary_empty') ?></p>
                    </div>
                </fieldset>

                <!-- 5. coordonnées -->
                <fieldset class="book-step" data-book-step="details">
                    <legend class="book-step__legend"><span class="book-step__num">05</span><?= t($config, 'book.details') ?></legend>
                    <div class="form-grid">
                        <label class="field">
                            <span class="field__label"><?= t($config, 'book.name') ?></span>
                            <input type="text" name="name" autocomplete="name" required data-validate="text">
                            <small class="field__error" data-error-for="name"></small>
                        </label>
                        <label class="field">
                            <span class="field__label"><?= t($config, 'book.phone') ?></span>
                            <input type="tel" name="phone" autocomplete="tel" required data-validate="phone" inputmode="tel">
                            <small class="field__error" data-error-for="phone"></small>
                        </label>
                        <label class="field">
                            <span class="field__label"><?= t($config, 'book.email') ?></span>
                            <input type="email" name="email" autocomplete="email" required data-validate="email">
                            <small class="field__error" data-error-for="email"></small>
                        </label>
                        <label class="field">
                            <span class="field__label"><?= t($config, 'book.occasion') ?></span>
                            <select name="occasion">
                                <?php foreach ($booking['occasions'] as $key => $label): ?>
                                <option value="<?= e($key) ?>"><?= bi($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="field field--full">
                            <span class="field__label"><?= t($config, 'book.notes') ?></span>
                            <textarea name="notes" rows="2" maxlength="400"></textarea>
                        </label>
                        <label class="check field--full">
                            <input type="checkbox" name="consent" required data-validate="consent">
                            <span class="check__box" aria-hidden="true"></span>
                            <span><?= t($config, 'book.consent') ?></span>
                            <small class="field__error" data-error-for="consent"></small>
                        </label>
                    </div>
                </fieldset>

                <p class="booking__summary" data-booking-summary aria-live="polite">
                    <span data-summary-text><?= t($config, 'book.summary_empty') ?></span>
                </p>

                <button class="btn btn--limone btn--lg btn--block" type="submit" id="booking-submit">
                    <?= t($config, 'book.cta') ?>
                </button>
                <p class="form-error" data-booking-error role="alert" hidden></p>
            </form>

            <!-- Carte postale de confirmation -->
            <div class="postcard" id="booking-postcard" hidden aria-live="polite">
                <div class="postcard__inner">
                    <div class="postcard__front">
                        <svg viewBox="0 0 400 260" class="postcard__art" aria-hidden="true">
                            <rect width="400" height="260" fill="var(--cielo)"/>
                            <path d="M0 190c60 0 90-36 140-36s80 36 140 36 90-26 120-26v96H0Z" fill="var(--maiolica)"/>
                            <path d="M0 210c60 0 90-30 140-30s80 30 140 30 90-22 120-22v72H0Z" fill="#123A78" opacity=".7"/>
                            <path d="M0 150c40-18 80-52 120-52 30 0 44 16 70 16 34 0 50-30 90-30 40 0 60 26 120 34v60H0Z" fill="var(--calce)"/>
                            <g stroke="var(--notte)" stroke-width="2.5" fill="none" opacity=".5">
                                <path d="M60 108v-16M76 108v-22M92 108v-14"/>
                            </g>
                            <circle cx="320" cy="52" r="24" fill="var(--limone)" stroke="var(--notte)" stroke-width="3"/>
                            <g transform="translate(180 168) scale(.7)" style="color:var(--notte)">
                                <use href="#i-gozzo" width="120" height="80"/>
                            </g>
                        </svg>
                        <p class="postcard__caption">Cucina di Costiera · Parma</p>
                    </div>
                    <div class="postcard__back">
                        <div class="postcard__stamp" aria-hidden="true">
                            <svg viewBox="0 0 100 100"><use href="#i-lemon-solid"/></svg>
                            <span>ITALIA</span>
                            <b>€ 1,10</b>
                        </div>
                        <div class="postcard__postmark" aria-hidden="true">
                            <svg viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="46" fill="none" stroke="currentColor" stroke-width="2.5"/>
                                <circle cx="60" cy="60" r="36" fill="none" stroke="currentColor" stroke-width="1.4"/>
                                <text class="postcard__postmark-text"><textPath href="#seal-path" startOffset="0">COSTIERA · COSTIERA · </textPath></text>
                            </svg>
                        </div>
                        <p class="postcard__ok"><?= t($config, 'book.done_title') ?></p>
                        <p class="postcard__code"><?= t($config, 'book.code') ?> <b data-booking-code>LMN-T-0000</b></p>
                        <dl class="postcard__recap" data-booking-recap></dl>
                        <div class="postcard__qr" data-booking-qr aria-hidden="true"></div>
                        <div class="postcard__actions">
                            <a class="btn btn--sm btn--outline" href="#" data-ics download="limonaia-prenotazione.ics"><?= t($config, 'book.ics') ?></a>
                            <button class="btn btn--sm btn--limone" type="button" data-book-again><?= t($config, 'book.again') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
