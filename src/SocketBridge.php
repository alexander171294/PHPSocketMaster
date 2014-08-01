<?php

class SocketBridge extends SocketMaster implements iSocketBridge
{
	private $obj = null;

	public function __construct($socket, SocketEventReceptor $callback) 
	{ 
		$this->obj = $callback;
		$this->obj->setMother($this);
		$this->socketRef = $socket; 
		$this->onConnect(); 
	}

	public function onError($errorMessage)
	{
		if($obj == null) throw new exception('Not Set Callback in Socket Bridge');
		$this->obj->onError($errorMessage);
	}

	public function onConnect()
	{
		if($obj == null) throw new exception('Not Set Callback in Socket Bridge');
		$this->obj->onConnect();
	}

	public function onDisconnect()
	{
		if($obj == null) throw new exception('Not Set Callback in Socket Bridge');
		$this->obj->onDisconnect();
	}

	public function onReceiveMessage($message)
	{
		$this->obj->onReceiveMessage($message);
	}

}