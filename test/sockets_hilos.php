<?php

class mysocket extends Thread
{
    private $socket = null;
    private $address = null;
    private $port = 0;
    
    public function __construct($address, $port)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        var_dump($this->socket); // ok
        $this->address = $address;
        $this->port = $port;
    }
    
    public function run()
    {
        var_dump($this->socket); // ok
        if(socket_connect($this->socket, $this->address, $this->port)===false)
            echo 'Error on connect';
        // spectate for incomming new messages
        while(true)
            $this->refresh();
    }
    
    public function refresh()
	{
            //$this->lock();
			$read = array($this->socket);
			$write = null;
			$exceptions = null;
			if(($result = socket_select($read, $write, $exceptions, 0)) === false)
				$this->onDisconnect();
            if(isset($read[0]))
                $this->socket = $read[0];
			if($result > 0) 
			{
                var_dump($this->socket);
                // SOCKET RECEIVE MESSAGE OR CHANGE STATUS
				if (false === ($len = socket_recv($this->socket, $buf, 2048, 0)))
                    echo 'error in read';
                if($buf === null)
                { // receive end signal
                    $this->endLoop = true;
                    $this->onDisconnect();
                } else { // show message
                    echo $buf;	
                }
				return true;
			} else { return false; }
            //$this->unlock();
	}
    
    public function onDiscconect()
    {
        echo 'Disconnected D:';
    }
}

$sock = new mysocket('irc.freenode.net', 6667);
    
$sock->start();