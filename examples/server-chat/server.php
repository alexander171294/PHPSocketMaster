<?php

// example of chat server cli.

require('../../src/iSocketMaster.php');

// import implementation of SocketMaster as Socket
include('Listen.php');
// import implementation of SocketEventReceptor as newClient
include('newClient.php');

// create a new socket
$sock = new Socket('localhost', '2026');

// array of clients online
$clients = array();


$sock->listen(); 
echo '** listen **';
while(true)
{
	$client = new newClient();
	$clients[] = $sock->accept($client); //** Abría que cambiar por el selectSocket para ver si hay conexiones entrantes
	// o ver que hacer para que se pueda hacer refresh a los clientes sin tener que esperar a que esto pase
	echo '** enter-client **';
}