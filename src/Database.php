<?php

class Database
{
  // Store connection to avoide multiple connections
  private ?PDO $conn = null;

  // Using constructor promotion to make constructor parameters
  // properties for this object
  public function __construct(
    private string $host,
    private string $name,
    private string $user,
    private string $password
  ) {}
    
  public function getConnection(): PDO
  {
    // If connection is null, connect
    if ($this->conn === null) {
      // Set data source name
      $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";
  
      // Initiate new PDO instance and throw errors if there is trouble connecting
      $this->conn = new PDO($dsn, $this->user, $this->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false
      ]);
    }

    // If not, return the connection already established
    return $this->conn;
  }
}