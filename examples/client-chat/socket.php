<?php

// example of implementation of Class Socket Master
class Socket extends SocketMaster
{
	// on Connect event
	private function onConnect()
	{
		echo '> Connected success!';
	}

	// on disconnect event
	private function onDisconnect()
	{
		echo '> Disconnected :(';
	}

	// on receive message event
	private function onReceiveMessage($message)
	{
		echo '< '.$message;
	}

	// on error message event
	private function onError($errorMessage)
	{
		echo 'Oops error ocurred: '.$errorMessage;
		die(); // finish
	}
}