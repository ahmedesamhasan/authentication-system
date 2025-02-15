<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use User\AuthenticationSystem\layout\Header;
use User\AuthenticationSystem\config\Config;
use User\AuthenticationSystem\layout\Footer;

$conn = Config::getConnection();

Header::render();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'] ?? '';
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';

  if (empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
    echo "<div class='alert alert-danger text-center'>Please fill all the fields</div>";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<div class='alert alert-danger text-center'>Invalid email format</div>";
  } elseif ($password !== $confirmPassword) {
    echo "<div class='alert alert-danger text-center'>Passwords do not match</div>";
  } else {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);

    if ($stmt->rowCount() > 0) {
      echo "<div class='alert alert-danger text-center'>User already exists</div>";
    } else {
      try {
        $conn->beginTransaction();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
        $stmt->execute([$email, $username, $hashedPassword]);

        $conn->commit();
        header('Location: login.php');
        exit();
      } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Registration Error: " . $e->getMessage());
        echo "<div class='alert alert-danger text-center'>Registration failed</div>";
      }
    }
  }
}
?>

<main class="form-signin w-50 m-auto">
  <form method="POST" action="register.php">
    <h1 class="h3 mt-5 fw-normal text-center">Register</h1>

    <div class="form-floating">
      <input name="email" type="email" class="form-control" id="floatingInput" placeholder="Email">
      <label for="floatingInput">Email</label>
    </div>

    <div class="form-floating">
      <input name="username" type="text" class="form-control" id="floatingUsername" placeholder="Username">
      <label for="floatingUsername">Username</label>
    </div>

    <div class="form-floating">
      <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password">
      <label for="floatingPassword">Password</label>
    </div>

    <div class="form-floating">
      <input name="confirm_password" type="password" class="form-control" id="floatingConfirmPassword"
        placeholder="Confirm Password">
      <label for="floatingConfirmPassword">Confirm Password</label>
    </div>

    <button name="submit" class="w-100 btn btn-lg btn-primary" type="submit">Register</button>
  </form>
</main>

<?php
Footer::render();
?>