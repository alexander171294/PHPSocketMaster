<?php

/** PHPSocketMaster tiene soporte para funciones Listen, ListenRefresh
 *  y para crear un servidor completo de sockets a la escucha, y hasta para crear
 *  un servidor mixto de sockets y websockets.
 *  
 *  Originalmente incluimos ejemplos para que usted vea como podr�a ser creado un
 *  servidor de sockets en php para recibir muchas conexiones.
 *  
 *  No obstante, actualmente se est� desarrollando PHPServerSocket una librer�a
 *  complementaria que facilita muchisimo la creaci�n de servidores, adem�s de 
 *  proveer los mecanismos necesarios para mantener la estabilidad del servidor.
 *  
 *  Si usted a�n asi decea crear su servidor, y no tiene intenci�n de utilizar 
 *  PHPServerSocket puede revisar los ejemplos de la carpeta OLD
 *  Y la documentaci�n que se provee en la wiki del proyecto.
 *  
 *  Si usted solo decea crear un socket client-client donde el que se encuentre
 *  a la escucha solo pueda recibir un socket y no m�s de uno, puede ver el
 *  siguiente ejemplo.
 */
 
require('../../src/iSocketMaster.php');
 
// socket a la escucha
class Listener extends \PHPSocketMaster\SocketMaster
{
	// nunca se ejecutar�n estas funciones (se escriben por compatibilidad):
	public function onConnect() {}
  public function onRefresh() {}
	public function onDisconnect()	{}
	public function onReceiveMessage($message) {}
  public function onSendRequest(&$cancel, $message) {}
  public function onSendComplete($message) {}

	// cuando ocurre un error
	public function onError($errorMessage)
	{
		echo 'Error ocurrido'.$errorMessage;
		die(); // finalizamos la ejecuci�n
	}

	public function onNewConnection(\PHPSocketMaster\SocketBridge $socket)
	{
      /* aqu� obtenemos que se conect� un nuevo cliente, y el objeto socket al que pertenece
         si fuera un sistema con multiples conexiones deber�amos enviar este socket
         al gestor de clientes.
         Pero dado que en el ejemplo es solo un cliente, no necesitamos
         que un gestor se encargue de refrezcar todos los clientes
         de modo que podemos simplemente meter el bucle directamente aqu� */
         // refrezcamos el nuevo cliente
         $socket->loop_refresh();
         /*
           si nosotros luego mantenemos el bucle del hilo principal (al final del codigo de este archivo)
           de forma infinita sin terminarlo, podremos crear reconexiones
         */
	}
     
}

// creamos el receptor
class receptor extends \PHPSocketMaster\SocketEventReceptor
{
    // cuando ocurra un error en nuestro cliente
    public function onError() {}
    
    // cuando se logre establecer la conexion recibida
    public function onConnect() 
    {
      /*
       a partir de la versi�n actual ya no es necesario utilizar
       getBridge para enviar mensajes en el listener, si desea enviar un mensaje
       en uno de estos eventos solo debe poner:
       $this->send('mimensaje')
      */
    }
    
    // cuando se desconecte
    public function onDisconnect() {}
    
    // cuando se recibe un mensaje
    public function onReceiveMessage($message) { var_dump($message); }
    
    // cuando se quiere enviar un mensaje al cliente
    public function onSendRequest(&$cancel, $message) {}
    
    // cuando se envio correctamente el mensaje al cliente
    public function onSendComplete($message) {}
    
    // se ejecuta cada segundo
    public function onRefresh() {}
}

/// creamos la instancia pasando por parametro el dominio o ip local y el puerto
$listener = new Listener('localhost', '2026');

// ponemos a la escucha
$listener->listen(); 

// creamos el receptor
$receptor = new receptor();

// refrezcamos hasta recibir la conexi�n
while($listener->refreshListen($receptor) == false){}

/** puede crear un socket a la escucha para un websocket agregando un parametro SCKM_WEB y cambiando
 *  una de las clases, si decea recibir conexiones de websocket por favor revise la documentaci�n
 *  ubicada en la wiki
 */