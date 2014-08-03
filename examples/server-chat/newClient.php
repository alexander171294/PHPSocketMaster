<?php

class newClient extends SocketEventReceptor
{

	private $name = 'Noname';
	private $requested = false;
	public $id;

	public function onError()
	{
		echo '> oOps error in client: '.$this->name;
		// borramos el cliente con el error
		ServerManager::DeleteClient($id); 
	}

	public function onConnect()
	{
		echo '> New client... Requesting Name';
		parent::getBridge()->send('What is your nick?');
	}

	public function onDisconnect()
	{
		echo '> disconnect client: '.$this->name;
		ServerManager::DeleteClient($id);
	}

	public function onReceiveMessage($message)
	{
		if($this->requested)
		{
			ServerManager::Resend($this->name.': '.$message);
			// aca tengo el mensaje
		} else {$this->requested = true; $this->name = $message; parent::getBridge()->send('Hello '.$this->name.', welcome to example chat server v1.0'); }
	}
}