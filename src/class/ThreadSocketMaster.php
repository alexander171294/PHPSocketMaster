<?php namespace PHPSocketMaster;


/**
 * @abstract SocketMaster
 * @author Alexander
 * @version 1.0
 * Clase diseñada como modelo de socket orientado a objetos
 * con eventos, y posibilidad de trabajar con multihilo.
 * Cuenta con la finalidad de escuchar y conectarse.
 *
 * @example none
 * @example none
 * @example IRCClient Threaded
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

abstract class SocketMaster extends \Thread implements iSocketMaster
{
	use Property;

	protected $address = 'localhost';
	protected $port = 0;
	protected $readcontrol = "\n";
	protected $endLoop = false;
	protected $listenClients = null;
    protected $typeListen = false;
    
    protected $type = SCKM_UNKNOWN; // esto es seteado de forma erronea
    protected $domain = SCKM_INET;
    protected $protocol = SCKM_TCP;
    protected $connectionType = SCKM_UNKNOWN;
    protected $state = false;
    
    private $thread = null;
	private $socketRef = null;
    // auxiiar
    private $aux_socketRef = null;

	// constructor function
	public function __construct($address, $port)
	{
		$this->ErrorControl(array($this, '__construct_'), array($address, $port));
	}
	// the wrapper of construct function
	final private function __construct_($address, $port)
	{
        $this->socketFactory();
		// seteamos variables fundamentales
		$this->address = $address;
		$this->port = $port;
	}
    
    final public function socketFactory()
    {
        // creamos el socket
		$this->socketRef = socket_create($this->domain, SOCK_STREAM, $this->protocol);
		if($this->socketRef == false) throw new \Exception('Failed to create socket :: '.$this->getError());
        $this->aux_socketRef = $this->socketRef;
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
            $uSocket = get_resource_type($this->socketRef) == 'Socket' ? $this->socketRef : $this->aux_socketRef;
			socket_close($uSocket);
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
        $this->state = true;
        $uSocket = get_resource_type($this->socketRef) == 'Socket' ? $this->socketRef : $this->aux_socketRef;
        $this->typeListen = true;
		// bindeamos el socket
		if(socket_bind($uSocket, $this->address, $this->port) == false)
			throw new \Exception('Failed to bind socket :: '.$this->getError());
		if (socket_listen($uSocket, 5) === false)
			throw new \Exception('Failed Listening :: '.$this->getError());
        $this->connectionType = SCKM_SERVER;
	}
	
	

	// connect to host
	final public function connect()
	{
		$this->ErrorControl(array($this, 'connect_'));
        return true;
	}
	// the wrapper of connect function
	final private function connect_()
	{ // new thread
        $this->state = true;
        if(socket_connect($this->socketRef, $this->address, $this->port)===false)
			throw new \Exception('Failed to connect :: '.$this->getError());
        // exec event
        $this->connectionType = SCKM_CLIENT;
        $this->onConnect();
        $this->start();
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
        $uSocket = get_resource_type($this->socketRef) == 'Socket' ? $this->socketRef : $this->aux_socketRef;
		$newSocketRef = socket_accept($uSocket);
		if($newSocketRef === false) throw new \Exception('Socket Accept Failed :: '.$this->getError());
		if($type == SCKM_BASIC)
			$instance = new SocketBridge($newSocketRef, $Callback);
		if($type == SCKM_WEB)
			$instance = new WebSocketBridge($newSocketRef, $Callback, $this->address, $this->port);
		$Callback->setMother($instance);
		$instance->onConnect();
        $instance->start();
		return $instance;
	}

	//send message by socket
    // not final function, if you need program a previously actions for send and then use parent::send(...); is acceptable.
	public function send($message, $readControl = true)
	{
		$this->ErrorControl(array($this, 'send_'), array($message, $readControl));
	}
	// the wrapper of send function
	final public function send_($message, $readControl = true)
	{
		if($readControl === true) $message = $message.$this->readcontrol;
        // use native socket or auxiliar.
        $uSocket = get_resource_type($this->socketRef) == 'Socket' ? $this->socketRef : $this->aux_socketRef;
        
        $cancel = false;
        $this->onSendRequest($cancel, $message);
        if(!$cancel)
            if(socket_write($uSocket, $message, strlen($message)) == false)
                throw new \Exception('Socket Send Message Failed :: '.$this->getError());
            else
                $this->onSendComplete($message);
	}

	//detect new messages
	// return true if new messages, return fales if not new messages
	final public function refresh()
	{
            //$this->lock();
			$read = array($this->socketRef);
			$write = null;
			$exceptions = null;
			if(($result = socket_select($read, $write, $exceptions, 0)) === false)
            {
                $this->state = false;
				$this->onDisconnect();
            }
			if($result > 0) 
			{
				$this->ErrorControl(array($this, 'read'));
				return true;
			} else { return false; }
            //$this->unlock();
	}
	
    final public function run()
    {
        $this->socketRef = $this->socketRef;
        if($this->typeListen == false)
            $this->loop_refresh();
        else
            $this->loop_refreshListen();
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
                $this->state = false;
				$this->onDisconnect();
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
	final public function ErrorControl($call, $args = array())
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
    
    // for my Waiteador :)
    final public function get_endLoop(){ return $this->endLoop; }
    
    final public function get_type(){ return $this->type; }
    final public function get_domain(){ return $this->domain; }
    final public function get_protocol(){ return $this->protocol; }
    final public function get_connectionType(){ return $this->connectionType; }
    final public function get_state(){ return $this->state; }
	
	// AND SETTERS :)
	final public function set_address($val) { $this->address = $val; }
	final public function set_port($val) { $this->port = $val; }
	// ATENCIÓN: en realidad la función original solo se llamaba en un ámbito privado por lo que no es necesario un public ni conveniente.
    // pero para poder utilizar los hilos es necesario un seteador
	final public function set_socketRef($val) { $this->socketRef = $val; }
    final public function set_aux_socketRef($val) { $this->aux_socketRef = $val; }
    
    final public function set_type($v){ $this->type = $v; }
    final public function set_domain($v){ if($this->state == false) $this->domain = $v; }
    final public function set_protocol($v){ if($this->state == false) $this->protocol = $v; }
    final public function set_connectionType($v){}
    final public function set_state($v){}
}
