{{--
    Online-users counter. Self-contained and reusable: drop
    @include('game-api::partials.online-users') anywhere inside a page that
    extends game-api::layout (socket.io client + window.socketServer are set up
    there). Renders a small fixed badge pinned to the bottom of the page.
--}}
<div class="fixed bottom-0 left-0 right-0 z-50 mb-4 flex justify-center pointer-events-none">
    <div class="pointer-events-auto flex items-center gap-2 rounded-full bg-main-dark px-4 py-2 text-sm text-alternative-accent shadow-lg ring-1 ring-alternative-accent">
        <span class="relative flex h-2.5 w-2.5">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span>
        </span>
        <span class="fira-sans"><span data-online-users-count>–</span> online</span>
    </div>
</div>

<script>
    (function () {
        function applyCount(count) {
            document.querySelectorAll('[data-online-users-count]').forEach(function (el) {
                el.textContent = count;
            });
        }

        function bind(socket) {
            if (!socket) return;
            socket.on('onlineUsersCount', applyCount);
            var request = function () { socket.emit('getOnlineUsersCount', {}, applyCount); };
            if (socket.connected) request();
            socket.on('connect', request);
        }

        // Reuse the page's existing socket (created by game.js) so we don't open
        // a second connection that would inflate the count; fall back to our own.
        if (window.socket) {
            bind(window.socket);
        } else if (window.io) {
            bind(io(window.socketServer));
        }
    })();
</script>
