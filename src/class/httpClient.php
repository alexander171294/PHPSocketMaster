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
	private $sessions = null;
	private $cookies = null;
	private $webpage = '';
	private $protocolHeader = 'http';
	
	/**
	 * para crear el objeto usar el factory
	 * Function __construct
	 * @param SocketMaster $socket
	 * @param bool $saveHeaders
	 */
	private function __construct($webpage, $saveHeaders = true)
	{
		$this->socket = new HTTPSocketMaster($webpage, 80);
		$this->socket->set_httpClient(self::$instance);
		$this->saveHeaders = $saveHeaders;
		$this->webpage = $webpage;
	}
	
	public function get($resources, $params, $headers = array('User-Agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0', 'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Lenguaje' => 'es-ar,es;q=0.8,en-us;q=0.5,en;q=0.3', 'Connection' => 'keep-alive'))
	{
		$res = null;
		$first = true;
		$headers['Host'] => $this->webpage;
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
		var_dump($headers);
		$this->socket->send($headers, false);
	}	
	
	public function post($resources, $params, $headers = array('User-Agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0', 'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Lenguaje' => 'es-ar,es;q=0.8,en-us;q=0.5,en;q=0.3', 'Connection' => 'keep-alive'))
	{
		// hacemos la conexion mandando la peticion
		$headers = $this->generateHeaders($this->protocolHeader.'://'.$this->webpage.'/'.$resources, $params, $headers, HTTP_POST);
		$this->socket->connect();
		var_dump($headers);
		$this->socket->send($headers, false);
	}
	
	private function generateHeaders($resources, $params, $headers, $type = HTTP_GET)
	{
		$header_final = $type.' '.$resources.HCNL;
		foreach($headers as $header => $val )
		{
			$header_final .= $header . ': '.$val.HCNL;
		}
		$header_final.=HCNL;
		$first = true;
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
		// parsear cabeceras
		$this->response = $msg;
		var_dump($msg);
	}
	
	public function get_response() { return $this->response; }
	public function set_response($val) { $this->response = null; }
	
	
	
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
	public function onDisconnect() { echo '> Disconnected :('; }
	
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