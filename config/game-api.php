<?php

return [
    // Base URL of the game-node socket.io server the browser connects to.
    // Defaults to the local Docker mapping; set GAME_SOCKET_URL for production.
    'socket_url' => env('GAME_SOCKET_URL', 'http://localhost:5001'),
];
