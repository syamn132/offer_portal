<?php
session_start();
require_once("../config/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->execute([$email]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        if (empty($employee['password'])) {
            $error = "Your account is not yet activated. Please contact Admin.";
        } elseif (password_verify($password, $employee['password'])) {
            $_SESSION['employee_logged_in'] = true;
            $_SESSION['employee_id'] = $employee['id'];

            if (!empty($employee['reset_required']) && $employee['reset_required'] == 1) {
                $_SESSION['force_password_change'] = true;
                header("Location: create-new-password.php");
                exit();
            }

            $conn->prepare("UPDATE employees SET last_login = NOW() WHERE id = ?")->execute([$employee['id']]);
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Employee not found.";
    }
}
?>

<!-- HTML Login Page -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Login - Smarton Technologies</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen flex items-center justify-center px-4">
  <div class="bg-white shadow-lg rounded-xl w-full max-w-md p-8">
    <h2 class="text-3xl font-bold text-center text-green-800 mb-6">Employee Login</h2>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 border border-red-300 rounded p-3 mb-4">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Email</label>
        <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Password</label>
        <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>

      <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
        Login
      </button>
    </form>

    <p class="text-center mt-4 text-sm text-gray-600">
      <a href="forgot-password.php" class="text-green-700 hover:underline">Forgot Password?</a>
    </p>
  </div>
</body>
</html>
