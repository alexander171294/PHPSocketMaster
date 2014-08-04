<?php namespace PHPSocketMaster;
// example of implementation of Class Socket Master for listen
class WebSocket extends SocketMaster
{
	// on Connect event
	public function onConnect() {}
	// on receive message event
	public function onReceiveMessage($message) { }
	
	// on disconnect event
	public function onDisconnect() { echo '> Server Problem and disconnect! D:'; }

	// on error message event
	public function onError($errorMessage)
	{ 
		echo 'Oops SERVER error ocurred: '.$errorMessage; 
		die(); // finish }
	}

	public function onNewConnection(SocketBridge $socket)
	{
		ServerManager::AddNewClient();
		ServerManager::AddClient($socket);
	}
}