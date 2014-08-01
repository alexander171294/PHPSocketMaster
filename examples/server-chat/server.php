<?php

// example of chat server cli.

require('../../src/iSocketMaster.php');

// import implementation of SocketMaster as Socket
include('Listen.php');
// import implementation of SocketEventReceptor as newClient
include('newClient.php');

// create a new socket
$sock = new Socket('localhost', '2025');

// array of clients online
$clients = array();

$sock->listen();
$client = new newClient();
$clients[] = $sock->accept($client);