<?php namespace PHPSocketMaster;


/**
 * @class void.socketMaster.php
 * @author Alexander
 * @version 1.0
 * Clase diseñada como modelo de socket orientado a objetos
 * sin eventos.
 * Cuenta con la finalidad de escuchar y conectarse.
 *
 * @example none
 */

class voidSocketMaster extends SocketMaster
{
    
    private $connected = false;
    
    private $newMessage = false;
    private $message = null;
    
    private $error = false;
    private $errMsg = null;
    
    private $connections = 0;
    private $newConnection = false;
    private $lastSocket = null;
    
    private $sendComplete = false;
    
    // on Connect event
	public function onConnect() 
    {
        $this->connected = true;
    }

	// on disconnect event
	public function onDisconnect()
	{
		$this->connected = false;
	}

	// on receive message event
	public function onReceiveMessage($message)
	{
		$this->newMessage = true;
        $this->message = !empty($this->message) ? $this->message.NL.$message : $message;
	}

	// on error message event
	public function onError($errorMessage)
	{
        $this->error = true;
        $this->errMsg = $errorMessage;
	}

	public function onNewConnection(SocketBridge $socket) 
    {
        $this->connections++;
        $this->newConnection = true;
        $this->lastSocket = $socket;
    }
    
    public function onSendRequest(&$cancel, $message) 
    {
        $this->sendComplete = false;
    }
    
    public function onSendComplete($message) 
    {
        $this->sendComplete = true;
    }
    
    
    // getters
    public function get_connected() { return $this->connected; }
    
    public function get_newMessage() { return $this->newMessage; }
    public function get_message() { return $this->message; }
    
    public function get_error() { return $this->error; }
    public function get_errMsg() { return $this->errMsg; }
    
    public function get_connections() { return $this->connections; }
    public function get_newConnection() { return $this->newConnection; }
    public function get_lastSocket() { return $this->lastSocket; }
    
    public function get_sendComplete() { return $this->sendComplete; }
    
    // alias
    public function isConnected(){ return $this->get_connected(); }
    public function isSendComplete() { return $this->get_sendComplete(); }
    
    // setters
    public function set_connected($val) {}
    public function set_newMessage($val) {}
    public function set_message($val) {}
    public function set_error($val) {}
    public function set_errMsg($val) {}
    public function set_connections($val) {}
    public function set_newConnection($val) {}
    public function set_lastSocket($val) {}
    public function set_sendComplete($val) {}
}