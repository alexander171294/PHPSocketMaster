<?php namespace PHPSocketMaster;


class newClient extends SocketEventReceptor
{

	private $name = 'Noname';
	private $requested = false;
	public $id;

	public function onError()
	{
		echo '> oOps error in client: '.$this->name;
		// borramos el cliente con el error
		ServerManager::DeleteClient($this->id); 
	}

	public function onConnect()
	{
		echo '> New client...';
	}

	public function onDisconnect()
	{
		echo '> disconnect client: '.$this->name;
		ServerManager::DeleteClient($this->id);
	}

	public function onReceiveMessage($message)
	{
		
		// fix for windows sockets message
		$message = isset($message[0]) ? $message[0] : $message;
		// send
		ServerManager::Resend('HI!');
		// mostramos en pantalla lo que llegó
		echo '>'.$message."\r\n";
	}
    
    public function onSendRequest(&$cancel, $message) 
    {
        //...
    }
    
    public function onSendComplete() 
    {
        //... 
    }
}
