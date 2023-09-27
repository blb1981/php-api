<?php 
  require __DIR__ . "/vendor/autoload.php";

  if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $database = new Database(
      $_ENV["DB_HOST"],
      $_ENV["DB_NAME"],
      $_ENV["DB_USER"],
      $_ENV["DB_PASS"],
    );

    $conn = $database->getConnection();

    $sql = "INSERT INTO user (first_name, last_name, username, email, password_hash, api_key)
            VALUES (:first_name, :last_name, :username, :email, :password_hash, :api_key)";
    
    $stmt = $conn->prepare($sql);

    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $api_key = bin2hex(random_bytes(16));

    $stmt->bindValue(":first_name", $_POST["first_name"], PDO::PARAM_STR);
    $stmt->bindValue(":last_name", $_POST["last_name"], PDO::PARAM_STR);
    $stmt->bindValue(":username", $_POST["username"], PDO::PARAM_STR);
    $stmt->bindValue(":email", $_POST["email"], PDO::PARAM_STR);
    $stmt->bindValue(":password_hash", $password_hash, PDO::PARAM_STR);
    $stmt->bindValue(":api_key", $api_key, PDO::PARAM_STR);

    $stmt->execute();

    echo "<p>Done</p> <p>Your api key is $api_key</p>";
    exit;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
  <title>Register for API Key</title>
</head>
<body>
  <div class="container">

    <h1>Register for API Key</h1>
    <form method="post">
      
      <label for="first_name">First name
        <input type="text" name="first_name" id="first_name">
      </label>
      <br>
      <label for="last_name">Last name
        <input type="text" name="last_name" id="last_name">
      </label>
      <br>
      <label for="username">Username
        <input type="text" name="username" id="username">
      </label>
      <br>
      <label for="email">Email
        <input type="email" name="email" id="email">
      </label>
      <br>
      <label for="password">Password
        <input type="password" name="password" id="password">
      </label>
      <br>
      
      <button type="submit">Register</button>
    </form>
  </div>
</body>
</html>