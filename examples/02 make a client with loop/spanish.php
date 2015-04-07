<?php

// cargamos phpSocketMaster
require('../../src/iSocketMaster.php');

/**
 * @wiki https://github.com/alexander171294/PHPSocketMaster/wiki/onEvent-Funciones
 */

class Socket extends SocketMaster
{
	
	public function onConnect()
	{
		echo '> Conectado correctamente';
	}

	public function onDisconnect()
	{
		echo '> desconectado :(';
	}

	public function onReceiveMessage($message)
	{
		echo '< '.$message;
	}

	public function onError($errorMessage)
	{
		echo 'Oops error ocurred: '.$errorMessage;
		die();
	}

	public function onNewConnection(SocketBridge $socket) { }
  
  public function onSendRequest(&$cancel, $message) 
  {

  }
   
  public function onSendComplete($message) 
  {
   
  }
  
}

$sock = new Socket('localhost', '2026');
$sock->connect();

// a partir de este punto, el socket se actualizará y funcionará bajo las 
// funciones establecidas en la clase de arriba
//$socket->loop_refresh();

/**
 * Podemos desarrollar nuestro propio bucle para hacer alguna accion entre cada refrezco del estado del bucle
 */
 
 while(true)
 {
    // hacer alguna acción aquí
    $socket->refresh();
 }