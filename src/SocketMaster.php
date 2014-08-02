<?php

// clase principal de manipulacion de sockets
abstract class SocketMaster implements iSocketMaster
{

	protected $address = '000.000.000.000';
	protected $port = 0;
	protected $readcontrol = "\n";

	private $socketRef = null;

	public function __construct($address, $port)
	{
		try
		{
			// seteamos variables fundamentales
			$this->address = $address;
			$this->port = $port;
			// creamos el socket
			$this->socketRef = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if($this->socketRef == false) throw new exception('Failed to create socket :: '.$this->getError());
		} catch (exception $error) {
			$this->onError($error->getMessage());
		}
	}

	final public function __destruct()
	{
		try
		{
			if(!empty($this->socketRef))
				socket_close($this->socketRef);
			$this->onDisconnect();
		} catch (exception $error) {
			$this->onError($error->getMessage());
		}
	}

	// wait for a new external connection request
	final public function listen()
	{
		try
		{
			// bindeamos el socket
			if(socket_bind($this->socketRef, $this->address, $this->port) == false)
				throw new exception('Failed to bind socket :: '.$this->getError());
			if (socket_listen($this->socketRef, 5) === false)
				throw new exception('Failed Listening :: '.$this->getError());
		} catch (exception $error) {
			$this->onError($error->getMessage());
		}
	}

	// connect to host
	final public function connect()
	{
		try
		{
			if(socket_connect($this->socketRef, $this->address, $this->port)===false)
				throw new exception('Failed to connect :: '.$this->getError());
			$this->onConnect();
		} catch (exception $error) {
			$this->onError($error->getMessage());
		}
	}

	// accept a new external connection and create new socket object
	/**
		@params: SocketEventReceptor $callback :: instancia de clase que ejecutara los eventos del socket creado
		@return: object of SocketBridge
		*/
	final public function accept(SocketEventReceptor $Callback)
	{
		try
		{
			$newSocketRef = socket_accept($this->socketRef);
			if($newSocketRef === false) throw new exception('Socket Accept Failed :: '.$this->getError());
			$instance = new SocketBridge($newSocketRef, $Callback);
			$Callback->setMother($instance);
			$instance->onConnect();
			return $instance;
		} catch (exception $error) {
			$this->onError($error->getMessage());
		}
	}

	//send message by socket
	final public function send($message)
	{
		try
		{
			$message = $message.$this->readcontrol;
			if(socket_write($this->socketRef, $message, strlen($message)) == false)
				throw new exception('Socket Send Message Failed :: '.$this->getError());
		} catch (exception $error) {
			$this->onError($error->getMessage());
		}
	}

	//detect new messages
	final public function refresh()
	{
			$read = array($this->socketRef);
			$write = null;
			$exceptions = null;
			if(($result = socket_select($read, $write, $exceptions, 0)) === false)
				$this->onDisconnect();
			var_dump($result);
			if($result > 0) 
				$this->read();
	}

	// recive a message by socket
	final private function read()
	{
		try
		{
			if (false === ($buf = socket_read($this->socketRef, 2048, PHP_NORMAL_READ)))
				throw new exception('Socket Read Failed :: '.$this->getError());
			$this->onReceiveMessage($buf);
		} catch (exception $error) {
			$this->onError($error->getMessage());
		}
	}

	// call to be connected
	abstract public function onConnect();
	// call to be disconnected
	abstract public function onDisconnect();
	// call to receive message
	abstract public function onReceiveMessage($message);
	// call on error xD
	abstract public function onError($errorMessage); 

	final private function getError()
	{
		return socket_strerror(socket_last_error($this->socketRef));
	}

	final public function setSocketRef($sref) { $this->socketRef = $sref; }
}