<?php

/** PHPSocketMaster tiene soporte para funciones Listen, ListenRefresh
 *  y para crear un servidor completo de sockets a la escucha, y hasta para crear
 *  un servidor mixto de sockets y websockets.
 *  
 *  Originalmente incluimos ejemplos para que usted vea como podría ser creado un
 *  servidor de sockets en php para recibir muchas conexiones.
 *  
 *  No obstante, actualmente se está desarrollando PHPServerSocket una librería
 *  complementaria que facilita muchisimo la creación de servidores, además de 
 *  proveer los mecanismos necesarios para mantener la estabilidad del servidor.
 *  
 *  Si usted aún asi decea crear su servidor, y no tiene intención de utilizar 
 *  PHPServerSocket puede revisar los ejemplos de la carpeta OLD
 *  Y la documentación que se provee en la wiki del proyecto.
 *  
 *  Si usted solo decea crear un socket client-client donde el que se encuentre
 *  a la escucha solo pueda recibir un socket y no más de uno, puede ver el
 *  siguiente ejemplo.
 */
 
require('../../src/iSocketMaster.php');
 
// socket a la escucha
class Listener extends \PHPSocketMaster\SocketMaster
{
	// nunca se ejecutarán estas funciones (se escriben por compatibilidad):
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
		die(); // finalizamos la ejecución
	}

	public function onNewConnection(\PHPSocketMaster\SocketBridge $socket)
	{
      /* aquí obtenemos que se conectó un nuevo cliente, y el objeto socket al que pertenece
         si fuera un sistema con multiples conexiones deberíamos enviar este socket
         al gestor de clientes.
         Pero dado que en el ejemplo es solo un cliente, no necesitamos
         que un gestor se encargue de refrezcar todos los clientes
         de modo que podemos simplemente meter el bucle directamente aquí */
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
    public function onConnect() {}
    
    // cuando se desconecte
    public function onDisconnect() {}
    
    // cuando se recibe un mensaje
    public function onReceiveMessage($message) { echo $message; }
    
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

// refrezcamos hasta recibir la conexión
while($listener->refreshListen($receptor) == false){}

/** puede crear un socket a la escucha para un websocket agregando un parametro SCKM_WEB y cambiando
 *  una de las clases, si decea recibir conexiones de websocket por favor revise la documentación
 *  ubicada en la wiki
 */