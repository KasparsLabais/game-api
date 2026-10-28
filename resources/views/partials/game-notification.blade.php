{{--
    Disappearing in-game notifications (toasts). Reusable: drop
    @include('game-api::partials.game-notification') into any page that extends
    game-api::layout (game.js dispatches the 'gameMasterNotification' DOM event).

    Toasts slide in at the bottom-right corner, stack (newest at the bottom,
    max 3), auto-dismiss after 10s or close via the button. Coloured by the
    message's level: info / warning / fatal / custom. Not persisted.
--}}
<style>
    .game-toast { animation: game-toast-in 0.35s cubic-bezier(.2, 1.3, .4, 1) both; }
    .game-toast.is-leaving { animation: game-toast-out 0.3s ease-in both; }
    .game-toast__timer { animation: game-toast-timer linear both; }
    @keyframes game-toast-in { from { opacity: 0; transform: translateX(120%); } to { opacity: 1; transform: translateX(0); } }
    @keyframes game-toast-out { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(120%); } }
    @keyframes game-toast-timer { from { width: 100%; } to { width: 0%; } }
    @media (prefers-reduced-motion: reduce) {
        .game-toast, .game-toast.is-leaving { animation: none; }
    }
</style>

<div id="game-notification"
     class="fixed bottom-4 right-4 left-4 sm:left-auto sm:w-96 z-50 flex flex-col gap-4 pointer-events-none"
     aria-live="polite"></div>

<script>
    (function () {
        var stack = document.getElementById('game-notification');
        if (!stack) return;

        var DURATION = 10000;
        var MAX = 3;

        // Level -> look. `strip` is the coloured title bar, `label` its text.
        var LEVELS = {
            info:    { strip: 'bg-brand text-white',        label: 'Info' },
            warning: { strip: 'bg-pop text-main-dark',      label: 'Heads up' },
            fatal:   { strip: 'bg-main-accent text-white',  label: 'Urgent' },
            custom:  { strip: 'bg-main-dark text-white',    label: 'Message' }
        };

        function dismiss(toast) {
            if (!toast || toast.classList.contains('is-leaving')) return;
            clearTimeout(toast._timer);
            toast.classList.add('is-leaving');
            setTimeout(function () { toast.remove(); }, 300);
        }

        function show(from, message, level, colour) {
            var look = LEVELS[level] || LEVELS.custom;

            var toast = document.createElement('div');
            toast.className = 'game-toast pointer-events-auto w-full bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] overflow-hidden';
            toast.setAttribute('role', level === 'fatal' ? 'alert' : 'status');

            // Title strip: level label + sender + close button.
            var strip = document.createElement('div');
            strip.className = 'flex items-center gap-2 px-3 py-2 border-b-4 border-main-dark ' + look.strip;
            // Unknown level but a colour was sent (e.g. older pre-set actions): use it.
            if (!LEVELS[level] && colour) {
                strip.style.backgroundColor = colour;
                strip.style.color = '#ffffff';
            }
            var label = document.createElement('span');
            label.className = 'russo uppercase tracking-widest text-xs shrink-0';
            label.textContent = look.label;
            var sender = document.createElement('span');
            sender.className = 'fira-sans text-xs font-bold opacity-80 truncate';
            sender.textContent = from;
            var close = document.createElement('button');
            close.type = 'button';
            close.setAttribute('aria-label', 'Close notification');
            close.className = 'ml-auto shrink-0 w-6 h-6 flex items-center justify-center border-2 border-current hover:bg-white/20';
            close.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>';
            close.addEventListener('click', function () { dismiss(toast); });
            strip.append(label, sender, close);

            var body = document.createElement('p');
            body.className = 'fira-sans text-main-dark font-bold text-base md:text-lg px-4 py-3 break-words';
            body.textContent = message;

            // Countdown bar until auto-dismiss.
            var timer = document.createElement('div');
            timer.className = 'game-toast__timer h-1.5 bg-main-dark';
            timer.style.animationDuration = DURATION + 'ms';

            toast.append(strip, body, timer);
            stack.appendChild(toast);

            var live = stack.querySelectorAll('.game-toast:not(.is-leaving)');
            if (live.length > MAX) dismiss(live[0]);

            toast._timer = setTimeout(function () { dismiss(toast); }, DURATION);
        }

        document.addEventListener('gameMasterNotification', function (e) {
            var detail = e.detail || {};
            if (!detail.message) return;
            show(detail.from || 'Game Master', detail.message, detail.level, detail.colour);
        });
    })();
</script>
