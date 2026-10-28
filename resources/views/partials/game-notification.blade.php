{{--
    Disappearing in-game notification banner. Reusable: drop
    @include('game-api::partials.game-notification') into any page that extends
    game-api::layout (game.js dispatches the 'gameMasterNotification' DOM event).

    Shows a message at the top of the screen, auto-fades after 10s, or closes
    immediately via the icon button. Not persisted — one-time messages only.
--}}
<div id="game-notification"
     class="hidden opacity-0 transition-opacity duration-500 ease-in-out fixed top-0 left-0 right-0 z-50 mt-4 flex justify-center px-4 pointer-events-none">
    <div id="game-notification__box" class="pointer-events-auto flex w-full max-w-xl items-start gap-3 rounded-lg bg-main-dark px-4 py-3 text-alternative-accent shadow-lg border-2 border-alternative-accent">
        <span id="game-notification__text" class="fira-sans flex-1 text-sm md:text-base"></span>
        <button id="game-notification__close" type="button" aria-label="Close notification"
                class="shrink-0 text-alternative-accent hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</div>

<script>
    (function () {
        var banner = document.getElementById('game-notification');
        if (!banner) return;

        var box = document.getElementById('game-notification__box');
        var textEl = document.getElementById('game-notification__text');
        var closeEl = document.getElementById('game-notification__close');
        var hideTimer = null;
        var removeTimer = null;

        // Pre-set actions carry a colour (by importance level); apply it so the
        // message is shown in its level's tone. Custom messages reset to default.
        function applyColour(colour) {
            if (colour) {
                box.style.backgroundColor = colour;
                box.style.borderColor = colour;
                box.style.color = '#ffffff';
                closeEl.style.color = '#ffffff';
            } else {
                box.style.backgroundColor = '';
                box.style.borderColor = '';
                box.style.color = '';
                closeEl.style.color = '';
            }
        }

        function hide() {
            clearTimeout(hideTimer);
            banner.classList.add('opacity-0');
            banner.classList.remove('opacity-100');
            // Remove from layout only after the fade-out transition finishes.
            removeTimer = setTimeout(function () { banner.classList.add('hidden'); }, 500);
        }

        function show(from, message, colour) {
            clearTimeout(removeTimer);
            textEl.textContent = from + ': ' + message;
            applyColour(colour);
            banner.classList.remove('hidden');
            // Force reflow so the opacity transition runs after un-hiding.
            void banner.offsetWidth;
            banner.classList.remove('opacity-0');
            banner.classList.add('opacity-100');

            clearTimeout(hideTimer);
            hideTimer = setTimeout(hide, 10000);
        }

        document.addEventListener('gameMasterNotification', function (e) {
            var detail = e.detail || {};
            if (!detail.message) return;
            show(detail.from || 'Game Master', detail.message, detail.colour);
        });

        closeEl.addEventListener('click', hide);
    })();
</script>
