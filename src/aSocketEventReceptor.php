<?php namespace PHPSocketMaster;

/**
 * @abstract SocketEventReceptor
 * @author Alexander
 * @version 1.0
 * Clase dise�ada para crear receptores de eventos para 
 * sockets creados por un listen.
 * 
 * Cuando se recive una conexion entrante, el socket la acepta
 * creando otro socket para manipular esa conexion en particular.
 * en ese caso, los eventos que le ocurran ser�n ejecutados en una clase
 * que es extendida de esta.
 * 
 * @example server-chat newClient.php
 */
abstract class SocketEventReceptor
{

	/**
	 * Var $bridge
	 * @var object instance of SocketBridge
	 * contiene la instancia del puente, para poder ejecutar
	 * acciones sobre el socket, cuando ocurre un evento.
	 */
	private $bridge = null;

	/**
	 * Function setMother
	 * @param SocketBridge $bridge
	 * @return none
	 */
	final public function setMother(SocketBridge &$bridge)
	{
		$this->bridge = $bridge;
	}

	/**
	 * Function Getter getBridge
	 * @return object instance of SocketBridge
	 * function dise�ada para obtener la instancia del puente
	 * de forma que se puedan ejecutar acciones sobre el socket.
	 */
	final public function getBridge() { return $this->bridge; }

	/**
	 * Abstract Functions overwritables
	 */
	abstract public function onError();
	abstract public function onConnect();
	abstract public function onDisconnect();
	abstract public function onReceiveMessage($message);
}