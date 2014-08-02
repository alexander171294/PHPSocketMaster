<?php

class SocketBridge extends SocketMaster implements iSocketBridge
{
	private $obj = null;

	public function __construct($socket, SocketEventReceptor &$callback) 
	{ 
		$this->obj = $callback;
		$this->socketRef = $socket; 
	}

	public function onError($errorMessage)
	{
		if($this->obj == null) throw new exception('Not Set Callback in Socket Bridge');
		$this->obj->onError($errorMessage);
	}

	public function onConnect()
	{
		if($this->obj == null) throw new exception('Not Set Callback in Socket Bridge');
		$this->obj->onConnect();
	}

	public function onDisconnect()
	{
		if($this->obj == null) throw new exception('Not Set Callback in Socket Bridge');
		$this->obj->onDisconnect();
	}

	public function onReceiveMessage($message)
	{
		$this->obj->onReceiveMessage($message);
	}

}