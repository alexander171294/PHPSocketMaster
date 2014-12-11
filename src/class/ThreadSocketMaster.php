<?php namespace PHPSocketMaster;


/**
 * @abstract SocketMaster
 * @author Alexander
 * @version 1.0
 * Clase diseñada como modelo de socket orientado a objetos
 * con eventos.
 * Cuenta con la finalidad de escuchar y conectarse.
 *
 * @example none
 * @example none
 * @example IRCClient Threaded
 */

// TYPES
define('SCKM_BASIC', 1);
define('SCKM_WEB', 2);

abstract class SocketMaster extends \Thread implements iSocketMaster
{
	use Property;

	protected $address = 'localhost';
	protected $port = 0;
	protected $readcontrol = "\n";
	protected $endLoop = false;
	protected $listenClients = null;
    
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
	private function __construct_($address, $port)
	{
        $this->socketFactory();
		// seteamos variables fundamentales
		$this->address = $address;
		$this->port = $port;
	}
    
    public function socketFactory()
    {
        // creamos el socket
		$this->socketRef = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
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
	}
	// the wrapper of disconnect function
	final private function disconnect_()
	{
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
	}
	// the wrapper of listen function
	final private function listen_()
	{
		// bindeamos el socket
		if(socket_bind($this->socketRef, $this->address, $this->port) == false)
			throw new \Exception('Failed to bind socket :: '.$this->getError());
		if (socket_listen($this->socketRef, 5) === false)
			throw new \Exception('Failed Listening :: '.$this->getError());
	}
	
	

	// connect to host
	final public function connect()
	{
		$this->ErrorControl(array($this, 'connect_'));
	}
	// the wrapper of connect function
	final private function connect_()
	{ // new thread
        if(socket_connect($this->socketRef, $this->address, $this->port)===false)
			throw new \Exception('Failed to connect :: '.$this->getError());
        // exec event
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
	public function send_($message, $readControl = true)
	{
		if($readControl === true) $message = $message.$this->readcontrol;
        // use native socket or auxiliar.
        $uSocket = get_resource_type($this->socketRef) == 'Socket' ? $this->socketRef : $this->aux_socketRef;
		if(socket_write($uSocket, $message, strlen($message)) == false)
			throw new \Exception('Socket Send Message Failed :: '.$this->getError());
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
				$this->onDisconnect();
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
        $this->loop_refresh();
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
	final public function refreshListen(SocketEventReceptor $Callback, $type = SCKM_BASIC)
	{
			$read = array($this->socketRef);
			$write = null;
			$exceptions = null;
			if(($result = socket_select($read, $write, $exceptions, 0)) === false)
				$this->onDisconnect();
			if($result > 0) 
			{
				$res = $this->accept($Callback, $type);
				$this->onNewConnection($res);
			}
	}
	
	// loop for function refreshListen
	final public function loop_refreshListen(SocketEventReceptor $Callback, &$clients, $type = SCKM_BASIC)
	{
		$this->listenClients = $clients;
		while($this->endLoop == false)
		{
			$this->refreshListen($callback, $type);
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
	public function ErrorControl($call, $args = array())
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
	
	// AND SETTERS :)
	final public function set_address($val) { $this->address = $val; }
	final public function set_port($val) { $this->port = $val; }
	// ATENCIÓN: en realidad la función original solo se llamaba en un ámbito privado por lo que no es necesario un public ni conveniente.
    // pero para poder utilizar los hilos es necesario un seteador
	final public function set_socketRef($val) { $this->socketRef = $val; }
    
    
}
