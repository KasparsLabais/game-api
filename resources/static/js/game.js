var playerList = {};
var gameInstance = {};

let socketServer = 'https://poker-bank.com';
if(window.location.hostname == 'localhost' || window.location.hostname == 'trivia.test') {
    socketServer = 'http://localhost:5000';
}

//var socket = io('http://localhost:3000');
var socket = io(socketServer);
socket.on('connect', function(data) {
    console.log('connect', data);
    socket.emit('userconnected', {'username': window.username, 'id': window.id, 'avatar': window.avatar, 'playerToken' : window.playerToken });
});
socket.on('playerJoined', (data) => {
    console.log('playerJoined', data);
    playerJoined(data);
});

socket.on('gameInstanceUpdated', (data) => {
    console.log('gameInstanceUpdated', data);
    gameInstance = data.gameInstance;
    //Change callbackgameInstanceUpdate to dispatchEvent
    document.dispatchEvent(new CustomEvent(data.action, { detail: data }));
    //callbackGameInstanceUpdated(data.gameToken, data.gameInstance, data.action);
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
    console.log('notifyGameMaster', data.data.action);
    document.dispatchEvent(new CustomEvent(data.data.action, { detail: data.data.data }));
});

socket.on('notifyRoom', (data) => {
    console.log('notifyRoom', data);
    document.dispatchEvent(new CustomEvent(data.data.action, { detail: data.data.payload }));
});

socket.on('alertNotification', (data) => {
    console.log('alertNotification', data);
    GameApi.displayAlertNotification(data.notificationType, data.message);
});

socket.on('userconnected', (data) => {
    console.log('userconnected', data);
    document.dispatchEvent(new CustomEvent('userconnected', { detail: data }));
});

const GameApi = {
    'openModal': function(target, headerTitle, bodyHtml) {

        let modalContent = document.querySelector(".game_modal-content[target='" + target + "']");

        let modalHeader = modalContent.querySelector(".game_modal-header");
        let modalBody = modalContent.querySelector(".game_modal-body");

        modalHeader.innerHTML = headerTitle;
        modalBody.innerHTML = '';

        modalBody.appendChild(bodyHtml);

        document.querySelector(".game_modal-holder[target='" + target + "']").classList.remove('hidden');
    },
    'closeModal': function(target) {
        document.querySelector(".game_modal-holder[target='" + target + "']").classList.add('hidden');
    },
    'joinRoom': function(gameToken, repeatCount = 0) {
        socket.emit('joinRoom', { 'gameToken' : gameToken, 'repeatCount' : repeatCount, 'playerToken': window.playerToken}, (answer) => {
            if (!answer.status && answer.repeatCount  < 10) {
                setTimeout(() => { console.log('Repeat'); GameApi.joinRoom(gameToken, answer.repeatCount); }, 1000);
            }
        });
    },
    'joinGameInstance': function(gameToken, redirectUrl, repeatCount = 0) {
        console.log('joinGameInstance', gameToken, redirectUrl, repeatCount);
        socket.emit('joinGameInstance', { 'gameToken' : gameToken, 'repeatCount' : repeatCount, 'playerToken': window.playerToken}, (answer) => {
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
        socket.emit('addOrUpdateGameInstance', { 'gameToken': gameToken, 'gameInstance':game, 'playerToken': window.playerToken});
    },
    'getGameInstance': function(gameToken) {
        console.log('getGameInstance', gameToken);
        socket.emit('getGameInstance', { gameToken });
    },
    'updateGameInstance': function(gameToken, gameInstance, action = 'updateGameInstanceStatus') {
        console.log('updateGameInstance', gameToken, gameInstance);
        socket.emit('updateGameInstance', { 'gameToken': gameToken, 'gameInstance':gameInstance, 'action': action, 'playerToken': window.playerToken});
    },
    'updatePlayerInstance': function(gameToken, playerInstance, action = 'emptyAction') {
        console.log('updatePlayerInstance', gameToken, playerInstance);
        socket.emit('updatePlayerInstance', { 'gameToken': gameToken, 'playerInstance':playerInstance, 'action': action, 'playerToken': window.playerToken});
    },
    'redirect': function(gameToken, url) {
        console.log('redirect', gameToken, url);
        socket.emit('redirect', { 'gameToken': gameToken, 'url': url });
    },
    'notifyGameMaster': function(gameToken, data) {
        console.log('notifyGameMaster', gameToken, data);
        socket.emit('notifyGameMaster', { 'gameToken': gameToken, 'data': data, 'playerToken': window.playerToken });
    },
    'notifyRoom': function(gameToken, data) {
        console.log('notifyRoom', gameToken, data);
        socket.emit('notifyRoom', { 'gameToken': gameToken, 'data': data, 'playerToken': window.playerToken });
    },
    'updatePointsUI': function(points) {
        console.log('updatePointsUI', points);
        let pointsHolder = document.getElementById('game-api-_points-holder');
        pointsHolder.innerHTML = points;
    },
    'updateGameInstanceSettings' : function(gameToken, gameInstanceSettings) {
        console.log('updateGameInstanceSettings', gameToken, gameInstanceSettings);
        socket.emit('updateGameInstanceSettings', { 'gameToken': gameToken, 'gameInstanceSettings': gameInstanceSettings, 'playerToken': window.playerToken });
    },
    'updateGameInstanceSetting' : function(gameToken, $key, $value) {
        fetch('/api/game-instance-setting', {'method': 'POST', 'body': JSON.stringify({'gameToken': gameToken, 'key': $key, 'value': $value}), 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}})
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                console.log('updateGameInstanceSetting', gameToken, $key, $value);
                socket.emit('updateGameInstanceSetting', { 'gameToken': gameToken, 'key': $key, 'value': $value, 'playerToken': window.playerToken });
            });
    },
    'triggerAlertNotification' : function(gameToken, messageType, notificationType, message, playerId = 0) {
        console.log('triggerAlertNotification', gameToken, messageType, notificationType, message);
        socket.emit('triggerAlertNotification', { 'gameToken': gameToken, 'messageType': messageType, 'notificationType': notificationType, 'message': message, 'playerToken': window.playerToken, 'playerId' : playerId });
    },
    'displayAlertNotification' : function(notificationType, message) {
        console.log('displayAlertNotification', notificationType, message);
        let alertHolder = document.getElementById('notification-box__' + notificationType);
        alertHolder.classList.remove('hidden');

        let alertMessageHolder = document.getElementById('notification-box__' + notificationType + '__message');
        alertMessageHolder.innerHTML = message;

        setTimeout(() => {
            GameApi.removeAlertNotification(notificationType);
        }, 5000);
    },
    'removeAlertNotification' : function(notificationType) {
        console.log('removeAlertNotification', notificationType);
        let alertHolder = document.getElementById('notification-box__' + notificationType);
        alertHolder.classList.add('hidden');

        let alertMessageHolder = document.getElementById('notification-box__' + notificationType + '__message');
        alertMessageHolder.innerHTML = '';
    }
}
