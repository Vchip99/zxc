var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);

server.listen(8890);

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