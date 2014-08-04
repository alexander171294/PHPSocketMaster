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
	public function getSocketEventReceptor();
}