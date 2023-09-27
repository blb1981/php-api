<?php 

class UserGateway
{
  // Declare private property to store db connection
  private PDO $conn;

  // Class will need access to db, so inject an instance into the constructor
  public function __construct(Database $database)
  {
    // Call getConnection method and store it in the $conn property
    $this->conn = $database->getConnection();
  }

  public function getbyAPIKey(string $key): array | false
  {
    $sql = "SELECT * 
            FROM user
            WHERE api_key = :api_key";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":api_key", $key, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}