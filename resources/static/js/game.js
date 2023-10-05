var playerList = {};
var gameInstance = {};

var socket = io('http://localhost:3000');
socket.on('connect', function(data) {
    console.log('connect', data);
    socket.emit('userconnected', {'username': window.username, 'id': window.id, 'token' : window.playerToken });
});
socket.on('playerJoined', (data) => {
    console.log('playerJoined', data);
    playerJoined(data);
});

socket.on('gameInstanceUpdated', (data) => {
    console.log('gameInstanceUpdated', data);
    gameInstance = data.gameInstance;
    callbackGameInstanceUpdated(data.gameToken, data.gameInstance, data.action);
});

socket.on('updatePoints', (data) => {
    console.log('updatePoints', data);
    GameApi.updatePointsUI(data.points);
});

socket.on('playerInstanceUpdated', (data) => {
    console.log('playerInstanceUpdated', data);
    document.dispatchEvent(new CustomEvent(data.action, { detail: data }));
});

socket.on('notifyGameMaster', (data) => {
    console.log('notifyGameMaster', data);
    document.dispatchEvent(new CustomEvent(data.action, { detail: data }));
});

const GameApi = {
    'joinRoom': function(gameToken, repeatCount = 0) {
        socket.emit('joinRoom', { 'gameToken' : gameToken, 'repeatCount' : repeatCount}, (answer) => {
            if (!answer.status && answer.repeatCount  < 10) {
                setTimeout(() => { console.log('Repeat'); GameApi.joinRoom(gameToken, answer.repeatCount); }, 1000);
            }
        });
    },
    'joinGameInstance': function(gameToken, redirectUrl, repeatCount = 0) {
        socket.emit('joinGameInstance', { 'gameToken' : gameToken, 'repeatCount' : repeatCount}, (answer) => {
            if (!answer.status && answer.repeatCount  < 10) {
                setTimeout(() => { console.log('Repeat'); GameApi.joinGameInstance(gameToken, redirectUrl, answer.repeatCount); }, 1000);
            } else {
                console.log('Redirecting to ', redirectUrl);
                window.location.href = redirectUrl;
            }
        });
    },
    'addGameInstance': function(gameToken, game) {
        console.log('addGameInstance', gameToken, game);
        gameInstance = game;
        socket.emit('addOrUpdateGameInstance', { 'gameToken': gameToken, 'gameInstance':game });
    },
    'getGameInstance': function(gameToken) {
        console.log('getGameInstance', gameToken);
        socket.emit('getGameInstance', { gameToken });
    },
    'updateGameInstance': function(gameToken, gameInstance, action = 'updateGameInstanceStatus') {
        console.log('updateGameInstance', gameToken, gameInstance);
        socket.emit('updateGameInstance', { 'gameToken': gameToken, 'gameInstance':gameInstance, 'action': action });
    },
    'updatePlayerInstance': function(gameToken, playerInstance, action = 'updatePlayerInstanceStatus') {
        console.log('updatePlayerInstance', gameToken, playerInstance);
        socket.emit('updatePlayerInstance', { 'gameToken': gameToken, 'playerInstance':playerInstance, 'action': action });
    },
    'redirect': function(gameToken, url) {
        console.log('redirect', gameToken, url);
        socket.emit('redirect', { 'gameToken': gameToken, 'url': url });
    },
    'notifyGameMaster': function(gameToken, data) {
        console.log('notifyGameMaster', gameToken, data);
        socket.emit('notifyGameMaster', { 'gameToken': gameToken, 'data': data });
    },
    'updatePointsUI': function(points) {
        console.log('updatePointsUI', points);
        let pointsHolder = document.getElementById('game-api-_points-holder');
        pointsHolder.innerHTML = points;
    }
}
