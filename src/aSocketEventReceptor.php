<?php

abstract class SocketEventReceptor
{

	private $bridge;

	final public function setMother(SocketBridge &$bridge)
	{
		$this->bridge = $bridge;
	}

	final public function getBridge() { return $this->bridge; }

	abstract public function onError();
	abstract public function onConnect();
	abstract public function onDisconnect();
	abstract public function onReceiveMessage($message);
}