<?php namespace PHPSocketMaster;

// Dependencias principales
require('resources/property.php');
require('resources/singleton.php');
require('class/SocketMaster.php');
require('class/aSocketEventReceptor.php');
require('iSocketBridge.php');
require('iWebSocketBridge.php');

/**
 * @abstract SocketMaster
 * @author Alexander
 * @version 1.0
 * @wiki https://github.com/alexander171294/PHPSocketMaster/wiki/Funciones-del-Socket
 * Clase diseñada como modelo de socket orientado a objetos
 * con eventos.
 * Cuenta con la finalidad de escuchar y conectarse.
 *
 * @example server-chat listen.php
 * @example client-chat socket.php
 */
// interface SocketMaster
interface iSocketMaster
{

	// Socket Constructor
	/**
	 * Function __construct
	 * @param string $address
	 * @param integer $port
	 * funcion constructora del socket
	 */
	//public function __construct($address, $port);

	/**
	 * Function __destruct
	 * funcion destructora del socket
	 */
	// Socket Destructor
	public function __destruct();

	/**
	 * Function listen
	 * prepare and listen a one port.
	 * prepara y pone a la escucha sobre un puerto (el establecido al crear el objeto)
	 */
	public function listen();
	
	/**
	 * Function accept
	 * 
	 * @params: SocketEventReceptor $callback :: instancia de clase que ejecutara los eventos del socket creado
	 * @return: object of SocketBridge
	 * wait and accept a new external connection and create new socket object (instance of SocketBridge). 
	 * espera y acepta conexiones externas y luego crea una nueva instancia de SocketMaster (su extension Socket Bridge)
	 * es necesario pasar por parametro una instancia de la clase que recibirá los eventos y ejecutará las tareas cuando 
	 * ocurran sobre el nuevo socket que se crea al aceptar una conexion (usado luego para gestionar dicha conexion)
	*/
	public function accept(SocketEventReceptor $Callback);
	

	/**
	 * Function connect
	 * connect to host
	 * conectarse a un server (establecido al crear la instancia)
	 */
	public function connect();
	
	/**
	 * Function disconnect
	 * disconnect of host
	 * desconectarse del server actual (mantiene las conexiones para hacer una reconeccion luego)
	 */
	public function disconnect();

	/**
	 * Function send
	 * @param string $message
	 * @param bool $readControl
	 * send a message by socket
	 * enviar un mensaje por el socket
	 */
	public function send($message, $readControl);

	/**
	 * Function refresh
	 * detect new received messages, and call onReceiveMessage
	 * detectar nuevos mensajes recibidos y ejecutar onReceiveMessage
	 * @return bool ¿new messages? true/false
	 */
	public function refresh();

	/**
	 * Function refreshListen
	 * @param SocketEventReceptor $Callback
	 * detect new incomming connections, and call accept using with args $callback
	 * detectar nuevas conecciones entrantes y llamar a la funcion accept usando como argumentos $callback
	 */
	public function refreshListen(SocketEventReceptor $Callback);

	
	// GETTERS
	/**
	 * Function get_address
	 * @return string of ip or host name
	 * WARN: puede omitir el uso de esta funcion accediendo directamente al
	 * atributo socketEventReceptor (como si de un atributo publico se tratara)
	 */
	public function get_address();
	/**
	 * Function get_port
	 * @return integer of port
	 * WARN: puede omitir el uso de esta funcion accediendo directamente al
	 * atributo socketEventReceptor (como si de un atributo publico se tratara)
	 */
	public function get_port();

	/**
	 * Function get_socketRef
	 * @return EXCEPTION
	 * WARN: esta función es únicamente simbólica, por el momento solo arrojará una excepcion
	 */
	// change to private for the moment
	//public function get_socketRef();
	
	// AND SETTERS :)
	/**
	 * Function set_address
	 * @param string $val new value for the property
	 * WARN: puede omitir el uso de esta funcion accediendo directamente al
	 * atributo socketEventReceptor (como si de un atributo publico se tratara)
	 */
	public function set_address($val);
	
	/**
	 * Function set_port
	 * @param integer $val new value for the property
	 * WARN: puede omitir el uso de esta funcion accediendo directamente al
	 * atributo socketEventReceptor (como si de un atributo publico se tratara)
	 */
	public function set_port($val);
	
	/**
	 * Function set_socketRef
	 * @return EXCEPTION
	 * WARN: esta función es únicamente simbólica, por el momento solo arrojará una excepcion
	 */
	// change to private for the moment
	//public function set_socketRef($val);
	
	// call to be connected
	// funcion llamada al conectarse
	//abstract private function onConnect();
	
	// call to be disconnected
	// funcion llamada al desconectarse
	//abstract private function onDisconnect();
	
	// call to receive message
	// funcion llamada al recivir un mensaje
	//abstract private function onReceiveMessage($message);
	
	// call on error xD
	// funcion llamada al detectar un error el socket
	//abstract private function onError($errorMessage);
	
	// call on new connection accepted by listen
	// funcion llamada al aceptar una nueva conexion cuando se está a la escucha con listen.
	//abstract public function onNewConnection(SocketBridge $socket); 
}