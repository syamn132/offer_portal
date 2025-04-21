<?php
session_start();
require_once("../config/db.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $emp_id = $_POST['emp_id'];
    $dob = $_POST['dob'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM employees WHERE email = ? AND emp_id = ? AND dob = ?");
        $stmt->execute([$email, $emp_id, $dob]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($employee) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE employees SET password = ?, reset_required = 0 WHERE id = ?");
            $update->execute([$hashed, $employee['id']]);

            $success = "Password has been reset successfully. <a href='login.php' class='underline text-green-700'>Login now</a>";
        } else {
            $error = "No matching employee found. Please contact Admin.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password - Employee</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen flex items-center justify-center px-4">
  <div class="bg-white shadow-lg rounded-xl w-full max-w-md p-8">
    <h2 class="text-3xl font-bold text-center text-green-800 mb-6">Reset Your Password</h2>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 border border-red-300 rounded p-3 mb-4">
        <?php echo $error; ?>
      </div>
    <?php elseif ($success): ?>
      <div class="bg-green-100 text-green-800 border border-green-300 rounded p-3 mb-4">
        <?php echo $success; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Email</label>
        <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Employee ID</label>
        <input type="text" name="emp_id" required class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Date of Birth</label>
        <input type="date" name="dob" required class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">New Password</label>
        <input type="password" name="new_password" required class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Confirm Password</label>
        <input type="password" name="confirm_password" required class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>

      <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
        Reset Password
      </button>
    </form>

    <p class="text-center mt-4 text-sm text-gray-600">
      <a href="login.php" class="text-green-700 hover:underline">Back to Login</a>
    </p>
  </div>
</body>
</html>
