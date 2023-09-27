<?php 

// No namespace for this simple API but add one if needed

class TaskController 
{
  public function __construct(private TaskGateway $gateway) {}

  public function processRequest(string $method, ?string $id): void 
  {
    // If no id, the request is for collections or to create a new resource
    if ($id === null) {
      if ($method == 'GET') {
        echo json_encode($this->gateway->getAll());
        
      } elseif ($method == 'POST') {

        // Get POST data in the form of JSON from the request body
        // Returns empty array if JSON is invalid or body is empty
        $data = (array) json_decode(file_get_contents("php://input"), true);
        
        // Validate data
        $errors = $this->getValidationErrors($data);

        // Return errors there are any
        if (!empty($errors)) {
          $this->respondUnprocessableEntity($errors);
          return;
        }

        $id = $this->gateway->create($data);
        $this->respondCreated($id);

      } else {
        // If wrong method is sent
        $this->responseMethodNotAllowed("GET, POST");
      }
    } // There is an id, so handle it based on http method
      else {

        // Make sure the resource exists
        $task = $this->gateway->get($id);

        // If not, return 404 
        if ($task === false) {
          $this->respondNotFound($id);
          return;
        }

        switch($method) {
          case "GET":
            echo json_encode($task);
            break;

          case "PATCH":
            // Get PATCH data in the form of JSON from the request body
            // Returns empty array if JSON is invalid or body is empty
            $data = (array) json_decode(file_get_contents("php://input"), true);
            
            // Validate data
            $errors = $this->getValidationErrors($data, false);

            // Return errors there are any
            if (!empty($errors)) {
              $this->respondUnprocessableEntity($errors);
              return;
            }

            $rows = $this->gateway->update($id, $data);
            echo json_encode(["message" => "Task updated", "rows" => $rows ]);
            break;

          case "DELETE": 
            echo "delete $id";
            break;

          default: 
            $this->responseMethodNotAllowed("GET, PATCH, DELETE");
          }
      }
  }

  private function respondUnprocessableEntity(array $errors): void
  {
    http_response_code(422);
    echo json_encode(["errors" => $errors]);
  }

  private function responseMethodNotAllowed(string $allowed_methods): void
  {
    http_response_code(405);
    header("Allow: $allowed_methods");
  }

  private function respondNotFound(string $id): void
  {
    http_response_code(404);
    echo json_encode(["message" => "Task with id $id was not found"]);
  }

  private function respondCreated(string $id): void
  {
    http_response_code(201);
    echo json_encode(["message" => "Task created", "id" => $id]);
  }

  private function getValidationErrors(array $data, bool $is_new = true): array
  {
    $errors = [];
    
    if ($is_new && empty($data["name"])) {
      $errors[] = "name is required";
    }

    if (!empty($data["priority"])) {
      if (filter_var($data["priority"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "priority must be an integer";
      }
    }

    return $errors;
  }
}