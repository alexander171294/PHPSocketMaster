<?php

// example of implementation of Class Socket Master
class Socket extends SocketMaster
{
	// on Connect event
	public function onConnect()
	{
		echo '> Connected success!';
	}

	// on disconnect event
	public function onDisconnect()
	{
		echo '> Disconnected :(';
	}

	// on receive message event
	public function onReceiveMessage($message)
	{
		echo '< '.$message;
	}

	// on error message event
	public function onError($errorMessage)
	{
		echo 'Oops error ocurred: '.$errorMessage;
		die(); // finish
	}

	public function onNewConnection(SocketBridge $socket) { }
}