<?php

require('../../src/iSocketMaster.php');
require('../../src/iHttpClient2.php');

$http = new PHPSocketMaster\httpClient('foro.infiernohacker.com');

// solicitamos el index.php
$result = $http->post('index.php', array('nick' => 'alexander1712', 'pass' => 'test123'));

if(isset($result['Headers']))
{
    // mostramos el cuerpo del mensaje recibido
    var_dump($result['Headers']);
    // mostramos la cabecera de respuesta
    var_dump($result['Body']);
} else var_dump($result);
