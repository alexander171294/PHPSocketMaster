<?php namespace PHPSocketMaster;


/**
 * @class httpClient
 * @author Alexander
 * @version 2.0
 * clase diseñada para hacer peticiones http a cualquier server
 * http.
 *
 * @example proximamente
 */

define('HCNL', "\r\n");

class httpClient implements ihttpClient
{
    use property;
    
    private $port = 0;
    private $domain = null;
    private $socket = null;
    private $headers = array('User-Agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0', 'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Lenguaje' => 'es-ar,es;q=0.8,en-us;q=0.5,en;q=0.3', 'Connection' => 'keep-alive');
    private $protocol = 'http';
    
    public function __construct($domain, $newHeaders = null, $port = 80)
    {
        $this->port = $port;
        $this->domain = $domain;
        $this->socket = new HTTPSocketMaster($this->domain, $this->port);
        // set new headers
        if(is_array($newHeaders))
        {
            foreach($newHeaders as $index => $value)
            {
                $this->headers[$index] = $value;    
            }
        }
        $this->headers['Host'] = $domain;
    }
    
    public function get($resource)
    {
        $body = 'GET '.$this->protocol.'://'.$this->domain.'/'.$resource.' HTTP/1.0'.HCNL;
        $body .= $this->make($resource);
        return $this->sendData($body);
    }
    
    public function post($resource, $argv = null)
    {
        $body = 'POST '.$this->protocol.'://'.$this->domain.'/'.$resource.' HTTP/1.0'.HCNL;
        $plainARGV = null;
        if($argv !== null)
        {
            foreach($argv as $index => $value)
                $plainARGV .= '&'.$index.'='.$value;
            $plainARGV = trim($plainARGV, '&');
        }
        $body .= $this->make($resource, $plainARGV);
        return $this->sendData($body);
    }
    
    private function make($resource, $bodyRequest = null)
    {
        $out = null;
        if(!empty($bodyRequest))
        {
            if(!isset($this->headers['Content-Type'])) $this->headers['Content-Type'] = 'text/plain; charset=UTF-8';
            $this->headers['Content-Length'] = strlen($bodyRequest);
        }
        foreach($this->headers as $index => $value)
        {
            $out .= $index.': '.$value.HCNL;
        }
        if(isset($this->headers['Content-Length'])) unset($this->headers['Content-Length']);
        $finish = empty($bodyRequest) ? null : $bodyRequest;
        return $out.HCNL.$finish;
    }
    
    private function sendData($body)
    {
        // conectamos al host
        $this->socket->connect();
        
        var_dump($body);
        // enviamos peticion
        $this->socket->send($body, false);
        
        // esperamos respuesta
        $this->socket->loop_refresh();
        // luego de finalizar la conexión revisamos el resultado

        if(bridge::$response)
            return bridge::$objResponse;
        else
            return false;
    }
    
    public function implode_cookies($cookies)
    {
        $first = false;
        $arrayString = null;
        foreach($cookies as $cookie => $value)
        {
            if($first === true)
            {
            $arrayString .= ';'.$cookie.'='.$value;
            } else { $arrayString = $cookie.'='.$value; $first = true; }
            }
        return $arrayString;
    }
    
    public function explodeCookies($cookies)
    {
        $individualCookieString = explode(';', $cookies);
        $out = $this->cookies;
            for($index = 0; $index < count($individualCookieString); $index++)
            {
                $vals = explode('=',$individualCookieString[$index]);
                $out[$vals[0]] = $vals[1];
            }
        return $out;
    }
    
    // SETTERS
    public function set_headers($value)
    {
        foreach($value as $index => $val)
        {
            $this->headers[$index] = $val;    
        }
    }
    
    // GETTERS
    public function get_headers(){ return $this->headers; }
    
    
}

class HTTPSocketMaster extends SocketMaster
{
	private $httpClient = null;
	
	// on Connect event
	public function onConnect(){}
	
	// on disconnect event
	public function onDisconnect(){ }
	
	// on receive message event
	public function onReceiveMessage($message)
	{
	    bridge::$response = true;
            $result = $this->parseResponse($message);
            if(isset($result['Headers']))
                bridge::$objResponse = $result;
            else
                bridge::$objResponse.= $result;
	}
        
        // parse a response
        private function parseResponse($textPlain)
        {
            //separamos cabeceras de cuerpo de mensaje
            $parts = explode(HCNL.HCNL, $textPlain);
            if(!isset($parts[0]) || $parts[0] == null) return false;
            // separamos cada cabecera
	    $headers = explode(HCNL, $parts[0]);
            $out = array();
            $onlybody = false;
            for($i = 1; $i<count($headers); $i++)
	    {
		preg_match("/(.*): (.*)/",$headers[$i],$match);
                if(isset($match[1]))
                    $out['Headers'][$match[1]] = $match[2];
                else
                    $onlybody = true;
	    }
            if($onlybody == false && isset($out['Headers']))
            {
                $out['Headers'] = (object) $out['Headers'];
                $out['Body'] = $parts[1];
            } else $out = $parts[0];
            return $out;
        }
	
	// on error message event
	public function onError($errorMessage){ }
	
	public function onNewConnection(SocketBridge $socket){}
    
    public function onSendRequest(&$cancel, $message) 
    {
        //...
    }
    
    public function onSendComplete() 
    {
        //... 
    }
}

class bridge
{
    static public $objResponse = null;
    static public $response = false;
}
