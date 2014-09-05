<?php

/*
 * ejemplo para logguearse y publicar en un smf
 * developed for simple machines forum 2.0.8
 */
echo 'starting...'."\r\n";

require('../../../src/iSocketMaster.php');
require('../../../src/iHttpClient.php');

// only in smf forums
define('SMF_TARGET', 'foro.infiernohacker.com');
define('SMF_USER', 'alexmanycool');
define('SMF_PASS', 'alex1234');
// target
define('SMF_BOARD', '6.0');
define('SMF_SUBJECT', 'probando');
define('SMF_MSG', 'Esto es una prueba de un mensaje publicado desde mi codigo en php');

// usamos namespace y factory del singleton para obtener la instancia
// $http = new httpClient(WEB_TARGET, true);
$http = PHPSocketMaster\httpClient::Factory(SMF_TARGET, true);

$cur_session = null;
echo 'login...'."\r\n";
// nos loggueamos :)
$http->post('index.php?action=login2',array('user' => SMF_USER, 'passwrd' => SMF_PASS, 'cookieneverexp'=>'on', 'hash_passwrd' => sha1(sha1(SMF_USER.SMF_PASS).$cur_session)));

// se loggueo correctamente y redirigimos al check user
if($http->response['Redirection'] === true)
{
	echo 'login success'."\r\n";
	// no queremos cambiar mas las cabeceras
	$http->saveHeaders = false;
	// cargamos la página de la redireccion
	echo 'sending smf check login'."\r\n";
	// check the user login
	$http->get($http->response['Location'], null);
	// post on the form of new post :)
	echo 'Waiting...';
	sleep(2);
	echo 'getting a seqnum and feda';
	$board = SMF_BOARD;
	$http->get('index.php', array('action' => 'post', 'board' => $board));
	// buscamos el sessid
	$init_sessionid = strpos($http->response['Main'],'sSessionId: \'') + strlen('sSessionId: \'');
	$fin_sessionid = strpos($http->response['Main'],'\'',$init_sessionid);
	$sessid = substr($http->response['Main'], $init_sessionid, $fin_sessionid-$init_sessionid);
	// buscamos el sessvar
	$init_sessionid = strpos($http->response['Main'],'sSessionVar: \'',$fin_sessionid) + strlen('sSessionVar: \'');
	$fin_sessionid = strpos($http->response['Main'],'\'',$init_sessionid);
	$sessvar = substr($http->response['Main'], $init_sessionid, $fin_sessionid-$init_sessionid);
	// buscamos el seqnum
	$init = strpos($http->response['Main'],'seqnum" value="',$fin_sessionid) + strlen('seqnum" value="');
	$fin = strpos($http->response['Main'],'"',$inicio);
	$seqnum = substr($http->response['Main'],$init, $fin-$init);
	echo 'Waiting...';
	sleep(2);
	echo 'sending post!'."\r\n";
	//$http->contentType = 'multipart/form-data';
	$http->post('index.php?action=post2;start=0;board='.$board,array('topic' => 0, 'subject' => SMF_SUBJECT, 'icon' => 'xx', 'message' => SMF_MSG, 'message_mode' => 0, 'notify' => 0, 'lock' => 0, 'sticky' => 0, 'move' => 0, 'additional_options' => 0, $sessid => $sessvar, 'seqnum' => $seqnum));
} else { echo 'error in loggin, view log.out for more info'; }

echo 'End of Program.. goodbye!';