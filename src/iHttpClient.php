<?php namespace PHPSocketMaster;


if(!class_exists('PHPSocketMaster\SocketMaster')) trigger_error('The httpClient require include iSocketMaster interface and SocketMaster class', E_USER_ERROR);

// requerimos la clase socketMaster
require('class/httpClient.php');

interface iHttpClient
{
    /**
	 * Function get
	 * @return array web resource
     * @param: string $resource (resource for example index.php or folder/example.php?albert=2)
	 * HTTP Request of type GET to webserver
     * Realizar una petición get al servidor web.
	*/
    public function get($resource);
    
    
    /**
	 * Function post
	 * @return array web resource
     * @param: string $resource (resource for example index.php or folder/example.php?albert=2)
     * @param: array $argv (variables post of form, for example array('user' => 'alexander', 'pass' => '124as'))
	 * HTTP Request of type post to webserver
     * Realizar una petición get al servidor web.
	*/
    public function post($resource, $argv = null);
    
    public function implode_cookies($cookies);
    public function explodeCookies($cookies);
    
    /**
	 * Function set_headers
	 * @return none
     * @param: array $value headers sends in the request
	 * set headers of the request
	*/
    public function set_headers($value);
    
    /**
	 * Function get_headers
	 * @return array
	 * get headers of the request
	*/
    public function get_headers();
}
