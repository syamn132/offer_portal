<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['email'];
    $password = $_POST['password'];

    // Hardcoded admin credentials
    $admin_email = "admin@sot";
    $admin_password = "Sot@42025";

    if ($username === $admin_email && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id']; // assuming you fetched admin row from DB
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Smarton Technologies - Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#162d3d] min-h-screen flex items-center justify-center">
  <div class="bg-white w-full max-w-md rounded-md shadow-md p-8">
    
    <!-- Branding -->
    <div class="flex items-center justify-center mb-6">
      <!-- You can replace this with your logo if available -->
      <div class="h-6 w-6 bg-blue-600 rounded-sm mr-2"></div>
      <span class="text-2xl font-semibold text-gray-900">Smarton Technologies</span>
    </div>

    <!-- Title -->
    <h2 class="text-2xl font-light text-gray-800 mb-6 text-center">Admin Sign in</h2>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
      <div class="bg-red-100 text-red-700 p-2 mb-4 rounded text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST">
      <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
      <input type="email" name="email" required class="w-full mb-4 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-400">

      <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
      <input type="password" name="password" required class="w-full mb-4 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-400">

      <div class="flex items-center mb-4">
        <input type="checkbox" id="remember" name="remember" class="mr-2">
        <label for="remember" class="text-sm text-gray-700">Keep me signed in</label>
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Login</button>
    </form>

    <!-- Footer -->
    <div class="text-xs text-center mt-6 text-gray-500">
      &copy; <?= date('Y') ?> SoT • All rights reserved
    </div>
  </div>
</body>
</html>
