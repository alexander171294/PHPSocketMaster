<?php namespace PHPSocketMaster;

// Cliente de ejemplo

// definimos que utilizaremos hilos
define('SCKM_THREAD', true);

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

$text = null;
$free = true;

sleep(5);

do
{
    // info:
    echo '? Put /exit for end client and put ! to refresh';
    // get text
	$text = $free ? getInputText() : null;
    
    // refresh or send menssage?
	if($text!='/exit' and $text!='' and strpos($text,'/join')===false and strpos($text,'/nick')===false and $text!='/free' )
		$sock->sendToChannel($channel, $text); //send text :)
    
    var_dump($text);
    
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
	
	if ($text == '/free')
	{
		$free = false;
	}
    
} while ($text != '/exit');



	
	
	
	
	
