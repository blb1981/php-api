<?php

declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

// echo $_SERVER["REQUEST_URI"];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", $path);
$resource = $parts[3];
$id = $parts[4] ?? null ;

// echo $resource, ' ', $id;
// echo $_SERVER["REQUEST_METHOD"];

// For the sake of this simple API, we're only using 
// "tasks" as a resource.
// For more complex APIs, use a 3rd party router

if ($resource != "tasks") {
  // You can manually set the "reason phrase" below
  // header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
  // but use this way instead
  http_response_code(404);
  exit;
}



// Create database object
$database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

// Create user gateway object
$user_gateway = new UserGateway($database);

$auth = new Auth($user_gateway);

if (!$auth->authenticateAPIKey()) {
  exit;
}

// Test the connection
// $database->getConnection();

// Create instance of TaskGateway class to pass into the TaskController class
$task_gateway = new TaskGateway($database);

$controller = new TaskController($task_gateway);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);