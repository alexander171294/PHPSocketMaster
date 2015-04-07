<?php

// cargamos phpSocketMaster
require('../../src/iSocketMaster.php');

/**
 * @wiki https://github.com/alexander171294/PHPSocketMaster/wiki/onEvent-Funciones
 */

class Socket extends SocketMaster
{

  use timeOut; // primero aclarmos que vamos a usar TimeOut

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
    // supongamos que ahora queremos que luego de recibir un mensaje espere 10 segundos
    // y ejecute una función que envie un mensaje diciendo "pasaron 10 segundos"
    // la siguiente funcion ejecutará la función "EnviarTiempo" de esta clase
    // esperando 10.0 segundos, y sin repetir (por eso el false)
    $this->setTimeOut(array($this, 'EnviarTiempo'), 10.0, false);
    // si cambiaramos ese false por un true cada 10 segundos a partir de este momento
    // se ejecutará la función enviarTiempo
	}
  
  // esta es la función que se ejecutará pasados 10 segundos de recibir un mensaje
  public function EnviarTiempo()
  {
      // ahora enviamos el mensaje hola
      $this->getBridge()->send('Hola');
  }
  
  // esta función se ejecuta cada muy muy poco tiempo, cada vez que se refrezca este socket
  public function onRefresh() 
  {
      // la usamos para revisar si se terminó el tiempo especificado 
      // para todas las funciones setTimeOut que hayan en el código
      $this->timeOut_refresh();
      // Este sistema permite establecer muchos setTimeOut para muchas funciones diferentes
      // o muchos setTimeOut para la misma función, es a gusto.
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
$socket->loop_refresh();