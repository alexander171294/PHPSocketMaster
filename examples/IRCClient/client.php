<?php

// Cliente de ejemplo
require('../../src/iSocketMaster.php');

// import implementation of SocketMaster as Socket
include('socket.php');
// import implementation of SocketMaster as Socket
include('irc.php');

// enter a text in console
function getInputText()
{
	return trim(fgets(STDIN));
}

// create a new socket
$sock = new ircClient('irc.freenode.net', 6667, 'JhonDoe99');

// connect to localhost server
$sock->connect();

$channel = null;

// info:
echo '? Put /exit for end client and put ! to refresh';

$text = null;

// while not exist
do
{
	// new messages?
	echo "\n".'** refreshing'."\n";
	$sock->refresh();
	// get text
	$text = getInputText();
	// refresh or send menssage?
	if($text!='/exit' and $text!='' and strpos($text,'/join')===false and strpos($text,'/nick')===false )
		$sock->sendToChannel($channel, $text); //send text :)
	if(strpos($text,'/join')!==false)
	{
		if(!empty($channel)) $sock->leaveChannel($channel);
		$channel = str_replace('/join ', null, $text);
		$sock->unirCanal($channel);
	}
	
	if(strpos($text,'/nick')!==false)
	{
		$sock->changeNick(str_replace('/nick ', null, $text));
	} 
	
	if($text == '/exit')
	{
		$sock->eexit();
	}
} while ($text != '/exit');