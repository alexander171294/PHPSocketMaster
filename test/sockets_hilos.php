<?php

class mysocket extends Thread
{
    private $socket = null;
    
    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        var_dump($this->socket);
        $this->start();
    }
    
    public function run()
    {
        var_dump($this->socket);
        socket_bind($this->socket, 'localhost', 80);
    }
}

$sock = new mysocket();