<?php 

// No namespace for this simple API but add one if needed

class TaskController 
{
  public function __construct(private TaskGateway $gateway)
  {
    
  }

  public function processRequest(string $method, ?string $id): void 
  {
    // If no id, the request is for collections
    if ($id === null) {
      if ($method == 'GET') {
        echo 'index';
      } elseif ($method == 'POST') {
        echo 'create';
      } else {
        // If wrong method is sent
        $this->responseMethodNotAllowed("GET, POST");
      }
    } // There is an id, so handle it based on http method
      else {

        switch($method) {
          case "GET":
            echo "show $id";
            break;

          case "PATCH":
            echo "update $id";
            break;

          case "DELETE": 
            echo "delete $id";
            break;

          default: 
            $this->responseMethodNotAllowed("GET, PATCH, DELETE");
          }
      }
  }

  private function responseMethodNotAllowed(string $allowed_methods): void
  {
    http_response_code(405);
    header("Allow: $allowed_methods");
  }
}