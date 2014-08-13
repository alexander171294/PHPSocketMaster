<?php

require('../../src/iSocketMaster.php');
require('../../src/class/httpClient.php');

// cambiamos por su namespace y el factory method del singleton
// $http = new httpClient('underc0de.org');
$http = PHPSocketMaster\httpClient::Factory('foro.infiernohacker.com', true);

// solicitamos el index.php
$http->get('index.php',array('board' => '71.0'));

// mostramos el cuerpo del mensaje recibido
var_dump($http->response);
// mostramos la cabecera de respuesta
var_dump($http->response['Header']);

