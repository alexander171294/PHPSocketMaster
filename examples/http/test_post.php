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
$http->post('index.php?action=login2',array('user' => $user, 'passwrd' => '', 'cookieneverexp'=>'on', 'hash_passwrd' => sha1(sha1($user.$pass).$cur_session)));

// mostramos el cuerpo del mensaje recibido
file_put_contents('log.out',$http->response['Main']);
// mostramos la cabecera de respuesta
var_dump($http->response['Header']);