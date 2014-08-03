<?php

// Cliente de ejemplo
require('../../src/iSocketMaster.php');

// import implementation of SocketMaster as Socket
include('socket.php');

// enter a text in console
function getInputText()
{
	return trim(fgets(STDIN));
}

// create a new socket
$sock = new Socket('localhost', '2026');

// connect to localhost server
$sock->connect();

// info:
echo '? Put /exit for end client and put ! to refresh';

$text = null;

// while not exist
while($text != '/exit')
{
	// new messages?
	echo "\n".'** refreshing'."\n";
	$sock->refresh();
	// get text
	$text = getInputText();
	// refresh or send menssage?
	if($text!='/exit' and $text!='!')
		$sock->send($text); //send text :)
}