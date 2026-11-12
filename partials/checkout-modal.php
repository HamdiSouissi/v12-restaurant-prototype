<?php
/** checkout-modal.php — 3 étapes, billet de bord perforé, tampon PAGATO, suivi gozzo. */
?>
<div class="checkout" id="checkout" role="dialog" aria-modal="true" aria-labelledby="checkout-title" hidden>
    <div class="checkout__tiles" aria-hidden="true"></div>
    <div class="checkout__panel">
        <header class="checkout__head">
            <p class="label"><?= t($config, 'checkout.title') ?></p>
            <h2 class="checkout__title" id="checkout-title"><?= t($config, 'checkout.step1') ?></h2>
            <button class="round-btn round-btn--sm" type="button" data-close-checkout aria-label="<?= t($config, 'checkout.close') ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-close"/></svg>
            </button>
        </header>

        <ol class="checkout__steps" aria-label="<?= t($config, 'checkout.title') ?>">
            <li class="is-active" data-step-dot="1"><span>1</span><?= t($config, 'checkout.step1') ?></li>
            <li data-step-dot="2"><span>2</span><?= t($config, 'checkout.step2') ?></li>
            <li data-step-dot="3"><span>3</span><?= t($config, 'checkout.step3') ?></li>
        </ol>

        <form class="checkout__form" id="checkout-form" novalidate>
            <!-- étape 1 -->
            <div class="checkout__step is-active" data-step="1">
                <div class="form-grid">
                    <label class="field">
                        <span class="field__label"><?= t($config, 'checkout.name') ?></span>
                        <input type="text" name="name" autocomplete="name" required data-validate="text">
                        <small class="field__error" data-error-for="name"></small>
                    </label>
                    <label class="field">
                        <span class="field__label"><?= t($config, 'checkout.phone') ?></span>
                        <input type="tel" name="phone" autocomplete="tel" required data-validate="phone" inputmode="tel">
                        <small class="field__error" data-error-for="phone"></small>
                    </label>
                    <label class="field field--full">
                        <span class="field__label"><?= t($config, 'checkout.email') ?></span>
                        <input type="email" name="email" autocomplete="email" required data-validate="email">
                        <small class="field__error" data-error-for="email"></small>
                    </label>

                    <div class="field field--full" data-delivery-only>
                        <label class="field">
                            <span class="field__label"><?= t($config, 'checkout.address') ?></span>
                            <input type="text" name="address" autocomplete="street-address" data-validate="address">
                            <small class="field__error" data-error-for="address"></small>
                        </label>
                    </div>

                    <label class="field field--full" data-delivery-only>
                        <span class="field__label"><?= t($config, 'checkout.notes') ?></span>
                        <textarea name="notes" rows="2" maxlength="300"></textarea>
                    </label>

                    <div class="field field--full" data-pickup-only hidden>
                        <span class="field__label"><?= t($config, 'checkout.pickup_time') ?></span>
                        <div class="pickup-times" data-pickup-times></div>
                    </div>
                </div>
            </div>

            <!-- étape 2 -->
            <div class="checkout__step" data-step="2">
                <div class="pay-options" role="radiogroup" aria-label="<?= t($config, 'checkout.step2') ?>">
                    <label class="pay-opt">
                        <input type="radio" name="payment" value="carta" checked>
                        <span class="pay-opt__box">
                            <svg viewBox="0 0 100 100" aria-hidden="true">
                                <rect x="10" y="26" width="80" height="50" rx="6" fill="none" stroke="currentColor" stroke-width="3.2"/>
                                <path d="M10 42h80" stroke="currentColor" stroke-width="3.2"/>
                                <path d="M22 62h18" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"/>
                            </svg>
                            <?= t($config, 'checkout.pay_card') ?>
                        </span>
                    </label>
                    <label class="pay-opt">
                        <input type="radio" name="payment" value="satispay">
                        <span class="pay-opt__box">
                            <svg viewBox="0 0 100 100" aria-hidden="true">
                                <rect x="12" y="12" width="76" height="76" rx="20" fill="none" stroke="currentColor" stroke-width="3.2"/>
                                <path d="M34 62c6 6 24 6 30 0 5-5-2-10-12-12s-18-6-12-12c5-5 20-6 26 0" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"/>
                            </svg>
                            <?= t($config, 'checkout.pay_satispay') ?>
                        </span>
                    </label>
                    <label class="pay-opt">
                        <input type="radio" name="payment" value="contanti">
                        <span class="pay-opt__box">
                            <svg viewBox="0 0 100 100" aria-hidden="true">
                                <rect x="8" y="30" width="84" height="40" rx="5" fill="none" stroke="currentColor" stroke-width="3.2"/>
                                <circle cx="50" cy="50" r="11" fill="none" stroke="currentColor" stroke-width="3.2"/>
                                <path d="M20 50h4M76 50h4" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"/>
                            </svg>
                            <?= t($config, 'checkout.pay_cash') ?>
                        </span>
                    </label>
                </div>

                <div class="card-fields" data-card-fields>
                    <label class="field">
                        <span class="field__label"><?= t($config, 'checkout.card_number') ?></span>
                        <input type="text" name="card" inputmode="numeric" maxlength="19" placeholder="4242 4242 4242 4242">
                    </label>
                    <label class="field">
                        <span class="field__label"><?= t($config, 'checkout.card_exp') ?></span>
                        <input type="text" name="exp" inputmode="numeric" maxlength="5" placeholder="MM/AA">
                    </label>
                    <label class="field">
                        <span class="field__label"><?= t($config, 'checkout.card_cvc') ?></span>
                        <input type="text" name="cvc" inputmode="numeric" maxlength="4" placeholder="123">
                    </label>
                </div>

                <p class="checkout__note">
                    <svg viewBox="0 0 100 100" aria-hidden="true"><use href="#i-clock"/></svg>
                    <?= t($config, 'checkout.pay_note') ?>
                </p>
            </div>

            <!-- étape 3 : récap avant envoi -->
            <div class="checkout__step" data-step="3">
                <ul class="checkout__recap" data-checkout-recap></ul>
                <dl class="cart-totals cart-totals--checkout">
                    <div><dt><?= t($config, 'cart.subtotal') ?></dt><dd data-checkout-subtotal>€ 0</dd></div>
                    <div><dt><?= t($config, 'cart.fee') ?></dt><dd data-checkout-fee>€ 3</dd></div>
                    <div class="cart-totals__total"><dt><?= t($config, 'cart.total') ?></dt><dd data-checkout-total>€ 0</dd></div>
                </dl>
            </div>

            <p class="form-error" data-checkout-error role="alert" hidden></p>

            <div class="checkout__actions">
                <button class="btn btn--ghost btn--sm" type="button" data-checkout-back hidden><?= t($config, 'checkout.back') ?></button>
                <button class="btn btn--limone btn--lg" type="button" data-checkout-next><?= t($config, 'checkout.next') ?></button>
                <button class="btn btn--limone btn--lg" type="submit" data-checkout-submit hidden><?= t($config, 'checkout.submit') ?></button>
            </div>
        </form>

        <!-- Biglietto di bordo -->
        <div class="ticket" id="ticket" hidden aria-live="polite">
            <div class="ticket__head">
                <p class="ticket__brand"><?= e($brand['name']) ?> <span>· <?= e($brand['subtitle']) ?></span></p>
                <p class="ticket__kind"><?= t($config, 'checkout.ticket') ?></p>
            </div>
            <div class="ticket__perf" aria-hidden="true"></div>
            <div class="ticket__body">
                <p class="ticket__order"><?= t($config, 'checkout.order_no') ?> <b data-ticket-code>LMN-0000</b></p>
                <ul class="ticket__items" data-ticket-items></ul>
                <dl class="ticket__totals" data-ticket-totals></dl>
                <div class="ticket__barcode" data-ticket-barcode aria-hidden="true"></div>
                <p class="ticket__eta"><?= t($config, 'checkout.eta') ?> <b data-ticket-eta></b></p>
            </div>
            <div class="ticket__stamp" aria-hidden="true">
                <svg viewBox="0 0 140 140">
                    <circle cx="70" cy="70" r="60" fill="none" stroke="currentColor" stroke-width="4"/>
                    <circle cx="70" cy="70" r="50" fill="none" stroke="currentColor" stroke-width="1.6"/>
                    <text x="70" y="80" text-anchor="middle" class="ticket__stamp-text"><?= strtoupper(t($config, 'checkout.paid')) ?></text>
                </svg>
            </div>

            <div class="tracker" data-tracker>
                <div class="tracker__path" aria-hidden="true">
                    <svg viewBox="0 0 400 60" preserveAspectRatio="none">
                        <path d="M10 40c40 0 60-18 100-18s60 18 100 18 60-18 100-18 40 10 80 10" fill="none" stroke="var(--maiolica)" stroke-width="2.5" stroke-dasharray="6 7"/>
                    </svg>
                    <span class="tracker__boat" data-tracker-boat>
                        <svg viewBox="0 0 120 80"><use href="#i-gozzo"/></svg>
                    </span>
                </div>
                <ol class="tracker__ports">
                    <li class="is-done"><?= t($config, 'checkout.track_received') ?></li>
                    <li><?= t($config, 'checkout.track_kitchen') ?></li>
                    <li><?= t($config, 'checkout.track_packed') ?></li>
                    <li><?= t($config, 'checkout.track_route') ?></li>
                </ol>
            </div>

            <button class="btn btn--outline btn--block" type="button" data-new-order><?= t($config, 'checkout.new_order') ?></button>
        </div>
    </div>
</div>
