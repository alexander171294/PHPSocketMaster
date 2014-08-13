<?php

require('../../src/iSocketMaster.php');
require('../../src/class/httpClient.php');

// cambiamos por su namespace y el factory method del singleton
// $http = new httpClient('underc0de.org');
$http = PHPSocketMaster\httpClient::Factory('underc0de.org', true);

// solicitamos el index.php
$http->get('foro/index.php',array('board' => '131.0'));

// mostramos lo recibido
var_dump($http->response);