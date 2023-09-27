<?php 

require __DIR__ . "/vendor/autoload.php";

$client = new GuzzleHttp\Client(['verify' => false]);

$response = $client->request("GET", "https://randomuser.me/api");

echo $response->getStatusCode(), "\n";
echo $response->getHeader('content-type')[0], "\n";
echo substr($response->getBody(), 0, 200), "...\n";