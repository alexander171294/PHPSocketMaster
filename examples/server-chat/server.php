<?php

/**
 * @wiki https://github.com/alexander171294/PHPSocketMaster/wiki/Socket-en-modo-Servidor
 */

// example of chat server cli.

require('../../src/iSocketMaster.php');

// import implementation of SocketMaster as Socket
include('Listen.php');
// import implementation of SocketEventReceptor as newClient
include('newClient.php');

class ServerManager
{
	static private $sock = null;
	static private $clients = array(); // array of clients online
	static private $NewClient = null;

	static public function start()
	{
		// create a new socket
		self::$sock = new Socket('localhost', '2026');

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
		self::$NewClient = new newClient();
	}

	static public function AddClient($sock)
	{
		$sock->id = count(self::$clients); // add te id
		var_dump($sock-id);
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
	}

}

ServerManager::Start();