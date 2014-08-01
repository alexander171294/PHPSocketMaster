<?php

class newClient extends SocketEventReceptor
{

	private $name = 'Noname';

	private function onError()
	{
		echo '> oOps error in client: '.$this->name;
	}

	private function onConnect()
	{
		echo '> New client... Requesting Name';
		
	}

	private function onDisconnect()

	private function onReceiveMessage($message)
}