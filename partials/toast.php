<?php
/** toast.php — zone de notifications empilables, aria-live polite. */
?>
<div class="toast-zone" id="toast-zone" aria-live="polite" aria-atomic="false"></div>

<template id="toast-template">
    <div class="toast" role="status">
        <svg class="toast__lemon" viewBox="0 0 100 100" aria-hidden="true"><use href="#i-lemon-solid"/></svg>
        <p class="toast__text"></p>
        <button class="toast__action" type="button" hidden></button>
        <button class="toast__close" type="button" aria-label="Chiudi">
            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-close"/></svg>
        </button>
    </div>
</template>
