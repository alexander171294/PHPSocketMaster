<?php namespace PHPSocketMaster;

if(!class_exists('SocketMaster')) trigger_error('The httpClient require include iSocketMaster interface and SocketMaster class', E_USER_ERROR);

// requerimos la clase socketMaster
require('class/httpClient.php');

/**
 * @abstract interface iHttpClient
 * @author Alexander
 * @version 1.0
 * interface de HTTP Client
 *
 * @example examples/http/
 */

interface iHttpClient
{
	// DON'T USE THE CONSTRUCTOR FOR GET INSTANCE OF THIS CLASS, USE THE Static Factory Method (HttpClient::Factory($web, $saveCookies))
	
	/**
	 * SEND REQUEST OF GET METHOD OF HTTP
	 * @param String $resources (web resource) ex: index.php
	 * @param Array $params
	 * @param Array $headers
	 */
	public function get($resources, $params, $headers);
	
	/**
	 * SEND REQUEST OF POST METHOD OF HTTP
	 * @param String $resources (web resource) ex: /myweb/index.php
	 * @param Array $params (params of post request)
	 * @param Array $headers (headers to send, is optional)
	 * 
	 */
	public function post($resources, $params, $headers);
	
	// autocall or emulate server response
	/**
	 * Autocalleable function, or emulate a server response (end expected on call setEOF function)
	 * @param string $msg (HTTP Response to request)
	 */
	public function onReceiveResponse($msg);
	
	// GETTERS
	/**
	 * Get the saved response of the server
	 */
	public function get_response();
	/**
	 * Getter of property saveHeaders
	 */
	public function get_saveHeaders();
	/**
	 * Getter of property contentType for post request
	 */
	public function get_contentType();
	
	// SETTERS
	/**
	 * this is a simbolic function, haven't an effects in the property response
	 */
	public function set_response($val);
	
	/**
	 * set the instance of HTTPSocketMaster for use
	 * @param HTTPSocketMaster $val
	 */
	public function set_socket(HTTPSocketMaster $val);
	/**
	 * setter of property saveHeaders (save cookies for example)
	 * @param bool $val
	 */
	public function set_saveHeaders($val);
	/**
	 * setter of property contentType for post requests
	 * @param string $val
	 */
	public function set_contentType($val);
	
	// set end of file (end of server responses)
	public function setEOF();
}