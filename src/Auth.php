<?php 

class Auth
{
  public function __construct(private UserGateway $user_gateway){}

  private int $user_id;

  public function authenticateAPIKey(): bool
  {
    // Check for api key in header
    if (empty($_SERVER["HTTP_X_API_KEY"])) {
      http_response_code(400);
      echo json_encode(["message" => "missing API key"]);
      return false;
    }

    $api_key = $_SERVER["HTTP_X_API_KEY"];

    $user = $this->user_gateway->getbyAPIKey($api_key);

    // Get user by API key
    if ($user === false) {
      http_response_code(401);
      echo json_encode(["message" => "invalid API key"]);
      return false;
    }

    $this->user_id = $user["id"];

    return true;
  }

  public function getUserID(): int
  {
    return $this->user_id;
  }

  public function authenticateAccessToken(): bool
  {
    if (!preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
      http_response_code(400);
      echo json_encode(["message" => "incomplete authorization header"]);
      return false;
    }
    
    $plain_text = base64_decode($matches[1], true);
    
    if ($plain_text === false) {
    }
    
    $data = json_decode($plain_text, true);
    
    if ($data === null) {
      http_response_code(400);
      echo json_encode(["message" => "invalid JSON"]);
      return false;

    }

    $this->user_id = $data["id"];

    return true;
  }
}