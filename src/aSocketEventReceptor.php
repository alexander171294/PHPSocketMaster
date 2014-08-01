<?php

abstract class SocketEventReceptor
{

	private $Socket;

	// cambiar esto por constructor //////////////////////////////////////////
	public function setMother(SocketBridge $mother)
	{
		$this->Socket = $mother;
	}

	abstract public function onError();
	abstract public function onConnect();
	abstract public function onDisconnect();
	abstract public function onReceiveMessage($message);
}