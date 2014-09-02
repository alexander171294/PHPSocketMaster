<?php

/*
 * ejemplo para logguearse en un smf
 */

require('../../src/iSocketMaster.php');
require('../../src/class/httpClient.php');

// cambiamos por su namespace y el factory method del singleton
// $http = new httpClient('underc0de.org');
$http = PHPSocketMaster\httpClient::Factory('foro.infiernohacker.com', true);

// solicitamos el index.php
$cur_session = null;
$user = 'alexmanycool';
$pass = 'alex1234';
$http->post('index.php?action=login2',array('user' => $user, 'passwrd' => $pass, 'cookieneverexp'=>'on', 'hash_passwrd' => sha1(sha1($user.$pass).$cur_session)));

// mostramos el cuerpo del mensaje recibido
file_put_contents('login2.log',$http->response['Main']);

// se loggueo correctamente y redirigimos al check user
if($http->response['Redirection'] === true)
{
	// no queremos cambiar mas las cabeceras
	$http->saveHeaders = false;
	// cargamos la página de la redireccion
	$http->get($http->response['Location'], null);
	// mostramos el cuerpo del mensaje recibido del check
	file_put_contents('check.log',$http->response['Main']);
	// ahora cargamos nuestro perfil
	$http->get('index.php',array('action' => 'profile'));
	// mostramos el cuerpo del mensaje recibido del perfil
	file_put_contents('perfil.log',$http->response['Main']);
} else { echo 'error in loggin, view log.out for more info'; }

echo 'End of Program.. goodbye!';