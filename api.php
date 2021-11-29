<?php

require_once __DIR__ . '/vendor/autoload.php';

use Laminas\Soap\Server;

class Hello
{
    /**
     * Say hello,
     * 
     * @param string $firstName
     * @return string $greetings
     */
    public function sayHello($firstName)
    {
        return 'Hello ' . $firstName;
    }
}

$serverUrl = 'http://localhost/php-soap/api.php';

$options = [
    'uri' => $serverUrl,
];


$server = new Server(null, $options);

if (isset($_GET['wsdl'])) {
    $soapAutoDiscover = new \Laminas\Soap\AutoDiscover(new \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence());
    $soapAutoDiscover->setBindingStyle(array('style' => 'document'));
    $soapAutoDiscover->setOperationBodyStyle(array('use' => 'literal'));
    $soapAutoDiscover->setClass('Hello');
    $soapAutoDiscover->setUri($serverUrl);

    header("Content-Type: text/xml");
    echo $soapAutoDiscover->generate()->toXml();
} else {
    $soap = new \Laminas\Soap\Server($serverUrl . '?wsdl');
    $soap->setObject(new \Laminas\Soap\Server\DocumentLiteralWrapper(new Hello()));
    $soap->handle();
}
