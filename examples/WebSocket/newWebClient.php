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
		// fix for windows sockets message
		$message = is_array($message) ? $message[0] : $message;
		if($this->requested)
		{
			$this->ParseReceptor($this->unMask($message));
		} else {
			$headers = $this->getHeaders($message);
			$this->code = $headers['Sec-WebSocket-Key']; 
			$this->requested = true;
			$this->getBridge()->send($this->generateResponse());
			echo 'End-HandShacke';
		}
	}
	
	private function getHeaders($message)
	{
		$lines = explode("\r", $message);
		$out = array();
		// salteamos el primero
		for($i = 1; $i<count($lines); $i++)
		{
			$unformat= explode(':',str_replace("\n",null, $lines[$i]));
			if(isset($unformat[1]))
				$out[$unformat[0]] = str_replace(' ',null,$unformat[1]);
		}
		return $out;
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
	
	// extract of https://github.com/srchea/PHP-Push-WebSocket/blob/master/lib/Server.class.php
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
	
		$text = null;
		for ($i = 0; $i < strlen($data); ++$i) {
			$text .= $data[$i] ^ $masks[$i%4];
		}
		return $text;
	}
	
	// extract of https://github.com/srchea/PHP-Push-WebSocket/blob/master/lib/Server.class.php
	private function Mask($message) 
	{
	    $length = strlen($message);
		$lengthField = null;
		if ($length < 126) 
		{
			$b2 = $length;
		} elseif ($length <= 65536) {
			$b2 = 126;
			$hexLength = dechex($length);
			if (strlen($hexLength)%2 == 1) 
			{
				$hexLength = '0' . $hexLength;
			}
			$n = strlen($hexLength) - 2;
			
			for ($i = $n; $i >= 0; $i=$i-2) 
			{
				$lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
			}
			while (strlen($lengthField) < 2) 
			{
				$lengthField = chr(0) . $lengthField;
			}
		} else {
			$b2 = 127;
			$hexLength = dechex($length);
			if (strlen($hexLength)%2 == 1) 
			{
				$hexLength = '0' . $hexLength;
			}
			$n = strlen($hexLength) - 2;
			for ($i = $n; $i >= 0; $i=$i-2) 
			{
				$lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
			}
			while (strlen($lengthField) < 8) 
			{
				$lengthField = chr(0) . $lengthField;
			}
		}
		return chr(1) . chr($b2) . $lengthField . $message;
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