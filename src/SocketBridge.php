<?php namespace PHPSocketMaster;

class SocketBridge extends SocketMaster implements iSocketBridge
{
	// use property; // teorícamente está cargado del traite de socket master
	private $SocketEventReceptor = null;

	public function __construct($socket, SocketEventReceptor &$callback) 
	{ 
		$this->SocketEventReceptor = $callback;
		parent::setSocketRef($socket);
	}

	public function onError($errorMessage)
	{
		return $this->ValidateObj(array($this->SocketEventReceptor, 'onError'), array($errorMessage));
	}

	public function onConnect()
	{
		return $this->ValidateObj(array($this->SocketEventReceptor, 'onConnect'));
	}

	public function onDisconnect()
	{
		return $this->ValidateObj(array($this->SocketEventReceptor, 'onDisconnect'));
	}

	public function onReceiveMessage($message)
	{
		return $this->ValidateObj(array($this->SocketEventReceptor, 'onReceiveMessage'), array($message));
	}
	
	// wrapper, agradecimiento a Destructor.cs por la idea
	private function ValidateObj($call, $args = null)
	{
		if($this->SocketEventReceptor != null)
		{ 
			call_user_func($call, $args);
			return true;
		} else {  throw new exception('Not Set Callback in Socket Bridge');  return false; }
	}

	public function onNewConnection(SocketBridge $socket) { }
	
	// @todo : esta funcion hay que quitarla, para eso está el property
	public function getSocketEventReceptor() { return $this->SocketEventReceptor; }
	
	// GETTERS
	final public function get_SocketEventReceptor() { return $this->SocketEventReceptor; }
	// SETTER
	final public function set_SocketEventReceptor($val) { throw new exception('Not writable attrib $SocketEventReceptor'); }

}