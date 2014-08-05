<?php namespace PHPSocketMaster;

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
		echo "\n";
		// fix for windows sockets message
		$message = is_array($message) ? $message[0] : $message;
		if($this->requested)
		{
			$this->ParseReceptor($this->unMask($message));
		} else {
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
	
	private function unMask($payload) 
	{
		$payload = str_replace("\r",null,$payload);
		$length = ord($payload[1]) & 127;
	
		if($length == 126) {
			$masks = substr($payload, 4, 4);
			$data = substr($payload, 8);
		}
		elseif($length == 127) {
			$masks = substr($payload, 10, 4);
			$data = substr($payload, 14);
		}
		else {
			$masks = substr($payload, 2, 4);
			$data = substr($payload, 6);
		}
	
		$text = '';
		for ($i = 0; $i < strlen($data); ++$i) {
			$text .= $data[$i] ^ $masks[$i%4];
		}
		return $text;
	}
	
	private function Mask($text) 
	{
	    // 0x1 text frame (FIN + opcode)
	    $b1 = 0x80 | (0x1 & 0x0f);
	    $length = strlen($text);
	
	    if($length > 125 && $length < 65536)
	        $header = pack('CCS', $b1, 126, $length);
	    elseif($length >= 65536)
	        $header = pack('CCN', $b1, 127, $length);
	
	    return $header.$text;
	}	
	
	private function ParseReceptor($message)
	{
		// your script code of chat here
		// tu código para el chat aquí
		var_dump($message);
		if($message!=null)
			$this->getBridge()->send($this->Mask(json_encode(array('message'=>'welcome', 'type' => 'system'))));
	}
}