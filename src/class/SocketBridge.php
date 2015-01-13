<?php namespace PHPSocketMaster;


class SocketBridge extends SocketMaster implements iSocketBridge
{
	private $SocketEventReceptor = null;

	public function __construct($socket, SocketEventReceptor &$callback) 
	{ 
		$this->SocketEventReceptor = $callback;
		$this->SocketRef = $socket;
        if(!defined('SCKM_THREAD'))
            $this->aux_SocketRef = $socket;
	}

	public function onError($errorMessage)
	{
		return $this->ValidateObj(array($this->SocketEventReceptor, 'onError'), array($errorMessage));
	}

	public function onConnect()
	{
		return $this->ValidateObj(array($this->SocketEventReceptor, 'onConnect'));
	}
    
    public function onRefresh()
    {
        $this->ValidateObj(array($this->SocketEventReceptor, 'timeOut_refresh'));
        return $this->ValidateObj(array($this->SocketEventReceptor, 'onRefresh'));
    }

	public function onDisconnect()
	{
		return $this->ValidateObj(array($this->SocketEventReceptor, 'onDisconnect'));
	}

	public function onReceiveMessage($message)
	{
		$message = is_array($message) ? $message[0] : $message;
		return $this->ValidateObj(array($this->SocketEventReceptor, 'onReceiveMessage'), array($message));
	}
    
    public function onSendRequest(&$cancel, $message)
	{
        // no use validateobj, the call_user_func not support ref params 
        if($this->SocketEventReceptor != null)
		{ 
            $this->SocketEventReceptor->onSendRequest($cancel, $message);
            return true;
        } else {  throw new \Exception('Not Set Callback in Socket Bridge');  return false; }
	}
    
    public function onSendComplete($message)
	{
		return $this->ValidateObj(array($this->SocketEventReceptor, 'onSendComplete'), array($message));
	}
	
	// wrapper, agradecimiento a Destructor.cs por la idea
	public function ValidateObj($call, $args = null)
	{
		if($this->SocketEventReceptor != null)
		{ 
			call_user_func($call, $args);
			return true;
		} else {  throw new \Exception('Not Set Callback in Socket Bridge');  return false; }
	}

	public function onNewConnection(SocketBridge $socket) { }
	
	// @todo : esta funcion hay que quitarla, para eso está el property
	public function getSocketEventReceptor() { return $this->SocketEventReceptor; }
	
	// GETTERS
	final public function get_SocketEventReceptor() { return $this->SocketEventReceptor; }
	// SETTER
	final public function set_SocketEventReceptor($val) { throw new exception('Not writable attrib $SocketEventReceptor'); }

}
