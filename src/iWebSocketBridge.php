<?php namespace PHPSocketMaster;

// requerimos la clase socketMaster
require('class/WebSocketBridge.php');

/**
 * @abstract interface iSocketBridge
 * @author Alexander
 * @version 1.0
 * interface de SocketBridge
 *
 * @example none
*/
interface iWebSocketBridge
{

	/**
	 * Function __construct of SocketBridge
	 * @param resource $socket of socket_create();
	 * @param SocketEventReceptor $callback
	 * @return object of SocketBridge class
	 */
	public function __construct($socket, SocketEventReceptor &$callback);

	/**
	 * Function getSocketEventReceptor
	 * @return object of SocketEventReceptor class
	*/
	// @todo : esta funcion hay que quitarla, para eso est� el property
	public function getSocketEventReceptor();

	/**
	 * Function get_SocketEventReceptor
	 * @return object of SocketEventReceptor class
	 * WARN: puede omitir el uso de esta funcion accediendo directamente al
	 * atributo socketEventReceptor (como si de un atributo publico se tratara)
	*/
	public function get_SocketEventReceptor();

	/**
	 * Function set_SocketEventReceptor
	 * @return none
	 * WARN!!: esta funci�n es �nicamente simb�lica para cumplir con los requisitos
	 * del atributo, pero se maneja a dicho atributo como solo lectura por lo que esta funci�n
	 * emite una excepcion catchable, de igual manera como la anterior no es necesario llamarla
	 * directamente, tambi�n al setear este atributo como si de un p�blico se tratara, ser� llamada esta funci�n
	*/
	public function set_SocketEventReceptor($val);
	
	/**
	 * Function send
	 * @param string $message
	 * enviar mensajes a traves del socket
	 */
	public function send($message);
	
	/**
	 * Function sendUnmasked
	 * @param string $message
	 * enviar mensajes a traves del socket sin enmascarar
	 */
	public function sendUnmasked($message);
	
	
}