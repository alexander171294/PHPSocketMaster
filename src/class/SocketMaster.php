<?php namespace PHPSocketMaster;


/**
 * @abstract SocketMaster
 * @author Alexander
 * @version 1.0
 * Clase diseñada como modelo de socket orientado a objetos
 * con eventos.
 * Cuenta con la finalidad de escuchar y conectarse.
 *
 * @example server-chat listen.php
 * @example client-chat socket.php
 */

// TYPES
define('SCKM_UNKNOWN', 0);
define('SCKM_BASIC', 1);
define('SCKM_WEB', 2);

// DOMAIN TYPES
define('SCKM_INET', AF_INET);
define('SCKM_INET6', AF_INET6);
define('SCKM_UNIX', AF_UNIX);

// PROTOCOL TYPES
define('SCKM_TCP', SOL_TCP);
define('SCKM_UDP', SOL_UDP);

// CONNECTIONS TYPES
define('SCKM_CLIENT', 4);
define('SCKM_SERVER', 5);

abstract class SocketMaster implements iSocketMaster
{
	use Property;

	protected $address = 'localhost';
	protected $port = 0;
	protected $readcontrol = "\n";
	protected $endLoop = false;
	protected $listenClients = null;
    
    protected $type = SCKM_BASIC; // esto es seteado de forma erronea
    protected $domain = SCKM_INET;
    protected $protocol = SCKM_TCP;
    protected $connectionType = SCKM_UNKNOWN;
    protected $state = false;

	private $socketRef = null;

	// constructor function
	public function __construct($address, $port)
	{
		$this->ErrorControl(array($this, '__construct_'), array($address, $port));
	}
	// the wrapper of construct function
	final private function __construct_($address, $port)
	{
		// seteamos variables fundamentales
		$this->address = $address;
		$this->port = $port;
		// creamos el socket
		$this->socketRef = socket_create($this->domain, SOCK_STREAM, $this->protocol);
		if($this->socketRef == false) throw new \Exception('Failed to create socket :: '.$this->getError());
	}
	
	// destructor function
	final public function __destruct()
	{
		$this->disconnect();
	}
	
	// disconnect function
	final public function disconnect()
	{
		$this->ErrorControl(array($this, 'disconnect_'));
        return true;
	}
	// the wrapper of disconnect function
	final private function disconnect_()
	{
        $this->state = false;
		if(!empty($this->socketRef))
		{
			socket_close($this->socketRef);
			$this->socketRef = null;
			$this->endLoop = true;
		}
		$this->onDisconnect();
	}

	// wait for a new external connection request
	final public function listen()
	{
		$this->ErrorControl(array($this, 'listen_'));
        return true;
	}
	// the wrapper of listen function
	final private function listen_()
	{
        $this->connectionType = SCKM_SERVER;
		// bindeamos el socket
		if(socket_bind($this->socketRef, $this->address, $this->port) == false)
			throw new \Exception('Failed to bind socket :: '.$this->getError());
		if (socket_listen($this->socketRef, 5) === false)
			throw new \Exception('Failed Listening :: '.$this->getError());
        $this->state = true;
	}

	// connect to host
	final public function connect()
	{
		$this->ErrorControl(array($this, 'connect_'));
        return true;
	}
	// the wrapper of connect function
	final private function connect_()
	{
        $this->connectionType = SCKM_CLIENT;
		if(socket_connect($this->socketRef, $this->address, $this->port)===false)
			throw new \Exception('Failed to connect :: '.$this->getError());
        $this->state = true;		
        $this->onConnect();
	}

	// accept a new external connection and create new socket object
	/**
		@params: SocketEventReceptor $callback :: instancia de clase que ejecutara los eventos del socket creado
		@return: object of SocketBridge
		*/
	final public function accept(SocketEventReceptor $Callback, $type = SCKM_BASIC)
	{
		$this->ErrorControl(array($this, 'accept_'), array($Callback, $type));
	}
	// the wrapper of accept function
	final private function accept_(SocketEventReceptor $Callback, $type = SCKM_BASIC)
	{
		$newSocketRef = socket_accept($this->socketRef);
		if($newSocketRef === false) throw new \Exception('Socket Accept Failed :: '.$this->getError());
		if($type == SCKM_BASIC)
			$instance = new SocketBridge($newSocketRef, $Callback);
		if($type == SCKM_WEB)
			$instance = new WebSocketBridge($newSocketRef, $Callback, $this->address, $this->port);
		$Callback->setMother($instance);
		$instance->onConnect();
		return $instance;
	}

	//send message by socket
	public function send($message, $readControl = true)
	{
		$this->ErrorControl(array($this, 'send_'), array($message, $readControl));
	}
	// the wrapper of send function
	final private function send_($message, $readControl = true)
	{
		if($readControl === true) $message = $message.$this->readcontrol;
        $cancel = false;
        $this->onSendRequest($cancel, $message);
        if(!$cancel)
            if(socket_write($this->socketRef, $message, strlen($message)) == false)
                throw new \Exception('Socket Send Message Failed :: '.$this->getError());
            else
                $this->onSendComplete($message);
	}

	//detect new messages
	// return true if new messages, return fales if not new messages
	final public function refresh()
	{
			$read = array($this->socketRef);
			$write = null;
			$exceptions = null;
			if(($result = socket_select($read, $write, $exceptions, 0)) === false)
            {
				$this->onDisconnect();
                $this->state = false;
            }
			if($result > 0) 
			{
				$this->ErrorControl(array($this, 'read'));
				return true;
			} else { return false; }
	}
	
	// loop for function refresh
	final public function loop_refresh()
	{
		while($this->endLoop == false)
		{
			$this->refresh();
		}
	}

	//detect new request external connections
	final public function refreshListen(SocketEventReceptor $Callback, $type = SCKM_UNKNOWN)
	{
            if($type !== SCKM_UNKNOWN) $this->type = $type;
			$read = array($this->socketRef);
			$write = null;
			$exceptions = null;
			if(($result = socket_select($read, $write, $exceptions, 0)) === false)
            {
				$this->onDisconnect();
                $this->state = false;
            }
			if($result > 0) 
			{
				$res = $this->accept($Callback, $this->type);
				$this->onNewConnection($res);
			}
	}
	
	// loop for function refreshListen
	final public function loop_refreshListen(SocketEventReceptor $Callback, &$clients, $type = SCKM_UNKNOWN)
	{
		$this->listenClients = $clients;
		while($this->endLoop == false)
		{
			$this->refreshListen($callback, $type);
            $type = SCKM_UNKNOWN;
			for($i=0; $i < count($this->listenClients); $i++)
			{
				$this->listenClients[$i]->refresh();
			}
		}
	}
	
	final public function set_listenClients(&$newArray)
	{
		$this->listenClients = $newArray;
	}

	// recive a message by socket
	final private function read()
	{
			$buf = null;
			if (false === ($len = socket_recv($this->socketRef, $buf, 2048, 0)))
				throw new \Exception('Socket Read Failed :: '.$this->getError());
			if($buf === null)
			{
				$this->endLoop = true;
				$this->onDisconnect();
			} else {
				$this->onReceiveMessage($buf);	
			}
	}
	
	// wrapper try, agradecimientos a Destructor.cs por la idea
	final private function ErrorControl($call, $args = array())
	{
		try
		{
			call_user_func_array($call, $args);
		} catch (\Exception $error) {
			$this->endLoop = true;
			$this->onError($error->getMessage());
		}
	}

	// call to be connected
	abstract public function onConnect();
	// call to be disconnected
	abstract public function onDisconnect();
	// call to receive message
	abstract public function onReceiveMessage($message);
	// call on error xD
	abstract public function onError($errorMessage); 
	// call on new connection accepted by listen
	abstract public function onNewConnection(SocketBridge $socket);
    
    // call on init send message
    abstract public function onSendRequest(&$cancel, $message);
    // call on finish send message
    abstract public function onSendComplete($message);

	final private function getError()
	{
		return socket_strerror(socket_last_error($this->socketRef));
	}

	// @todo: revisar por qué puse esta función, donde la uso y la viabilidad de cambiarla por su setter
	//final public function setSocketRef($sref) { $this->socketRef = $sref; }
    
	// GETTERS
	final public function get_address() { return $this->address; }
	final public function get_port() { return $this->port; }
	// ATENCIÓN: en realidad la función original solo se llamaba en un ámbito privado por lo que no es necesario un public ni conveniente.
	final private function get_socketRef() { return $this->socketRef; }
    
    final public function get_type(){ return $this->type; }
    final public function get_domain(){ return $this->domain; }
    final public function get_protocol(){ return $this->protocol; }
    final public function get_connectionType(){ return $this->connectionType; }
    final public function get_state(){ return $this->state; }
	
	// AND SETTERS :)
	final public function set_address($val) { $this->address = $val; }
	final public function set_port($val) { $this->port = $val; }
	// ATENCIÓN: en realidad la función original solo se llamaba en un ámbito privado por lo que no es necesario un public ni conveniente.
	final private function set_socketRef($val) { $this->socketRef = $val; }
    
    final public function set_type($v){ $this->type = $v; }
    final public function set_domain($v){ if($this->state == false) $this->domain = $v; }
    final public function set_protocol($v){ if($this->state == false) $this->protocol = $v; }
    final public function set_connectionType($v){}
    final public function set_state($v){}
    
}
