var fs = require('fs');
var http = require('http');
var express = require('express');
var app = express();

// var options = {
//     // key: fs.readFileSync('/etc/apache2/ssl/file.pem'),
//     // cert: fs.readFileSync('/etc/apache2/ssl/file.crt')
// };
var serverPort = 8080;

var server = http.Server(app);
var io = require('socket.io')(server);
// io.set('transports', ['websocket', 'polling']);
server.listen(serverPort);

io.sockets.on('connection', function(socket){
    socket.on('subscribe', function(room) {
        // console.log('joining room', room);
        socket.join(room);
        socket.room = room;
    })

    socket.on('unsubscribe', function(room) {
        // console.log('leaving room', room);
        socket.leave(room);
    })

    socket.on('send', function(data) {
        // console.log('sending message');
        io.sockets.in(data.room).emit('message', data);
    });

    socket.on('sendClient', function(data) {
        // console.log('sending client message');
        io.sockets.in(data.room).emit('clientMessage', data);
        io.sockets.in(data.room).emit('clientMessageCount', data);
    });

    socket.on('sendClientUser', function(data) {
        // console.log('sending client user message');
        io.sockets.in(data.room).emit('clientMessage', data);
        io.sockets.in(data.room).emit('clientUserMessageCount', data);
    });

    socket.on('clientSubscribe', function(room) {
        // console.log('joining client room', room);
        socket.join(room);
        socket.room = room;
    })
});