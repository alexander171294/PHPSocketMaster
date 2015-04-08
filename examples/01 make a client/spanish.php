<?php

// cargamos phpSocketMaster
require('../../src/iSocketMaster.php');

/**
 * @wiki https://github.com/alexander171294/PHPSocketMaster/wiki/onEvent-Funciones
 */

// ejemplo de la implementaci�n de un socket
class Socket extends \PHPSocketMaster\SocketMaster
{
	// funcion que se ejecuta cuando se conecta el socket
	public function onConnect()
	{
    // nos conectamos correctamente
		echo '> Conectado correctamente';
	}

	// al desconectarse el cliente
	public function onDisconnect()
	{
		echo '> desconectado :(';
	}

	// al recibir un mensaje del servidor o socket en modo escucha
	public function onReceiveMessage($message)
	{
    // mostramos el mensaje
		echo '< '.$message;
	}

	// funcion al ocurrir un error
	public function onError($errorMessage)
	{
    // mostramos el mensaje de error
		echo 'Oops error ocurred: '.$errorMessage;
		die(); // finalizamos la ejecuci�n
	}

  // esta funci�n en este momento no nos interesa, sirve para socket en modo escucha (como servidor)
	public function onNewConnection(\PHPSocketMaster\SocketBridge $socket) { }
  
  // esta funcion es ejecutada cuando nuestro cliente quiere enviar un mensaje
  // permite cancelar el envio del mensaje cambiando el valor de la variable $cancel a false  
  public function onSendRequest(&$cancel, $message) 
  {

  }
  
  // esta funcion es ejecutada cuando se envia correctamente un mensaje desde nuestro cliente  
  public function onSendComplete($message) 
  {
   
  }
  
  // esta funcion la veremos m�s adelante
  public function onRefresh(){}
  
}

// ahora creamos una instancia de nuestro socket
// establecemos la ip o dominio al cual nos queremos conectar
// y el puerto
$sock = new Socket('localhost', '2026');

// hacemos efectiva la conexi�n
$sock->connect();

// a partir de este punto, el socket se actualizar� y funcionar� bajo las 
// funciones establecidas en la clase de arriba
$sock->loop_refresh();