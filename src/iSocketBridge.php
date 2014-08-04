<?php namespace PHPSocketMaster;

// requerimos la clase socketMaster
require('SocketBridge.php');

/**
 * @abstract interface iSocketBridge
 * @author Alexander
 * @version 1.0
 * interface de SocketBridge
 * 
 * @example none
 */
interface iSocketBridge
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
	// @todo : esta funcion hay que quitarla, para eso está el property
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
	 * WARN!!: esta función es únicamente simbólica para cumplir con los requisitos
	 * del atributo, pero se maneja a dicho atributo como solo lectura por lo que esta función
	 * emite una excepcion catchable, de igual manera como la anterior no es necesario llamarla
	 * directamente, también al setear este atributo como si de un público se tratara, será llamada esta función
	 */
	public function set_SocketEventReceptor($val);
}