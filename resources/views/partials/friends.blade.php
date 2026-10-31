{{--
    Friends panel + private messaging. Self-contained: include
    @include('game-api::partials.friends') from a page that extends
    game-api::layout (it provides socket.io, window.socket from game.js, the CSRF
    meta tag and window.id/username for the authenticated user).

    Neo-brutalist "old school mail":
    - Floating envelope (bottom-right). A bookmark ribbon shows how many friends
      are live; a sticky note is attached when there are unread messages.
    - Click to open a mail client: "Inbox" (friends by most recent message, with
      a preview) and "Online" folders, plus "+ Add contact" search.
    - Click a friend to open the conversation as a message-board thread.
    - Delivery is real-time via the node; every message is persisted by the
      Laravel app first, so history survives a node restart.
--}}
@auth
<style>
    #friends-toggle:hover .friends-envelope { transform: translate(-2px, -2px); box-shadow: 7px 7px 0 0 #14323B; }
    #friends-toggle:active .friends-envelope { transform: translate(3px, 3px); box-shadow: 0 0 0 0 #14323B; }
    .friends-note-in { animation: friends-note-in 0.4s cubic-bezier(.2, 1.6, .4, 1) both; }
    @keyframes friends-note-in { from { opacity: 0; transform: rotate(30deg) scale(0.4); } to { opacity: 1; transform: rotate(8deg) scale(1); } }
    @media (prefers-reduced-motion: reduce) { .friends-note-in { animation: none; } }
</style>

<div id="friends-widget" class="fixed bottom-4 right-4 z-50 fira-sans" data-me="{{ Auth::id() }}">

    {{-- Floating envelope --}}
    <button id="friends-toggle" type="button" aria-label="Friends and messages" aria-expanded="false"
            class="relative block pt-4 pr-4">
        <span class="friends-envelope relative block w-20 h-14 bg-white border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] transition-all duration-100">
            {{-- Flap --}}
            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 72 48" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,0 36,28 72,0" fill="none" stroke="#14323B" stroke-width="4" stroke-linejoin="miter"/>
                <polyline points="0,48 28,22" fill="none" stroke="#14323B" stroke-width="2"/>
                <polyline points="72,48 44,22" fill="none" stroke="#14323B" stroke-width="2"/>
            </svg>
        </span>

        {{-- Bookmark ribbon: friends live --}}
        <span id="friends-online-bookmark" class="hidden absolute top-1 left-2 w-7 h-10" title="Friends online">
            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 28 40" aria-hidden="true">
                <polygon points="1,1 27,1 27,39 14,30 1,39" fill="#1E93AB" stroke="#14323B" stroke-width="2"/>
            </svg>
            <span id="friends-online-count" class="relative block text-center russo text-white text-sm leading-none pt-2">0</span>
        </span>

        {{-- Sticky note: unread messages --}}
        <span id="friends-unread-note" class="hidden absolute -top-1 right-0 w-11 h-11 bg-pop border-2 border-main-dark shadow-[3px_3px_0_0_#14323B] rotate-[8deg] flex-col items-center justify-center" title="New messages">
            <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-6 h-2.5 bg-white/80 border border-main-dark/40 -rotate-6" aria-hidden="true"></span>
            <span id="friends-unread-count" class="russo text-main-dark text-base leading-none">0</span>
            <span class="fira-sans text-[9px] font-bold uppercase text-main-dark leading-none mt-0.5">new</span>
        </span>
    </button>

    {{-- Mail client window --}}
    <div id="friends-panel" class="hidden absolute bottom-20 right-0 w-[23rem] max-w-[calc(100vw-2rem)] bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B] overflow-hidden">

        {{-- Title bar --}}
        <div class="flex items-center gap-2 bg-main-dark px-3 py-2">
            <span class="w-3 h-3 rounded-full bg-main-accent shrink-0"></span>
            <span class="w-3 h-3 rounded-full bg-pop shrink-0"></span>
            <span class="w-3 h-3 rounded-full bg-green-500 shrink-0"></span>
            <span id="friends-panel-title" class="ml-1 russo uppercase tracking-widest text-white text-xs truncate">Mail</span>
            <button id="friends-close" type="button" aria-label="Close" class="ml-auto shrink-0 w-6 h-6 flex items-center justify-center border-2 border-white/60 text-white hover:bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
        </div>

        {{-- INBOX VIEW --}}
        <div id="friends-list-view">
            {{-- Toolbar: folders + add contact --}}
            <div class="flex items-stretch border-b-4 border-main-dark bg-surface">
                <button type="button" data-folder="inbox" class="friends-folder px-3 py-2 russo text-xs uppercase tracking-wide border-r-4 border-main-dark">
                    Inbox <span id="friends-inbox-count" class="hidden ml-1 bg-main-accent text-white px-1.5"></span>
                </button>
                <button type="button" data-folder="online" class="friends-folder px-3 py-2 russo text-xs uppercase tracking-wide border-r-4 border-main-dark">
                    Online <span id="friends-online-folder-count" class="ml-1 text-main-dark/50">0</span>
                </button>
                <button id="friends-add-toggle" type="button" class="ml-auto px-3 py-2 russo text-xs uppercase tracking-wide bg-pop border-l-4 border-main-dark hover:bg-white">+ Add</button>
            </div>

            {{-- Add-contact search (hidden by default) --}}
            <div id="friends-add-box" class="hidden border-b-4 border-main-dark px-3 py-3 bg-white">
                <label for="friends-search-input" class="block fira-sans text-[10px] uppercase tracking-widest text-main-dark/60 mb-1">To:</label>
                <input id="friends-search-input" type="text" placeholder="Search username…" autocomplete="off"
                       class="w-full bg-white border-2 border-main-dark px-2 py-1.5 text-sm text-main-dark font-bold focus:outline-none focus:shadow-[3px_3px_0_0_#14323B]">
                <div id="friends-search-results" class="mt-2 flex flex-col gap-1 max-h-40 overflow-y-auto"></div>
            </div>

            <div id="friends-list" class="max-h-80 overflow-y-auto">
                <p id="friends-empty" class="fira-sans text-main-dark/60 text-sm text-center py-8 px-4"></p>
            </div>
        </div>

        {{-- THREAD VIEW (message board) --}}
        <div id="friends-chat-view" class="hidden flex flex-col h-[28rem] max-h-[70vh]">
            <div class="flex items-center gap-2 px-3 py-2 bg-main-light border-b-4 border-main-dark">
                <button id="friends-chat-back" type="button" aria-label="Back to inbox"
                        class="shrink-0 px-2 py-1 border-2 border-main-dark bg-white russo text-[10px] uppercase tracking-wide hover:bg-pop">&laquo; Inbox</button>
                <img id="friends-chat-avatar" src="/images/default-avatar.jpg" alt="" class="w-8 h-8 border-2 border-main-dark object-cover shrink-0">
                <div class="flex flex-col leading-tight min-w-0">
                    <span class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60">Thread with</span>
                    <span id="friends-chat-name" class="fira-sans text-sm font-bold text-main-dark truncate">—</span>
                </div>
                <span id="friends-chat-status" class="ml-auto shrink-0 border-2 border-main-dark px-1.5 py-0.5 russo text-[10px] uppercase tracking-wide">offline</span>
            </div>
            <div id="friends-chat-messages" class="flex-1 overflow-y-auto px-3 py-3 bg-main-light flex flex-col gap-3"></div>
            <div class="border-t-4 border-main-dark p-2 bg-white flex flex-col gap-2">
                <textarea id="friends-chat-input" rows="2" maxlength="2000" placeholder="Write a reply…"
                          class="w-full resize-none bg-white border-2 border-main-dark px-2 py-1.5 text-sm text-main-dark focus:outline-none focus:shadow-[3px_3px_0_0_#14323B]"></textarea>
                <div class="flex items-center gap-2">
                    <span class="fira-sans text-[10px] text-main-dark/50">Enter to post &middot; Shift+Enter for a new line</span>
                    <button id="friends-chat-send" type="button"
                            class="ml-auto bg-main-accent text-white border-2 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-1.5 russo text-xs uppercase tracking-wide transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[4px_4px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">Post reply</button>
                </div>
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
        friends: [],            // [{id, username, avatar, last_message}]
        online: {},             // id -> true
        unread: {},             // id -> count
        activeFriendId: null,   // open thread
        folder: 'inbox',        // 'inbox' | 'online'
        unreadShown: 0,         // for the sticky-note entrance animation
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
    function pad(n) { return n.toString().padStart(2, '0'); }
    function fmtTime(iso) {
        try { var d = new Date(iso); return pad(d.getHours()) + ':' + pad(d.getMinutes()); }
        catch (e) { return ''; }
    }
    // Inbox column: time for today, otherwise "12 Oct".
    function fmtInboxDate(iso) {
        try {
            var d = new Date(iso), now = new Date();
            if (d.toDateString() === now.toDateString()) return fmtTime(iso);
            return d.getDate() + ' ' + d.toLocaleString('en', { month: 'short' });
        } catch (e) { return ''; }
    }
    // Board post header: "12 Oct 2026, 14:05".
    function fmtPostDate(iso) {
        try {
            var d = new Date(iso);
            return d.getDate() + ' ' + d.toLocaleString('en', { month: 'short' }) + ' ' + d.getFullYear() + ', ' + fmtTime(iso);
        } catch (e) { return ''; }
    }
    function totalUnread() {
        var t = 0; for (var k in state.unread) { t += state.unread[k] || 0; } return t;
    }
    function onlineCount() {
        var n = 0; for (var k in state.online) { if (state.online[k]) n++; } return n;
    }
    function avatarImg(src, cls) {
        var img = document.createElement('img');
        img.src = src || '/images/default-avatar.jpg'; img.alt = ''; img.className = cls;
        img.onerror = function () { this.onerror = null; this.src = '/images/default-avatar.jpg'; };
        return img;
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
    // Bookmark ribbon on the envelope + "Online" folder count.
    function updateOnlineCount() {
        var n = onlineCount();
        el('friends-online-count').textContent = n > 99 ? '99+' : n;
        el('friends-online-bookmark').classList.toggle('hidden', n === 0);
        el('friends-online-folder-count').textContent = n;
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
            updateUnreadNote();
        });
    }
    // Sticky note on the envelope + Inbox folder badge.
    function updateUnreadNote() {
        var n = totalUnread();
        var note = el('friends-unread-note');
        el('friends-unread-count').textContent = n > 99 ? '99+' : n;
        note.classList.toggle('hidden', n === 0);
        note.classList.toggle('flex', n > 0);
        if (n > state.unreadShown) {
            // Re-run the "slap it on" animation when new mail arrives.
            note.classList.remove('friends-note-in'); void note.offsetWidth; note.classList.add('friends-note-in');
        }
        state.unreadShown = n;
        var badge = el('friends-inbox-count');
        badge.textContent = n;
        badge.classList.toggle('hidden', n === 0);
    }
    function setLastMessage(friendId, message, mine, iso) {
        var f = friendById(friendId);
        if (f) { f.last_message = { message: message, mine: mine, created_at: iso }; }
    }

    // ---------- render: inbox ----------
    function setFolder(folder) {
        state.folder = folder;
        document.querySelectorAll('.friends-folder').forEach(function (b) {
            var active = b.getAttribute('data-folder') === folder;
            b.classList.toggle('bg-white', active);
            b.classList.toggle('bg-main-light', !active);
            b.classList.toggle('text-main-dark', active);
            b.classList.toggle('text-main-dark/60', !active);
        });
        renderFriends();
    }
    function renderFriends() {
        var list = el('friends-list');
        list.querySelectorAll('[data-friend]').forEach(function (n) { n.remove(); });

        var rows = state.friends.slice();
        if (state.folder === 'online') {
            rows = rows.filter(function (f) { return state.online[f.id]; });
        }
        // Inbox order: unread first, then most recent conversation, then live, then name.
        rows.sort(function (a, b) {
            var au = state.unread[a.id] ? 1 : 0, bu = state.unread[b.id] ? 1 : 0;
            if (au !== bu) return bu - au;
            var at = a.last_message ? Date.parse(a.last_message.created_at) : 0;
            var bt = b.last_message ? Date.parse(b.last_message.created_at) : 0;
            if (at !== bt) return bt - at;
            var ao = state.online[a.id] ? 1 : 0, bo = state.online[b.id] ? 1 : 0;
            if (ao !== bo) return bo - ao;
            return a.username.localeCompare(b.username);
        });

        var empty = el('friends-empty');
        empty.textContent = state.friends.length
            ? (state.folder === 'online' ? 'No friends are online right now.' : '')
            : 'No contacts yet. Use “+ Add” to find people.';
        empty.style.display = rows.length ? 'none' : 'block';

        rows.forEach(function (f, i) {
            var online = !!state.online[f.id];
            var unread = state.unread[f.id] || 0;
            var last = f.last_message;

            var row = document.createElement('button');
            row.type = 'button';
            row.setAttribute('data-friend', f.id);
            row.className = 'w-full flex items-start gap-3 px-3 py-2.5 text-left border-b-2 border-main-dark/20 hover:bg-pop/40 ' +
                (unread ? 'bg-pop/20' : (i % 2 ? 'bg-main-light/60' : 'bg-white'));

            // Avatar with status square.
            var avatarWrap = document.createElement('div');
            avatarWrap.className = 'relative shrink-0';
            avatarWrap.appendChild(avatarImg(f.avatar, 'w-9 h-9 border-2 border-main-dark object-cover'));
            var dot = document.createElement('span');
            dot.className = 'absolute -bottom-1 -right-1 w-3.5 h-3.5 border-2 border-main-dark ' + (online ? 'bg-green-500' : 'bg-surface');
            dot.title = online ? 'online' : 'offline';
            avatarWrap.appendChild(dot);

            // From / preview.
            var mid = document.createElement('div');
            mid.className = 'flex-1 min-w-0';
            var top = document.createElement('div');
            top.className = 'flex items-center gap-2';
            var name = document.createElement('span');
            name.className = 'text-sm text-main-dark truncate ' + (unread ? 'font-black' : 'font-bold');
            name.textContent = f.username;
            top.appendChild(name);
            if (online) {
                var live = document.createElement('span');
                live.className = 'shrink-0 bg-green-500 text-white border border-main-dark px-1 russo text-[9px] uppercase tracking-wide';
                live.textContent = 'Live';
                top.appendChild(live);
            }
            var preview = document.createElement('div');
            preview.className = 'text-xs truncate mt-0.5 ' + (unread ? 'text-main-dark font-bold' : 'text-main-dark/60');
            preview.textContent = last ? ((last.mine ? 'You: ' : '') + last.message) : 'No messages yet — say hi!';
            mid.appendChild(top); mid.appendChild(preview);

            // Date + unread count.
            var right = document.createElement('div');
            right.className = 'shrink-0 flex flex-col items-end gap-1';
            var date = document.createElement('span');
            date.className = 'text-[10px] text-main-dark/60 font-bold';
            date.textContent = last ? fmtInboxDate(last.created_at) : '';
            right.appendChild(date);
            if (unread > 0) {
                var badge = document.createElement('span');
                badge.className = 'bg-main-accent text-white border border-main-dark russo text-[10px] px-1.5';
                badge.textContent = unread;
                right.appendChild(badge);
            }

            row.appendChild(avatarWrap);
            row.appendChild(mid);
            row.appendChild(right);
            row.addEventListener('click', function () { openChat(f); });
            list.appendChild(row);
        });
    }

    // ---------- add contact ----------
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
            none.className = 'text-xs text-main-dark/60 py-1'; none.textContent = 'No matches.';
            box.appendChild(none); return;
        }
        users.forEach(function (u) {
            var row = document.createElement('div');
            row.className = 'flex items-center gap-2 border-2 border-main-dark bg-main-light px-2 py-1';
            var name = document.createElement('span');
            name.className = 'flex-1 text-sm font-bold text-main-dark truncate'; name.textContent = u.username;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'border-2 border-main-dark bg-green-500 text-white russo text-[10px] uppercase tracking-wide px-2 py-0.5 hover:bg-green-600';
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
            row.appendChild(avatarImg(u.avatar, 'w-7 h-7 border-2 border-main-dark object-cover'));
            row.appendChild(name); row.appendChild(btn);
            box.appendChild(row);
        });
    }

    // ---------- thread (message board) ----------
    function showView(which) {
        el('friends-list-view').classList.toggle('hidden', which !== 'list');
        el('friends-chat-view').classList.toggle('hidden', which !== 'chat');
        el('friends-panel-title').textContent = which === 'chat' ? 'Message Board' : 'Mail';
    }
    function friendById(id) {
        for (var i = 0; i < state.friends.length; i++) { if (state.friends[i].id === id) return state.friends[i]; }
        return null;
    }
    function updateChatStatus() {
        var online = !!state.online[state.activeFriendId];
        var s = el('friends-chat-status');
        s.textContent = online ? 'Live' : 'Offline';
        s.className = 'ml-auto shrink-0 border-2 border-main-dark px-1.5 py-0.5 russo text-[10px] uppercase tracking-wide ' +
            (online ? 'bg-green-500 text-white' : 'bg-surface text-main-dark');
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
                var msgs = res.data || [];
                if (!msgs.length) { boardEmpty(friend.username); }
                msgs.forEach(function (m) { appendMessage(m.mine, m.message, m.created_at); });
                scrollMessages();
            }
            // Those messages are now read.
            state.unread[friend.id] = 0;
            renderFriends();
            updateUnreadNote();
        });
        el('friends-chat-input').focus();
    }
    function boardEmpty(username) {
        var p = document.createElement('p');
        p.setAttribute('data-board-empty', '1');
        p.className = 'text-center text-xs text-main-dark/50 py-6';
        p.textContent = 'No posts yet. Start the thread with ' + username + '!';
        el('friends-chat-messages').appendChild(p);
    }
    // One board post: header bar (author + date) above the message body.
    function appendMessage(mine, text, iso) {
        var empty = el('friends-chat-messages').querySelector('[data-board-empty]');
        if (empty) empty.remove();

        var friend = friendById(state.activeFriendId) || {};
        var post = document.createElement('div');
        post.className = 'bg-white border-2 border-main-dark shadow-[3px_3px_0_0_#14323B] ' + (mine ? 'ml-6' : 'mr-6');

        var head = document.createElement('div');
        head.className = 'flex items-center gap-2 px-2 py-1 border-b-2 border-main-dark ' +
            (mine ? 'bg-brand text-white' : 'bg-main-dark text-white');
        head.appendChild(avatarImg(mine ? window.avatar : friend.avatar, 'w-5 h-5 border border-white/60 object-cover'));
        var author = document.createElement('span');
        author.className = 'russo text-[11px] uppercase tracking-wide truncate';
        author.textContent = mine ? (window.username || 'You') : (friend.username || 'Friend');
        var date = document.createElement('span');
        date.className = 'ml-auto shrink-0 fira-sans text-[10px] opacity-75';
        date.textContent = fmtPostDate(iso);
        head.appendChild(author); head.appendChild(date);

        var body = document.createElement('div');
        body.className = 'px-3 py-2 text-sm text-main-dark whitespace-pre-wrap break-words';
        body.textContent = text;

        post.appendChild(head); post.appendChild(body);
        el('friends-chat-messages').appendChild(post);
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
            setLastMessage(to, m.message, true, m.created_at);
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
        setLastMessage(fromId, payload.message, false, payload.created_at);

        // Thread already open: show it live.
        if (state.activeFriendId === fromId && state.open) {
            appendMessage(false, payload.message, payload.created_at);
            scrollMessages();
            // Mark as read immediately since it's on screen.
            jsonFetch('/friends/' + fromId + '/messages');
            return;
        }
        // Otherwise bump the unread counter + sticky note.
        state.unread[fromId] = (state.unread[fromId] || 0) + 1;
        // A brand-new friend might not be in the list yet — refresh it.
        if (!friendById(fromId)) { loadFriends(); } else { renderFriends(); updateUnreadNote(); }
    }

    // ---------- wire up ----------
    function togglePanel(force) {
        state.open = (typeof force === 'boolean') ? force : !state.open;
        el('friends-panel').classList.toggle('hidden', !state.open);
        el('friends-toggle').setAttribute('aria-expanded', state.open ? 'true' : 'false');
        if (state.open) { state.activeFriendId = null; showView('list'); loadFriends(); }
    }

    el('friends-toggle').addEventListener('click', function () { togglePanel(); });
    el('friends-close').addEventListener('click', function () { togglePanel(false); });
    document.querySelectorAll('.friends-folder').forEach(function (b) {
        b.addEventListener('click', function () { setFolder(b.getAttribute('data-folder')); });
    });
    el('friends-add-toggle').addEventListener('click', function () {
        var box = el('friends-add-box'); box.classList.toggle('hidden');
        if (!box.classList.contains('hidden')) el('friends-search-input').focus();
    });
    el('friends-search-input').addEventListener('input', onSearchInput);
    el('friends-chat-back').addEventListener('click', function () { state.activeFriendId = null; showView('list'); renderFriends(); });
    el('friends-chat-send').addEventListener('click', sendMessage);
    el('friends-chat-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

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

    // Prime unread/presence on load (panel closed) so the envelope is accurate,
    // then announce myself so already-online friends are notified I went live.
    setFolder('inbox');
    loadFriends().then(announceMe);
    setInterval(refreshPresence, 15000);
    setInterval(loadUnread, 30000);
})();
</script>
@endauth
