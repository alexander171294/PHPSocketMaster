<?php namespace PHPSocketMaster;

/**
 * @wiki ...
 */

// example of websocket using directly SocketMaster

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
			self::$sock->refreshListen(self::$NewClient, SCKM_WEB); // add SCKM_WEB for the WebSocket
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
		$sock->SocketEventReceptor->id = count(self::$clients); // add te id
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