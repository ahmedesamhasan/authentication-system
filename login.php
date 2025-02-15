<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use User\AuthenticationSystem\layout\Header;
use User\AuthenticationSystem\config\Config;
use User\AuthenticationSystem\layout\Footer;

$conn = Config::getConnection();

// ✅ طباعة الهيدر
Header::render();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $emailOrUsername = $_POST['emailOrUsername'] ?? '';
  $password = $_POST['password'] ?? '';

  if (empty($emailOrUsername) || empty($password)) {
    echo "<div class='alert alert-danger text-center'>Please fill all the fields</div>";
  } else {
    $sql = (filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL))
      ? "SELECT * FROM users WHERE email = :input"
      : "SELECT * FROM users WHERE username = :input";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':input', $emailOrUsername, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        header('Location: dashboard.php');
        exit();
      } else {
        echo "<div class='alert alert-danger text-center'>Invalid password</div>";
      }
    } else {
      echo "<div class='alert alert-danger text-center'>Invalid email or username</div>";
    }
  }
}
?>

<main class="form-signin w-50 m-auto">
  <form method="POST" action="login.php">
    <h1 class="h3 mt-5 fw-normal text-center">Login</h1>

    <div class="form-floating">
      <input name="emailOrUsername" type="text" class="form-control" id="floatingInput" placeholder="Email or Username">
      <label for="floatingInput">Email or Username</label>
    </div>

    <div class="form-floating">
      <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password">
      <label for="floatingPassword">Password</label>
    </div>

    <button name="submit" class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
  </form>
</main>

<?php
Footer::render();
?>