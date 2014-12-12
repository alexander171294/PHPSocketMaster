<?php namespace PHPSocketMaster;

// example of implementation of Class Socket Master for listen
class Socket extends SocketMaster
{
	// on Connect event
	public function onConnect()
	{
		// never execute this
	}

	// on disconnect event
	public function onDisconnect()
	{
		echo '> Server destruct!';
	}

	// on receive message event
	public function onReceiveMessage($message)
	{
		echo '> Server recive message o.O';
	}

	// on error message event
	public function onError($errorMessage)
	{
		echo 'Oops SERVER error ocurred: '.$errorMessage;
		die(); // finish
	}

	public function onNewConnection(SocketBridge $socket)
	{
		ServerManager::AddNewClient();
		ServerManager::AddClient($socket);
	}
    
    public function onSendRequest(&$cancel, $message) 
    {
        //...
    }
    
    public function onSendComplete($message) 
    {
        //... 
    }
}