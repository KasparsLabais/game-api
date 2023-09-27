var playerList = {};
var gameInstance = {};

var socket = io('http://localhost:3000');
socket.on('connect', function(data) {
    socket.emit('userconnected', {'username': window.username, 'id': window.id});
});

const joinRoom = (gameToken) => {
    socket.to(gameToken).emit('playerJoined', { gameToken, playerList });
}

//game instance controller to handle socket io events
const addGameInstance = (gameToken, gameInstance) => {
    socket.emit('addOrUpdateGameInstance', { 'gameToken': gameToken, 'gameInstance':gameInstance });
}
const getGameInstance = (gameToken) => {
    socket.emit('getGameInstance', { gameToken });
}



socket.on('playerJoined', (data) => {

});