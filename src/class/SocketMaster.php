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
define('SCKM_BASIC', 1);
define('SCKM_WEB', 2);

abstract class SocketMaster implements iSocketMaster
{
	/**
	 * Dado que mi version de php es menor a 5.4 
	 * los traits no existen para mi php, por lo que aparentemente
	 * tengo que abstenerme a usarlos, y eso se resume en la escritura
	 * del trait property directamente en la clase SocketMaster
	 */
	use Property;

	protected $address = 'localhost';
	protected $port = 0;
	protected $readcontrol = "\n";

	private $socketRef = null;

	public function __construct($address, $port)
	{
		try
		{
			// seteamos variables fundamentales
			$this->address = $address;
			$this->port = $port;
			// creamos el socket
			$this->socketRef = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if($this->socketRef == false) throw new \Exception('Failed to create socket :: '.$this->getError());
		} catch (\Exception $error) {
			$this->onError($error->getMessage());
		}
	}

	final public function __destruct()
	{
		try
		{
			if(!empty($this->socketRef))
				socket_close($this->socketRef);
			$this->onDisconnect();
		} catch (\Exception $error) {
			$this->onError($error->getMessage());
		}
	}

	// wait for a new external connection request
	final public function listen()
	{
		try
		{
			// bindeamos el socket
			if(socket_bind($this->socketRef, $this->address, $this->port) == false)
				throw new \Exception('Failed to bind socket :: '.$this->getError());
			if (socket_listen($this->socketRef, 5) === false)
				throw new \Exception('Failed Listening :: '.$this->getError());
		} catch (\Exception $error) {
			$this->onError($error->getMessage());
		}
	}

	// connect to host
	final public function connect()
	{
		try
		{
			if(socket_connect($this->socketRef, $this->address, $this->port)===false)
				throw new \Exception('Failed to connect :: '.$this->getError());
			$this->onConnect();
		} catch (\Exception $error) {
			$this->onError($error->getMessage());
		}
	}

	// accept a new external connection and create new socket object
	/**
		@params: SocketEventReceptor $callback :: instancia de clase que ejecutara los eventos del socket creado
		@return: object of SocketBridge
		*/
	final public function accept(SocketEventReceptor $Callback, $type = SCKM_BASIC)
	{
		try
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
		} catch (\Exception $error) {
			$this->onError($error->getMessage());
		}
	}

	//send message by socket
	public function send($message, $readControl = true)
	{
		try
		{
			if($readControl === true) $message = $message.$this->readcontrol;
			if(socket_write($this->socketRef, $message, strlen($message)) == false)
				throw new \Exception('Socket Send Message Failed :: '.$this->getError());
		} catch (\Exception $error) {
			$this->onError($error->getMessage());
		}
	}

	//detect new messages
	final public function refresh()
	{
			$read = array($this->socketRef);
			$write = null;
			$exceptions = null;
			if(($result = socket_select($read, $write, $exceptions, 0)) === false)
				$this->onDisconnect();
			if($result > 0) 
				$this->ErrorControl(array($this, 'read'));
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

	// recive a message by socket
	final private function read()
	{
			$buf = null;
			if (false === ($len = socket_recv($this->socketRef, $buf, 2048, 0)))
				throw new \Exception('Socket Read Failed :: '.$this->getError());
			if($buf === '') // esto estaba literalmente así en la documentación
			{ 
				$this->onDisconnect();
			} else {
				$this->onReceiveMessage($buf);	
			}
	}
	
	// wrapper try, agradecimientos a Destructor.cs por la idea
	private function ErrorControl($call, $args = null)
	{
		try
		{
			call_user_func($call, $args);
		} catch (\Exception $error) {
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
	
	// AND SETTERS :)
	final public function set_address($val) { $this->address = $val; }
	final public function set_port($val) { $this->port = $val; }
	// ATENCIÓN: en realidad la función original solo se llamaba en un ámbito privado por lo que no es necesario un public ni conveniente.
	final private function set_socketRef($val) { $this->socketRef = $val; }
}