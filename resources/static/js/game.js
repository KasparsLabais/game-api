var playerList = {};
var gameInstance = {};

var socket = io('http://localhost:3000');
socket.on('connect', function(data) {
    socket.emit('userconnected', {'username': window.username, 'id': window.id});
});
socket.on('playerJoined', (data) => {
    playerJoined(data);
});

const joinRoom = (gameToken, repeatCount = 0) => {
    socket.emit('joinGameInstance', { 'gameToken' : gameToken, 'repeatCount' : repeatCount}, (answer) => {
        if (!answer.status && answer.repeatCount  < 10) {
            setTimeout(() => { console.log('Repeat'); joinRoom(gameToken, answer.repeatCount); }, 1000);
        }
    });
}

//game instance controller to handle socket io events
const addGameInstance = (gameToken, gameInstance) => {
    console.log('addGameInstance', gameToken, gameInstance);
    socket.emit('addOrUpdateGameInstance', { 'gameToken': gameToken, 'gameInstance':gameInstance });
}
const getGameInstance = (gameToken) => {
    socket.emit('getGameInstance', { gameToken });
}
