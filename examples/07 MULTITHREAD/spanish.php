<?php

// definimos que utilizaremos hilos
define('SCKM_THREAD', true);

// cargamos phpSocketMaster
require('../../src/iSocketMaster.php');

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
  
  public function onRefresh() 
  {
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
  
}

$sock = new Socket('localhost', '2026');
$sock->connect();

/* esperamos a que el hilo del socket termine (podemos hacer otras cosas mientras tanto
   pero para simplificar el ejemplo, vamos a dejar un bucle infinito que corre en primer plano
   y el socket corriendo en segundo plano) */
while($sock->endLoop == false) {}