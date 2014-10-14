<?php

require('../../src/iSocketMaster.php');
require('../../src/iHttpClient2.php');

// creamos una nueva instancia
$http = new PHPSocketMaster\httpClient('localhost');

// solicitamos el index.php
$result = $http->get('index.php?example=true');

// mostramos el cuerpo del mensaje recibido
var_dump($result['Headers']);
// mostramos la cabecera de respuesta
var_dump($result['Body']);

