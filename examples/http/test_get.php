<?php

require('../../src/iSocketMaster.php');
require('../../src/class/httpClient.php');

$http = new httpClient('underc0de.org');

// solicitamos el index.php
$http->get('foro/index.php',array('board' => '131.0'));

// mostramos lo recibido
var_dump($http->response);