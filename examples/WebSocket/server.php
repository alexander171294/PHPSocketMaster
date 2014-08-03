<?php

require('../../src/iSocketMaster.php');

// import implementation of SocketMaster as webSocket
include('webSocket.php');
// import implementation of SocketEventReceptor as newWebClient
include('newWebClient.php');

class ServerManager
{
	static private $sock = null;
	static private $clients = array(); // array of clients online
	static private $NewClient = null;

	static public function start()
	{
		// create a new socket
		self::$sock = new WebSocket('localhost', '2026');

		self::$sock->listen();

		echo '** listen **';

		self::AddNewClient();

		while(true)
		{
			self::$sock->refreshListen(self::$NewClient); // detect new clients
			// refresh messages
			for($i=0; $i<count(self::$clients); $i++)
			{
				self::$clients[$i]->refresh();
			}
		}
	}

	static public function AddNewClient()
	{
		self::$NewClient = new newWebClient();
	}

	static public function AddClient($sock)
	{
		$sock->getSocketEventReceptor()->id = count(self::$clients); // add te id
		self::$clients[] = $sock;
	}

	static public function Resend($message)
	{
		for($i=0; $i<count(self::$clients); $i++)
		{
			self::$clients[$i]->send($message);
		}
	}

	static public function DeleteClient($id)
	{
		unset(self::$clients[$id]);
		// reordenamos indices del arreglo
		self::$clients = array_values(self::$clients);
	}

}

ServerManager::Start();