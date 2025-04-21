<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once("../config/db.php");

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: employee-list.php");
    exit();
}

// Fetch all employees
$stmt = $conn->query("SELECT id, emp_id, name, email, dob FROM employees ORDER BY id DESC");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee List</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome Icons -->
  <style>
    /* Custom Styling for Table and Sidebar */
    .table-card {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
    }
    .table-header {
      background-color: #f9fafb;
      font-weight: bold;
      color: #4b5563;
    }
    .fab-button {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background-color: #34D399;
      border-radius: 50%;
      padding: 16px;
      color: white;
      font-size: 24px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body class="bg-gray-100 font-sans">

<!-- Header Section -->
<header class="bg-white shadow-md fixed top-0 w-full z-30">
  <div class="flex justify-between items-center p-4">
    <h1 class="text-xl font-bold text-gray-800">Employee List</h1>
    <button class="lg:hidden text-gray-600" id="sidebar-toggle">
      <i class="fas fa-bars"></i> <!-- Hamburger Icon for Mobile -->
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

<!-- Main Content Section -->
<main class="content lg:ml-64 p-6 mt-20">
  <h2 class="text-3xl font-semibold text-gray-800 mb-6">Employee List</h2>

  <!-- Employee Table Card -->
  <div class="table-card bg-white p-6 rounded-lg shadow-md mb-6">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto border-collapse">
        <thead class="table-header">
          <tr>
            <th class="px-4 py-2 text-left">Employee ID</th>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Email</th>
            <th class="px-4 py-2 text-left">Date of Birth</th>
            <th class="px-4 py-2 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($employees as $emp): ?>
            <tr class="border-b">
              <td class="px-4 py-2"><?= $emp['emp_id'] ?></td>
              <td class="px-4 py-2"><?= $emp['name'] ?></td>
              <td class="px-4 py-2"><?= $emp['email'] ?></td>
              <td class="px-4 py-2"><?= $emp['dob'] ?></td>
              <td class="px-4 py-2 text-center">
                <a href="employee-details.php?id=<?= $emp['id'] ?>" class="text-blue-600 hover:underline">View</a> |
                <a href="employee-list.php?delete=<?= $emp['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Floating Action Button (FAB) -->
  <div class="fab-button">
    <a href="create-employee.php" class="text-white">
      <i class="fas fa-plus"></i> <!-- Floating Action Button Icon -->
    </a>
  </div>
</main>

<script>
  // JavaScript to toggle the sidebar visibility on smaller screens
  document.getElementById('sidebar-toggle').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('hidden');
  });
</script>

</body>
</html>
