<?php namespace PHPSocketMaster;

define('NL', "\r\n");

class ircClient extends Socket
{
	private $nick;
	
	public function __construct($host, $port, $nick)
	{
		$this->nick = $nick;
		parent::__construct($host, $port);
	}
	
	public function parse()
	{
		//parse input messages if is necesary
        
	}
	
	public function onConnect()
	{
		echo '>> conectado correctamente'.NL;
		echo '>> Enviando cabeceras...'.NL;
		$this->changeNick($this->nick);
		$this->send('USER thex experimental irc.freenode.net :John Doe'.NL);
		echo '>> esperando eventos...'.NL;
	}
	
	public function unirCanal($canal)
	{
		$this->send('JOIN '.$canal.NL);
	}
	
	public function sendToChannel($canal, $message)
	{
		$this->send('PRIVMSG '.$canal.' :'.$message.NL);
	}
	
	public function eexit()
	{
		$this->send('QUIT'.NL);
	}
	
	public function leaveChannel($canal)
	{
		$this->send('PART '.$canal.NL);
	}
	
	public function changeNick($nick)
	{
		$this->send('NICK '.$this->nick.NL);
	}
}