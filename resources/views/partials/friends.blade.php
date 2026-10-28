{{--
    Friends panel + private messaging. Self-contained: include
    @include('game-api::partials.friends') from a page that extends
    game-api::layout (it provides socket.io, window.socket from game.js, the CSRF
    meta tag and window.id/username for the authenticated user).

    - Floating button (bottom-right) shows how many friends are online.
    - Click to open a panel listing friends (online first) + an "Add friend"
      search. Click a friend to open a chat window and exchange private messages.
    - Delivery is real-time via the node; every message is persisted by the
      Laravel app first, so history survives a node restart.
--}}
@auth
<div id="friends-widget" class="fixed bottom-4 right-4 z-50 fira-sans" data-me="{{ Auth::id() }}">

    {{-- Floating toggle button --}}
    <button id="friends-toggle" type="button"
            class="relative flex items-center gap-2 rounded-full bg-main-dark px-4 py-2 text-sm text-alternative-accent shadow-lg ring-1 ring-alternative-accent hover:ring-main-accent transition">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z"/>
            <path d="M5.082 14.254a8.287 8.287 0 0 0-1.308 5.135 9.687 9.687 0 0 1-1.764-.44l-.115-.04a.563.563 0 0 1-.373-.487l-.01-.121a3.75 3.75 0 0 1 3.57-4.047ZM20.226 19.389a8.287 8.287 0 0 0-1.308-5.135 3.75 3.75 0 0 1 3.57 4.047l-.01.121a.563.563 0 0 1-.373.486l-.115.04c-.567.2-1.156.349-1.764.441Z"/>
        </svg>
        <span><span id="friends-online-count">0</span> friends online</span>
        <span id="friends-unread-dot" class="hidden absolute -top-1 -right-1 flex h-3 w-3">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-500 opacity-75"></span>
            <span class="relative inline-flex h-3 w-3 rounded-full bg-rose-600"></span>
        </span>
    </button>

    {{-- Panel --}}
    <div id="friends-panel" class="hidden absolute bottom-14 right-0 w-80 max-w-[90vw] rounded-lg bg-main-light shadow-2xl ring-1 ring-black/10 overflow-hidden">

        {{-- LIST VIEW --}}
        <div id="friends-list-view">
            <div class="flex items-center justify-between bg-main-dark px-4 py-3">
                <h3 class="text-alternative-accent font-semibold">Friends</h3>
                <button id="friends-add-toggle" type="button" class="text-xs font-semibold text-main-dark bg-main-accent rounded px-2 py-1 hover:opacity-90">+ Add</button>
            </div>

            {{-- Add-friend search (hidden by default) --}}
            <div id="friends-add-box" class="hidden border-b border-slate-300 px-3 py-2 bg-white">
                <div class="flex gap-2">
                    <input id="friends-search-input" type="text" placeholder="Search username…"
                           class="flex-1 border border-slate-300 rounded px-2 py-1 text-sm">
                </div>
                <div id="friends-search-results" class="mt-2 flex flex-col gap-1 max-h-40 overflow-y-auto"></div>
            </div>

            <div id="friends-list" class="max-h-80 overflow-y-auto divide-y divide-slate-200">
                <p id="friends-empty" class="text-slate-500 text-sm text-center py-6 px-4">No friends yet. Use “+ Add” to find people.</p>
            </div>
        </div>

        {{-- CHAT VIEW --}}
        <div id="friends-chat-view" class="hidden flex flex-col h-96">
            <div class="flex items-center gap-2 bg-main-dark px-3 py-2">
                <button id="friends-chat-back" type="button" class="text-alternative-accent hover:text-main-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <img id="friends-chat-avatar" src="/images/default-avatar.jpg" class="w-8 h-8 rounded-full border border-slate-500 object-cover">
                <div class="flex flex-col leading-tight">
                    <span id="friends-chat-name" class="text-alternative-accent text-sm font-semibold">—</span>
                    <span id="friends-chat-status" class="text-xs text-slate-400">offline</span>
                </div>
            </div>
            <div id="friends-chat-messages" class="flex-1 overflow-y-auto px-3 py-3 bg-white flex flex-col gap-2"></div>
            <div class="flex gap-2 border-t border-slate-300 p-2 bg-main-light">
                <input id="friends-chat-input" type="text" maxlength="2000" placeholder="Type a message…"
                       class="flex-1 border border-slate-300 rounded px-3 py-2 text-sm" autocomplete="off">
                <button id="friends-chat-send" type="button" class="bg-main-accent text-white rounded px-4 py-2 text-sm font-semibold hover:opacity-90">Send</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // Authenticated users only (guests have no friends).
    if (window.playerType !== 'player' || !window.id) return;

    var root = document.getElementById('friends-widget');
    if (!root) return;

    var ME = parseInt(root.getAttribute('data-me'), 10);
    var CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    var state = {
        friends: [],            // [{id, username, avatar}]
        online: {},             // id -> true
        unread: {},             // id -> count
        activeFriendId: null,   // open chat
        open: false
    };

    var el = function (id) { return document.getElementById(id); };

    // ---------- helpers ----------
    function jsonFetch(url, opts) {
        opts = opts || {};
        opts.headers = Object.assign({ 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, opts.headers || {});
        opts.credentials = 'same-origin';
        return fetch(url, opts).then(function (r) { return r.json(); }).catch(function () { return { success: false }; });
    }
    function fmtTime(iso) {
        try { var d = new Date(iso); return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0'); }
        catch (e) { return ''; }
    }
    function totalUnread() {
        var t = 0; for (var k in state.unread) { t += state.unread[k] || 0; } return t;
    }

    // ---------- socket (reuse game.js connection) ----------
    var socket = window.socket;
    function refreshPresence() {
        if (!socket || !socket.connected || !state.friends.length) { return; }
        socket.emit('friendsOnline', { friendIds: state.friends.map(function (f) { return f.id; }) }, function (onlineIds) {
            state.online = {};
            (onlineIds || []).forEach(function (id) { state.online[id] = true; });
            renderFriends();
            updateOnlineCount();
            if (state.activeFriendId) updateChatStatus();
        });
    }
    function updateOnlineCount() {
        var n = 0; for (var k in state.online) { if (state.online[k]) n++; }
        el('friends-online-count').textContent = n;
    }
    // Tell the node "I'm online" so it can notify my online friends I went live.
    // Safe to call repeatedly — the node throttles per user (5 min while testing).
    function announceMe() {
        if (!socket || !socket.connected) return;
        socket.emit('announceOnline', {
            userId: ME,
            username: window.username,
            avatar: window.avatar,
            friendIds: state.friends.map(function (f) { return f.id; })
        });
    }

    // ---------- data ----------
    function loadFriends() {
        return jsonFetch('/friends/list').then(function (res) {
            state.friends = (res && res.success) ? res.data : [];
            renderFriends();
            refreshPresence();
            return loadUnread();
        });
    }
    function loadUnread() {
        return jsonFetch('/friends/unread').then(function (res) {
            state.unread = (res && res.success) ? (res.data || {}) : {};
            renderFriends();
            updateUnreadDot();
        });
    }
    function updateUnreadDot() {
        el('friends-unread-dot').classList.toggle('hidden', totalUnread() === 0);
    }

    // ---------- render: friends list ----------
    function renderFriends() {
        var list = el('friends-list');
        list.querySelectorAll('[data-friend]').forEach(function (n) { n.remove(); });

        var sorted = state.friends.slice().sort(function (a, b) {
            var ao = state.online[a.id] ? 1 : 0, bo = state.online[b.id] ? 1 : 0;
            if (ao !== bo) return bo - ao;                 // online first
            return a.username.localeCompare(b.username);
        });

        el('friends-empty').style.display = sorted.length ? 'none' : 'block';

        sorted.forEach(function (f) {
            var online = !!state.online[f.id];
            var unread = state.unread[f.id] || 0;

            var row = document.createElement('button');
            row.type = 'button';
            row.setAttribute('data-friend', f.id);
            row.className = 'w-full flex items-center gap-3 px-3 py-2 hover:bg-slate-100 text-left';

            var avatarWrap = document.createElement('div');
            avatarWrap.className = 'relative';
            var img = document.createElement('img');
            img.src = f.avatar; img.className = 'w-9 h-9 rounded-full border border-slate-300 object-cover';
            img.onerror = function () { this.src = '/images/default-avatar.jpg'; };
            var dot = document.createElement('span');
            dot.className = 'absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white ' + (online ? 'bg-green-500' : 'bg-slate-400');
            avatarWrap.appendChild(img); avatarWrap.appendChild(dot);

            var mid = document.createElement('div');
            mid.className = 'flex-1 min-w-0';
            var name = document.createElement('div');
            name.className = 'text-sm font-semibold text-main-dark truncate';
            name.textContent = f.username;
            var status = document.createElement('div');
            status.className = 'text-xs ' + (online ? 'text-green-600' : 'text-slate-400');
            status.textContent = online ? 'online' : 'offline';
            mid.appendChild(name); mid.appendChild(status);

            row.appendChild(avatarWrap);
            row.appendChild(mid);

            if (unread > 0) {
                var badge = document.createElement('span');
                badge.className = 'bg-rose-600 text-white text-xs font-bold rounded-full px-2 py-0.5';
                badge.textContent = unread;
                row.appendChild(badge);
            }

            row.addEventListener('click', function () { openChat(f); });
            list.appendChild(row);
        });
    }

    // ---------- add friend ----------
    var searchTimer = null;
    function onSearchInput() {
        clearTimeout(searchTimer);
        var q = el('friends-search-input').value.trim();
        if (!q) { el('friends-search-results').innerHTML = ''; return; }
        searchTimer = setTimeout(function () {
            jsonFetch('/friends/search?q=' + encodeURIComponent(q)).then(function (res) {
                renderSearch((res && res.success) ? res.data : []);
            });
        }, 250);
    }
    function renderSearch(users) {
        var box = el('friends-search-results');
        box.innerHTML = '';
        if (!users.length) {
            var none = document.createElement('p');
            none.className = 'text-xs text-slate-400 py-1'; none.textContent = 'No matches.';
            box.appendChild(none); return;
        }
        users.forEach(function (u) {
            var row = document.createElement('div');
            row.className = 'flex items-center gap-2';
            var img = document.createElement('img');
            img.src = u.avatar; img.className = 'w-7 h-7 rounded-full border border-slate-300 object-cover';
            img.onerror = function () { this.src = '/images/default-avatar.jpg'; };
            var name = document.createElement('span');
            name.className = 'flex-1 text-sm truncate'; name.textContent = u.username;
            var btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'text-xs font-semibold text-white bg-green-600 rounded px-2 py-1 hover:opacity-90';
            btn.textContent = 'Add';
            btn.addEventListener('click', function () {
                jsonFetch('/friends/add', { method: 'POST', body: JSON.stringify({ friend_id: u.id }) }).then(function (res) {
                    if (res && res.success) {
                        el('friends-search-input').value = '';
                        el('friends-search-results').innerHTML = '';
                        el('friends-add-box').classList.add('hidden');
                        loadFriends();
                    }
                });
            });
            row.appendChild(img); row.appendChild(name); row.appendChild(btn);
            box.appendChild(row);
        });
    }

    // ---------- chat ----------
    function showView(which) {
        el('friends-list-view').classList.toggle('hidden', which !== 'list');
        el('friends-chat-view').classList.toggle('hidden', which !== 'chat');
    }
    function friendById(id) {
        for (var i = 0; i < state.friends.length; i++) { if (state.friends[i].id === id) return state.friends[i]; }
        return null;
    }
    function updateChatStatus() {
        var online = !!state.online[state.activeFriendId];
        var s = el('friends-chat-status');
        s.textContent = online ? 'online' : 'offline';
        s.className = 'text-xs ' + (online ? 'text-green-400' : 'text-slate-400');
    }
    function openChat(friend) {
        state.activeFriendId = friend.id;
        el('friends-chat-name').textContent = friend.username;
        el('friends-chat-avatar').src = friend.avatar;
        updateChatStatus();
        el('friends-chat-messages').innerHTML = '';
        showView('chat');

        jsonFetch('/friends/' + friend.id + '/messages').then(function (res) {
            if (res && res.success) {
                (res.data || []).forEach(function (m) { appendMessage(m.mine, m.message, m.created_at); });
                scrollMessages();
            }
            // Those messages are now read.
            state.unread[friend.id] = 0;
            renderFriends();
            updateUnreadDot();
        });
        el('friends-chat-input').focus();
    }
    function appendMessage(mine, text, iso) {
        var wrap = document.createElement('div');
        wrap.className = 'flex flex-col max-w-[80%] ' + (mine ? 'self-end items-end' : 'self-start items-start');
        var bubble = document.createElement('div');
        bubble.className = 'rounded-2xl px-3 py-2 text-sm break-words ' +
            (mine ? 'bg-main-accent text-white rounded-br-sm' : 'bg-slate-200 text-slate-800 rounded-bl-sm');
        bubble.textContent = text;
        var time = document.createElement('span');
        time.className = 'text-[10px] text-slate-400 mt-0.5'; time.textContent = fmtTime(iso);
        wrap.appendChild(bubble); wrap.appendChild(time);
        el('friends-chat-messages').appendChild(wrap);
    }
    function scrollMessages() {
        var m = el('friends-chat-messages'); m.scrollTop = m.scrollHeight;
    }
    function sendMessage() {
        var input = el('friends-chat-input');
        var text = input.value.trim();
        var to = state.activeFriendId;
        if (!text || !to) return;
        input.value = '';
        jsonFetch('/friends/messages', { method: 'POST', body: JSON.stringify({ to_user_id: to, message: text }) }).then(function (res) {
            if (!res || !res.success) { input.value = text; return; }
            var m = res.data;
            appendMessage(true, m.message, m.created_at);
            scrollMessages();
            // Relay over the socket for instant delivery to the recipient.
            if (socket && socket.connected) {
                socket.emit('friendsMessage', {
                    toUserId: to,
                    payload: { id: m.id, from_user_id: ME, from_username: m.from_username, from_avatar: m.from_avatar, message: m.message, created_at: m.created_at }
                });
            }
        });
    }

    // ---------- incoming message ----------
    function onIncoming(payload) {
        if (!payload || !payload.from_user_id) return;
        var fromId = parseInt(payload.from_user_id, 10);

        // If I'm already friends and have the chat open, show it live.
        if (state.activeFriendId === fromId && state.open) {
            appendMessage(false, payload.message, payload.created_at);
            scrollMessages();
            // Mark as read immediately since it's on screen.
            jsonFetch('/friends/' + fromId + '/messages');
            return;
        }
        // Otherwise bump the unread counter + indicator.
        state.unread[fromId] = (state.unread[fromId] || 0) + 1;
        // A brand-new friend might not be in the list yet — refresh it.
        if (!friendById(fromId)) { loadFriends(); } else { renderFriends(); updateUnreadDot(); }
    }

    // ---------- wire up ----------
    function togglePanel(force) {
        state.open = (typeof force === 'boolean') ? force : !state.open;
        el('friends-panel').classList.toggle('hidden', !state.open);
        if (state.open) { showView('list'); loadFriends(); }
    }

    el('friends-toggle').addEventListener('click', function () { togglePanel(); });
    el('friends-add-toggle').addEventListener('click', function () {
        var box = el('friends-add-box'); box.classList.toggle('hidden');
        if (!box.classList.contains('hidden')) el('friends-search-input').focus();
    });
    el('friends-search-input').addEventListener('input', onSearchInput);
    el('friends-chat-back').addEventListener('click', function () { state.activeFriendId = null; showView('list'); });
    el('friends-chat-send').addEventListener('click', sendMessage);
    el('friends-chat-input').addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); sendMessage(); } });

    // A friend just came online — show a toast and reflect their status live.
    function onFriendLive(data) {
        if (!data || !data.username) return;
        try { if (typeof GameApi !== 'undefined' && GameApi.displayAlertNotification) GameApi.displayAlertNotification('info', data.username + ' is now online'); } catch (e) {}
        if (data.id) { state.online[data.id] = true; renderFriends(); updateOnlineCount(); }
    }

    if (socket) {
        socket.on('friendsMessage', onIncoming);
        socket.on('friendWentLive', onFriendLive);
        socket.on('onlineUsersCount', function () { refreshPresence(); });
        socket.on('connect', function () { refreshPresence(); loadFriends().then(announceMe); });
    }

    // Prime unread/presence on load (panel closed) so the indicator is accurate,
    // then announce myself so already-online friends are notified I went live.
    loadFriends().then(announceMe);
    setInterval(refreshPresence, 15000);
    setInterval(loadUnread, 30000);
})();
</script>
@endauth
