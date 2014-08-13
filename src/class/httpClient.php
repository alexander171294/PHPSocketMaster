<?php namespace PHPSocketMaster;

/**
 * @class httpClient
 * @author Alexander
 * @version 1.0
 * clase diseñada para hacer peticiones http a cualquier server
 * http.
 *
 * @example proximamente
 */

define('HTTP_GET', 'GET');
define('HTTP_POST', 'POST');
define('HCNL', "\r\n");

class httpClient
{
	use Property, Singleton;
	
	private $socket = null;
	private $saveHeaders = true;
	private $response = null;
	private $cookies = null;
	private $webpage = '';
	private $protocolHeader = 'http';
	private $version = '1.1';
	private $eof = false;
	private $first = true;
	
	/**
	 * para crear el objeto usar el factory
	 * Function __construct
	 * @param SocketMaster $socket
	 * @param bool $saveHeaders
	 */
	private function __construct($webpage, $saveHeaders = true)
	{
		$this->socket = new HTTPSocketMaster($webpage, 80);
		$this->saveHeaders = $saveHeaders;
		$this->webpage = $webpage;
	}
	
	public function get($resources, $params, $headers = array('User-Agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0', 'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Lenguaje' => 'es-ar,es;q=0.8,en-us;q=0.5,en;q=0.3', 'Connection' => 'keep-alive'))
	{
		// set eof as false
		$this->eof = false;
		// set first as true
		$this->first = true;
		// set me instance
		$this->socket->set_httpClient(self::get_instance());
		
		$res = null;
		$first = true;
		// agregamos ademas del host las cookies
		if(!empty($this->cookies)) $headers['Cookie'] = $this->cookies;
		// agregamos el host
		$headers['Host'] = $this->webpage;
		// generamos la nueva peticion con variables
		foreach($params as $param => $val)
		{
			if($first == true)
			{
				$first = false;
				$res .= '?'.urlencode(trim($param)).'='.urlencode($val);	
			} else {
				$res .= '&'.urlencode(trim($param)).'='.urlencode($val);
			}	
		}
		// hacemos la conexion mandando la peticion
		$headers = $this->generateHeaders($this->protocolHeader.'://'.$this->webpage.'/'.$res, null, $headers, HTTP_GET);
		$this->socket->connect();
		$this->socket->send($headers, false);
		// esperamos la respuesta
		while($this->eof === false)
		{
			$this->socket->refresh();
		}
	}	
	
	public function post($resources, $params, $headers = array('User-Agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0', 'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Lenguaje' => 'es-ar,es;q=0.8,en-us;q=0.5,en;q=0.3', 'Connection' => 'keep-alive'))
	{
		// set eof as false
		$this->eof = false;
		// set first as true
		$this->first = true;
		// set me instance
		$this->socket->set_httpClient(self::get_instance());
		// agregamos ademas del host las cookies
		if(!empty($this->cookies)) $headers['Cookie'] = $this->cookies;
		// agregamos el host
		$headers['Host'] = $this->webpage;
		// hacemos la conexion mandando la peticion
		$headers = $this->generateHeaders($this->protocolHeader.'://'.$this->webpage.'/'.$resources, $params, $headers, HTTP_POST);
		$this->socket->connect();
		var_dump($headers);
		$this->socket->send($headers, false);
		// esperamos la respuesta
		while($this->eof === false)
		{
			$this->socket->refresh();
		}
	}
	
	private function generateHeaders($resources, $params, $headers, $type = HTTP_GET)
	{
		$header_final = $type.' '.$resources.' '.strtoupper($this->protocolHeader).'/'.$this->version.HCNL;
		foreach($headers as $header => $val )
		{
			$header_final .= $header . ': '.$val.HCNL;
		}
		$header_final.=HCNL;
		$first = true;
		// evitamos foreach al dope
		if(!empty($params))
		foreach($params as $param => $val )
		{
			if($first == true)
			{
				$first = false;
				$header_final .= $param . '=' . $val . HCNL;
			} else {
				$header_final .= '&'.$param . '=' . $val . HCNL;
			}
		}
		return $header_final;
	}
	
	public function onReceiveResponse($msg)
	{
		if($msg == null) $this->eof = true;
		if($this->first === true)
		{
			$this->first = false;
			$response = array();
			// parseamos las cabeceras
			$parts = explode(HCNL.HCNL, $msg);
			$headers = explode(HCNL, $parts[0]);
			$response['Header'] = $headers[0];
			for($i = 1; $i<count($headers); $i++)
			{
			preg_match("/(.*): (.*)/",$headers[$i],$match);
			$response[$match[1]] = $match[2];
			}
			$response['Main'] = $parts[1];
			// vemos si hay que guardar las cookies
			if($this->saveHeaders == true) $this->cookies = $response['Set-Cookie'];
			// parsear cabeceras
			$this->response = $response;
		} else {
			$response = $this->response;
			$response['Main'] .= $msg;
			$this->response = $response;
		}
	}
	
	public function get_response() { return $this->response; }
	public function set_response($val) { $this->response = null; }
	
	public function setEOF() { $this->eof = true; }
}

class HTTPSocketMaster extends SocketMaster
{
	private $httpClient = null;
	
	public function set_httpClient($val)
	{
		$this->httpClient = $val;
	}
	
	// on Connect event
	public function onConnect()
	{
		// estamos conectados
		
	}
	
	// on disconnect event
	public function onDisconnect() 
	{
		 $this->httpClient->setEOF();		
	}
	
	// on receive message event
	public function onReceiveMessage($message)
	{
		$this->httpClient->onReceiveResponse($message);
	}
	
	// on error message event
	public function onError($errorMessage)
	{
		trigger_error('Oops HTTP error ocurred: '.$errorMessage, E_USER_ERROR);
	}
	
	public function onNewConnection(SocketBridge $socket) { }
}