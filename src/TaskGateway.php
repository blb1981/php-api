<?php 

class TaskGateway
{
  // Declare private property to store db connection
  private PDO $conn;

  // Class will need access to db, so inject an instance into the constructor
  public function __construct(Database $database)
  {
    // Call getConnection method and store it in the $conn property
    $this->conn = $database->getConnection();
  }

  public function getAllForUser(int $user_id)
  {
    $sql = "SELECT *
            FROM task
            WHERE user_id = :user_id
            ORDER BY name";

    // $stmt = $this->conn->query($sql); // This can be used for non prepared statements
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Adjusting the output so booleans are not returned as 1 or 0
    // We have to do this manually since there is no other way.
    // return $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      // Cast the is_completed value to boolean
      $row['is_completed'] = (bool) $row['is_completed'];

      // Append row to the array
      $data[] = $row;
    }

    return $data;
  }

  public function getForUser(int $user_id, string $id): array | false
  {
    $sql = "SELECT *
            FROM task
            WHERE id = :id
            AND user_id = :user_id";
            

    // To avoid SQL injection we'll use a prepared statement
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data !== false) {
      $data['is_completed'] = (bool) $data['is_completed'];
    }

    return $data;
  }

  public function createForUser(int $user_id, array $data): string
  {
    // To avoid SQL injection we'll use a prepared statement
    $sql = "INSERT INTO task (name, priority, is_completed, user_id)
            VALUES(:name, :priority, :is_completed, :user_id)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);

    // If priority is empty, handle what to enter
    if (empty($data["priority"])) {
      $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_NULL);      
    } else {
      $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);      
    }

    // Make false if not provided
    $stmt->bindValue(":is_completed", $data["is_completed"] ?? false, PDO::PARAM_BOOL);

    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();

    return $this->conn->lastInsertId();
  }

  public function updateForUser(int $user_id, string $id, array $data): int
  {
    $fields = [];

    if (!empty($data["name"])) {
      $fields["name"] = [
        $data["name"],
        PDO::PARAM_STR
      ];
    }

    // if (!empty($data["priority"])) {
    //   $fields["priority"] = [
    //     $data["priority"],
    //     PDO::PARAM_INT
    //   ];
    // }
    // if (!empty($data["is_completed"])) {
    //   $fields["is_completed"] = [
    //     $data["is_completed"],
    //     PDO::PARAM_BOOL
    //   ];
    // }

    // array_key_exists is used here because null or false counts as empty in PHP
    if (array_key_exists("priority", $data)) {
      $fields["priority"] = [
        $data["priority"],
        // set PDO data type to null if necessary
        $data["priority"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT 
      ];
    }
    
    // array_key_exists is used here because null or false counts as empty in PHP
    if (array_key_exists("is_completed", $data)) {
      $fields["is_completed"] = [
        $data["is_completed"],
        PDO::PARAM_BOOL
      ];
    }

    // Make sure fields are not empty
    if (empty($fields)) {
      return 0;
    } else {
      // Build the SQL query
      $sets = array_map(function($value){
        return "$value = :$value";
      }, array_keys($fields));
  
      $sql = "UPDATE task"
              . " SET " . implode(", ", $sets)
              . " WHERE id = :id
                  AND user_id = :user_id";
  
      $stmt = $this->conn->prepare($sql);

      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

      foreach($fields as $name => $values) {
        $stmt->bindValue(":$name", $values[0], $values[1]);
      }

      $stmt->execute();
      return $stmt->rowCount();
    }
  }

  public function deleteForUser(int $user_id, string $id): int
  {
    $sql = "DELETE FROM task
            WHERE id = :id
            AND user_id = :user_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount();
  }
}