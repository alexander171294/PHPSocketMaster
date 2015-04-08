<?php

// cargamos phpSocketMaster
require('../../src/iSocketMaster.php');

/**
 * @wiki https://github.com/alexander171294/PHPSocketMaster/wiki/onEvent-Funciones
 */

class Socket extends \PHPSocketMaster\SocketMaster
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

	public function onNewConnection(\PHPSocketMaster\SocketBridge $socket) { }
  
  public function onSendRequest(&$cancel, $message) 
  {

  }
   
  public function onSendComplete($message) 
  {
   
  }
  
  // esta funcion la veremos m�s adelante
  public function onRefresh(){}
  
}

$sock = new Socket('localhost', '2026');
$sock->connect();

// a partir de este punto, el socket se actualizar� y funcionar� bajo las 
// funciones establecidas en la clase de arriba
//$socket->loop_refresh();

/**
 * Podemos desarrollar nuestro propio bucle para hacer alguna accion entre cada refrezco del estado del bucle
 */
 
 while(true)
 {
    // hacer alguna acci�n aqu�
    $sock->refresh();
 }