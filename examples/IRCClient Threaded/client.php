<?php namespace PHPSocketMaster;

// THIS SOURCE DON'T WORK!!!!
// Este código no funciona

/**
 ** En este momento aún estoy trabajando con sockets en hilos, al menos con las funciones nativas
 ** dadas las limitaciones de Pthreads, aún con mucho esfuerzo no logro compartir el recurso de 
 ** socket entre los dos contextos que necesito, el recurso pierde el tipo y deja de funcionar
 **
 ** Si usted desea utilizar sockets con hilos, hay una solución para windows que será implementada
 ** próximamente.
 
 ** El manejo de hilos de PHPSocketMaster aún es una característica experimental y recién comienzo con el desarrollo
 **
 */

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
    $var = fgets(fopen('php://stdin', 'r'));
    var_dump($var);
    exit();
    return ($var !== null) ? trim($var) : null;
}

// create a new socket
$sock = new ircClient('irc.freenode.net', 6667, 'JhonDoe99');

// connect to localhost server
$sock->connect();

$channel = null;

$text = null;
$free = true;

sleep(7);
/*


do
{
    // info:
    echo '? Put /exit for end client and put ! to refresh';
    // get text
	$text = $free ? getInputText() : null;
    
    // refresh or send menssage?
	if($text!='/exit' and $text!='' and strpos($text,'/join')===false and strpos($text,'/nick')===false and $text!='/free' )
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
	
	if ($text == '/free')
	{
		$free = false;
	}
    
    var_dump($text);
    
} while ($text != '/exit');



	*/

$sock->send('JOIN #underc0de');

echo 'Waiteando';
// wait for end thread.
while($sock->endLoop == false) {}

	
	
	
	
