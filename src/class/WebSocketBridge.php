<?php namespace PHPSocketMaster;

class WebSocketBridge extends SocketBridge implements iWebSocketBridge
{
	private $SendHandshake = false;
	private $magicString = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
	
	public function __construct($socket, SocketEventReceptor &$callback, $address = null, $port = 0) 
	{ 
		parent::__construct($socket, $callback);
		$this->address = $address;
		$this->port = $port;
	}

	public function onReceiveMessage($message)
	{
		$message = is_array($message) ? $message[0] : $message;
		if($this->SendHandshake == true)
		{
			if($message !== null)
				parent::ValidateObj(array($this->SocketEventReceptor, 'onReceiveMessage'), array($this->unMask($message)));
			else { $this->onDisconnect(); }
		} else {
			// parsear cabeceras
			$h = $this->parseHeaders($message);
			// enviar handshake
			$this->sendUnmasked($this->generateResponse($h['Sec-WebSocket-Key']),false);
			$this->SendHandshake = true;
			// handshake finish
		}
	}
	
	public function send($message, $readControl = false)
	{
		parent::send($this->Mask($message), $readControl);
	}
	
	public function sendUnmasked($message, $readControl = false)
	{
		parent::send($message, $readControl);
	}
	
	// mask message
	final private function Mask($message)
	{
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($message);
		if($length <= 125)
			$header = pack('CC', $b1, $length);
		elseif($length > 125 && $length < 65536)
			$header = pack('CCS', $b1, 126, $length);
		elseif($length >= 65536)
			$header = pack('CCN', $b1, 127, $length);
		return $header.$message;
	}
	
	// @todo agregarlo al interface
	final private function unMask($payload)
	{
		$data = $payload;
		
		$payloadLength = '';
		$mask = '';
		$unmaskedPayload = '';
		$decodedData = array();
		
		// estimate frame type:
		$firstByteBinary = sprintf('%08b', ord($data[0]));
		$secondByteBinary = sprintf('%08b', ord($data[1]));
		$opcode = bindec(substr($firstByteBinary, 4, 4));
		$isMasked = ($secondByteBinary[0] == '1') ? true : false;
		$payloadLength = ord($data[1]) & 127;
		
		// close connection if unmasked frame is received:
		if ($isMasked === false) {
			$this->disconnect($socket);
		}
		
		switch ($opcode) {
			// text frame:
			case 1:
				$decodedData['type'] = 'text';
				break;
		
				// connection close frame:
			case 8:
				$decodedData['type'] = 'close';
				break;
		
				// ping frame:
			case 9:
				$decodedData['type'] = 'ping';
				break;
		
				// pong frame:
			case 10:
				$decodedData['type'] = 'pong';
				break;
		
			default:
				// Close connection on unknown opcode:
				//$this->close(1003);
				break;
		}
		
		if ($payloadLength === 126) {
			$mask = substr($data, 4, 4);
			$payloadOffset = 8;
		} elseif ($payloadLength === 127) {
			$mask = substr($data, 10, 4);
			$payloadOffset = 14;
		} else {
			$mask = substr($data, 2, 4);
			$payloadOffset = 6;
		}
		
		$dataLength = strlen($data);
		
		if ($isMasked === true) {
			for ($i = $payloadOffset; $i < $dataLength; $i++) {
				$j = $i - $payloadOffset;
				$unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
			}
			$decodedData['payload'] = $unmaskedPayload;
		} else {
			$payloadOffset = $payloadOffset - 4;
			$decodedData['payload'] = substr($data, $payloadOffset);
		}
	
		return $decodedData;
	}
	
	// generate handshake response
	final private function generateResponse($code)
	{
		$secAccept = base64_encode(pack('H*', sha1($code . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
				"Upgrade: websocket\r\n" .
				"Connection: Upgrade\r\n" .
				"WebSocket-Origin: $this->address\r\n" .
				"WebSocket-Location: ws://$this->address:$this->port\r\n".
				"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
		return $upgrade;
	}
	
	// parse headers
	final private function parseHeaders($headers)
	{
		$lines = explode("\r", $headers);
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
	
}