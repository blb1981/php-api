<?php

require dirname(__DIR__) . "/vendor/autoload.php";

set_error_handler("ErrorHandler::handleError");
set_exception_handler('ErrorHandler::handleException');

// Create environment variables variable
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));

// Load env variables to be used in the $_ENV superglobal
$dotenv->load();

// Set all responses to be json
header("Content-type: application/json; charset=UTF-8");