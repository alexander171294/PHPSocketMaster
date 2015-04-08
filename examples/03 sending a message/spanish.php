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
    // supongamos que ahora le queremos responder el mensaje
    // usamos la funcion para obtener el puente
    // y luego la funcion para enviar un mensaje
    $this->getBridge()->send('Hola');
    // la otra forma es desde fuera de los eventos, 
    // puede observar un ejemplo más abajo
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
  
  // esta funcion la veremos más adelante
  public function onRefresh(){}
  
}

$sock = new Socket('localhost', '2026');
$sock->connect();
// suponiendo que quicieramos enviar un mensaje ahora mismo
$sock->send('mi mensaje');
$sock->loop_refresh();