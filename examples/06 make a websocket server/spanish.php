<?php
 
require('../../src/iSocketMaster.php');
 

class Listener extends \PHPSocketMaster\SocketMaster
{

	public function onConnect() {}
  public function onRefresh() {}
	public function onDisconnect()	{}
	public function onReceiveMessage($message) {}
  public function onSendRequest(&$cancel, $message) {}
  public function onSendComplete($message) {}


	public function onError($errorMessage)
	{
		echo 'Error ocurrido'.$errorMessage;
		die(); 
	}

	public function onNewConnection(\PHPSocketMaster\SocketBridge $socket)
	{
    $socket->loop_refresh();
	}
     
}


class receptor extends \PHPSocketMaster\SocketEventReceptor
{
    public function onError() {}
    
    public function onConnect() 
    {
    
    }
    
    public function onDisconnect() {}
    
    public function onReceiveMessage($message) { var_dump($message); }
    
    public function onSendRequest(&$cancel, $message) {}
    
    public function onSendComplete($message) {}
    
    public function onRefresh() {}
}


$listener = new Listener('localhost', '2026');


$listener->listen(); 


$receptor = new receptor();

// AGREGAMOS LA CONSTANTE SCKM_WEB PARA SOPORTAR WEBSOCKETS
while($listener->refreshListen($receptor, SCKM_WEB) == false){}
