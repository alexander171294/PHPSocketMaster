<?php namespace PHPSocketMaster;

if(!class_exists('PHPSocketMaster\SocketMaster')) trigger_error('The httpClient require include iSocketMaster interface and SocketMaster class', E_USER_ERROR);

// requerimos la clase socketMaster
require('class/httpClient2.php');

interface iHttpClient
{
}
