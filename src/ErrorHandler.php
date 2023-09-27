<?php 

class ErrorHandler 
{
  public static function handleError(
    int $errno,
    string $errstr,
    string $errfile,
    int $errline): void 
  {
    // TODO IN A PRODUCTION ENVIRONMENT, OUTPUT A MORE GENERIC MESSAGE THAN BELOW
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
  }


  public static function handleException(Throwable $exception): void 
  {
    // Set generic http response code for server errors
    http_response_code(500);

    // Output the exception
    // TODO IN A PRODUCTION ENVIRONMENT, OUTPUT A MORE GENERIC MESSAGE THAN BELOW
    echo json_encode([
      "code" => $exception->getCode(),
      "message" => $exception->getMessage(),
      "file" => $exception->getFile(),
      "line" => $exception->getLine()
    ]);
  }
}