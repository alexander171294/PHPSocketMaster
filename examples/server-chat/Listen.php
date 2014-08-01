<?php

// example of implementation of Class Socket Master for listen
class Socket extends SocketMaster
{
	// on Connect event
	private function onConnect()
	{
		// never execute this
	}

	// on disconnect event
	private function onDisconnect()
	{
		echo '> Server Disconnect event o.O';
	}

	// on receive message event
	private function onReceiveMessage($message)
	{
		echo '> Server recive message o.O';
	}

	// on error message event
	private function onError($errorMessage)
	{
		echo 'Oops SERVER error ocurred: '.$errorMessage;
		die(); // finish
	}
}