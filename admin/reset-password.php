<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once("../config/db.php");

$success = $error = "";
$password_shown = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $new_password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE employees SET password = ?, password_raw = ?, reset_required = 1 WHERE id = ?");
    if ($stmt->execute([$hashed_password, $new_password, $employee_id])) {
        $success = "✅ Password reset successfully.";
        $password_shown = $new_password;
    } else {
        $error = "❌ Failed to reset password.";
    }
}

// Fetch all employees
$employees = $conn->query("SELECT id, name, email FROM employees")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Employee Password - Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome Icons -->
</head>
<body class="bg-gray-100 font-sans">

<!-- Header Section -->
<header class="bg-white shadow-md fixed top-0 w-full z-30">
  <div class="flex justify-between items-center p-4">
    <h1 class="text-xl font-bold text-gray-800">Reset Emp Password</h1>
    <button class="lg:hidden text-gray-600" id="sidebar-toggle">
      <i class="fas fa-bars"></i>
    </button>
  </div>
</header>

<!-- Sidebar Section -->
<aside id="sidebar" class="lg:w-64 w-64 bg-white shadow-md min-h-screen px-6 py-8 fixed inset-0 lg:block hidden z-30">
  <div class="mb-6">
    <h1 class="text-xl font-bold text-indigo-600">Admin Panel</h1>
  </div>
  <nav class="flex flex-col space-y-4">
    <a href="dashboard.php" class="flex items-center text-gray-700 px-4 py-2 hover:bg-indigo-100 rounded">
      <i class="fas fa-home mr-2"></i> Dashboard
    </a>
    <a href="employee-list.php" class="flex items-center text-gray-700 px-4 py-2 hover:bg-indigo-100 rounded">
      <i class="fas fa-users mr-2"></i> Employee List
    </a>
    <a href="create-employee.php" class="flex items-center text-gray-700 px-4 py-2 hover:bg-indigo-100 rounded">
      <i class="fas fa-user-plus mr-2"></i> Create Employee
    </a>
    <a href="upload-offer.php" class="flex items-center text-gray-700 px-4 py-2 hover:bg-indigo-100 rounded">
      <i class="fas fa-file-upload mr-2"></i> Upload Offer Letter
    </a>
    <a href="reset-password.php" class="flex items-center text-gray-700 px-4 py-2 hover:bg-indigo-100 rounded">
      <i class="fas fa-lock mr-2"></i> Reset Employee Password
    </a>
    <a href="logout.php" class="flex items-center text-red-600 px-4 py-2 hover:bg-red-100 rounded">
      <i class="fas fa-sign-out-alt mr-2"></i> Logout
    </a>
  </nav>
</aside>

<!-- Main Content -->
<main class="flex-1 lg:ml-64 p-8 mt-20">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Reset Employee Password</h2>

    <!-- Alert Messages -->
    <?php if ($success): ?>
      <div class="bg-green-100 text-green-700 p-4 mb-4 rounded border border-green-300">
        <?= $success ?><br>
        <span class="block mt-2 text-sm"><strong>Temporary Password:</strong> <code class="bg-white border px-2 py-1 rounded text-red-700"><?= $password_shown ?></code></span>
      </div>
    <?php elseif ($error): ?>
      <div class="bg-red-100 text-red-700 p-4 mb-4 rounded border border-red-300">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <!-- Reset Form -->
    <form method="POST" onsubmit="confirmReset(event)">
      <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-1">Select Employee</label>
        <select name="employee_id" class="w-full p-2 border rounded" required>
          <option value="">-- Select Employee --</option>
          <?php foreach ($employees as $emp): ?>
            <option value="<?= $emp['id'] ?>"><?= $emp['name'] ?> (<?= $emp['email'] ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">Reset Password</button>
    </form>
  </div>
</main>

<script>
  function confirmReset(event) {
    const confirmAction = confirm("Are you sure you want to reset this employee's password?");
    if (!confirmAction) {
      event.preventDefault();
    }
  }

  // JavaScript to toggle the sidebar visibility on smaller screens
  document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('sidebar-toggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function() {
        var sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
      });
    }
  });
</script>

</body>
</html>