<?php namespace PHPSocketMaster;

class SocketBridge extends SocketMaster implements iSocketBridge
{
	private $obj = null;

	public function __construct($socket, SocketEventReceptor &$callback) 
	{ 
		$this->obj = $callback;
		parent::setSocketRef($socket);
	}

	public function onError($errorMessage)
	{
		return $this->ValidateObj(array($this->obj, 'onError'), array($errorMessage));
	}

	public function onConnect()
	{
		return $this->ValidateObj(array($this->obj, 'onConnect'));
	}

	public function onDisconnect()
	{
		return $this->ValidateObj(array($this->obj, 'onDisconnect'));
	}

	public function onReceiveMessage($message)
	{
		return $this->ValidateObj(array($this->obj, 'onReceiveMessage'), array($message));
	}
	
	// wrapper, agradecimiento a Destructor.cs por la idea
	private function ValidateObj($call, $args = null)
	{
		if($this->obj != null)
		{ 
			call_user_func($call, $args);
			return true;
		} else {  throw new exception('Not Set Callback in Socket Bridge');  return false; }
	}

	public function onNewConnection(SocketBridge $socket) { }
	
	public function getSocketEventReceptor() { return $this->obj; }

}