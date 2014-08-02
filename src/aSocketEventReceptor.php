<?php

abstract class SocketEventReceptor
{

	public $socket;

	// cambiar esto por constructor //////////////////////////////////////////
	public function setMother(SocketBridge $mother)
	{
		$this->socket = $mother;
	}

	abstract public function onError();
	abstract public function onConnect();
	abstract public function onDisconnect();
	abstract public function onReceiveMessage($message);
}