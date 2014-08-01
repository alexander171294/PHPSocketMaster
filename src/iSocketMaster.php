<?php

// requerimos la clase socketMaster
require('SocketMaster.php');
// cambiar por su interface;
require('aSocketEventReceptor.php');
// cambiar por su interface;
require('iSocketBridge.php');

// interface SocketMaster
interface iSocketMaster
{

	// Socket Constructor
	//public function __construct($address, $port);

	// Socket Destructor
	public function __destruct();

	// wait for a new external connection request
	public function listen();

	// connect to host
	public function connect();

	// accept a new external connection and create new socket object
	/**
		@params: SocketEventReceptor $callback :: instancia de clase que ejecutara los eventos del socket creado
		@return: object of SocketBridge
	*/
	public function accept(SocketEventReceptor $Callback);

	//send message by socket
	public function send($message);

	//detect new messages
	public function refresh();

	// call to be connected
	//abstract private function onConnect();
	// call to be disconnected
	//abstract private function onDisconnect();
	// call to receive message
	//abstract private function onReceiveMessage($message);
	// call on error xD
	//abstract private function onError($errorMessage); 
}