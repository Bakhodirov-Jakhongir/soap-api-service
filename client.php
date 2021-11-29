<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new Laminas\Soap\Client('http://localhost/php-soap/api.php?wsdl');
$result = $client->sayHello(['firstName' => 'World']);

echo $result->sayHelloResult;
