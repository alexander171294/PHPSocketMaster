<?php

// requerimos la clase socketMaster
require('SocketBridge.php');

// interface para class SocketBridge 
interface iSocketBridge
{

	public function __construct($socket, SocketEventReceptor &$callback);

}