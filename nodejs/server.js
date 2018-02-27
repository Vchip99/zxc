var fs = require('fs');
var https = require('https');
var express = require('express');
var app = express();

var options = {
    key: fs.readFileSync('/var/www/html/vchip/file.pem'),
    cert: fs.readFileSync('/var/www/html/vchip/file.crt')
};
var serverPort = 8080;

var server = https.createServer(options, app);
var io = require('socket.io')(server);
io.set('transports', ['websocket', 'polling']);
server.listen(serverPort);

// var fs = require( 'fs' );
// var app = require('express')();
// var https = require('https');
// var server = https.createServer({
//     key: fs.readFileSync('./test_key.key'),
//     cert: fs.readFileSync('./test_cert.crt'),
//     ca: fs.readFileSync('./test_ca.crt'),
//     requestCert: false,
//     rejectUnauthorized: false
// },app);

// var server = require('http').Server(app);
// var io = require('socket.io')(server);

// server.listen(8890);

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