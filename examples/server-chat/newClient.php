<?php

class newClient extends SocketEventReceptor
{

	private $name = 'Noname';
	private $requested = false;

	private function onError()
	{
		echo '> oOps error in client: '.$this->name;
	}

	private function onConnect()
	{
		echo '> New client... Requesting Name';
		$this->Socket->send('What is your nick?');
	}

	private function onDisconnect()
	{
		echo '> disconnect client: '.$this->name;
	}

	private function onReceiveMessage($message)
	{
		if($requested)
		{
			// aca tengo el mensaje
		} else { $this->name = $message; $this->socket->send('Hello '.$this->name.', welcome to example chat server v1.0'); }
	}
}