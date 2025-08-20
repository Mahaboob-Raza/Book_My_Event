<?php
session_start();
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  
<?php include("includes/navbar.php"); ?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card p-4 shadow-sm">
        <h3 class="mb-3 text-center">Admin Login</h3>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include("includes/footer.php"); ?>

</body>
</html>