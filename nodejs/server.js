var fs = require('fs');
var https = require('https');
var express = require('express');
var app = express();

var options = {
    key: fs.readFileSync('/etc/apache2/ssl/file.pem'),
    cert: fs.readFileSync('/etc/apache2/ssl/file.crt')
};
var serverPort = 8080;

var server = https.createServer(options, app);
var io = require('socket.io')(server);
io.set('transports', ['websocket', 'polling']);
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
});