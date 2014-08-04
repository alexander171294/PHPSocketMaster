<?php

define('NL', "\n");

class newWebClient extends SocketEventReceptor
{

	private $code = null;
	private $magicString = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
	private $requested = false;
	private $host = 'localhost'; //ip or domain of server
	private $port = 80;
	private $fileMaster = null;
	private $counts = 0;
	
	public $id;

	public function onError()
	{
		echo '> oOps error in client: #'.$this->id.NL;
		// borramos el cliente con el error
		ServerManager::DeleteClient($this->id);
	}

	public function onConnect()
	{
		echo '> New client...'.NL;
	}

	public function onDisconnect()
	{
		echo '> disconnect client: '.$this->id.NL;
		ServerManager::DeleteClient($this->id);
	}

	public function onReceiveMessage($message)
	{
		// fix for windows sockets message
		$message = is_array($message) ? $message[0] : $message;
		if($this->requested)
		{
			echo $message;
			$this->ParseReceptor($message);
		} else {
			echo '[*]';
			if($this->isHandCode($message))
			{
				$this->code = $this->getHandCode($message);
			} 
			if($message == "\n" || $message == "\r")
			{
				$this->counts++; 
			} else { $this->counts = 0; }
			if($this->counts == 2) 
			{
				$this->requested = true;
				echo 'End-HandShacke';
				$this->getBridge()->send($this->generateResponse());
			}
		}
	}
	
	private function getHandCode($message)
	{
		$header = str_replace("\r", null, str_replace('Sec-WebSocket-Key: ', null, $message));
		return $header;
	}
	
	private function isHandCode($message)
	{
		return (strpos($message, 'Sec-WebSocket-Key: ')!== false);
	}
	
	private function generateResponse()
	{
		$secAccept = base64_encode(pack('H*', sha1($this->code . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
				"Upgrade: websocket\r\n" .
				"Connection: Upgrade\r\n" .
				"WebSocket-Origin: $this->host\r\n" .
				"WebSocket-Location: ws://$this->host:$this->port$this->fileMaster\r\n".
				"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
		return $upgrade;
	}
	
	private function ParseReceptor($message)
	{
		// your script code of chat here
		// tu código para el chat aquí
	}
}