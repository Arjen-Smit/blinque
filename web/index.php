<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(file_get_contents(__DIR__ . '/../config.yml'));

$bridgeIp = $config['bridgeIp'];
$accessKey = $config['accessKey'];
$lamp = $config['lamp'];

$client = new \Phue\Client($bridgeIp, $accessKey);

try {
    $client->sendCommand(
        new \Phue\Command\Ping
    );
} catch (\Phue\Transport\Exception\ConnectionException $e) {
    echo 'There was a problem accessing the bridge';
}

$status = "none";

foreach($client->sendCommand(new \Phue\Command\GetLights()) as $light) {

	if ($light->getUniqueId() == $lamp && $light->isOn()) {

		$light->setAlert('select');
		$status = "blinked";

	}
}
header('Content-Type: application/json');
echo json_encode(["status" => $status]);
