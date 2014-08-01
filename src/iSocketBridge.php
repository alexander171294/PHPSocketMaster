<?php

// requerimos la clase socketMaster
require('class/SocketBridge.php');

// interface para class SocketBridge 
interface iSocketBridge
{

	public function __construct($socket, SocketEventReceptor $callback);

}