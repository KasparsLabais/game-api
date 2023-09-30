var playerList = {};
var gameInstance = {};

var socket = io('http://localhost:3000');
socket.on('connect', function(data) {
    console.log('connect', data);
    socket.emit('userconnected', {'username': window.username, 'id': window.id});
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

const joinRoom = (gameToken, repeatCount = 0) => {
    socket.emit('joinRoom', { 'gameToken' : gameToken, 'repeatCount' : repeatCount}, (answer) => {
        if (!answer.status && answer.repeatCount  < 10) {
            setTimeout(() => { console.log('Repeat'); joinRoom(gameToken, answer.repeatCount); }, 1000);
        }
    });
}

const joinGameInstance = (gameToken, redirectUrl, repeatCount = 0) => {
    socket.emit('joinGameInstance', { 'gameToken' : gameToken, 'repeatCount' : repeatCount}, (answer) => {
        if (!answer.status && answer.repeatCount  < 10) {
            setTimeout(() => { console.log('Repeat'); joinGameInstance(gameToken, redirectUrl, answer.repeatCount); }, 1000);
        } else {
            console.log('Redirecting to ', redirectUrl);
            window.location.href = redirectUrl;
        }
    });
}

const addGameInstance = (gameToken, game) => {
    console.log('addGameInstance', gameToken, game);

    gameInstance = game;
    socket.emit('addOrUpdateGameInstance', { 'gameToken': gameToken, 'gameInstance':game });
}
const getGameInstance = (gameToken) => {
    socket.emit('getGameInstance', { gameToken });
}

const updateGameInstance = (gameToken, gameInstance, action = 'updateGameInstanceStatus') => {
    socket.emit('updateGameInstance', { 'gameToken': gameToken, 'gameInstance':gameInstance, 'action': action });
}

const redirect = (gameToken, url) => {
    socket.emit('redirect', { 'gameToken': gameToken, 'url': url });
}