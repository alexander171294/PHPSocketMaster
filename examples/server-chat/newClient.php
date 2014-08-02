<?php

class newClient extends SocketEventReceptor
{

	private $name = 'Noname';
	private $requested = false;

	public function onError()
	{
		echo '> oOps error in client: '.$this->name;
	}

	public function onConnect()
	{
		echo '> New client... Requesting Name';
		parent::getBridge()->send('What is your nick?');
	}

	public function onDisconnect()
	{
		echo '> disconnect client: '.$this->name;
	}

	public function onReceiveMessage($message)
	{
		if($requested)
		{
			// aca tengo el mensaje
		} else { $this->name = $message; $this->socket->send('Hello '.$this->name.', welcome to example chat server v1.0'); }
	}
}