<?php
require_once("../config/db.php");
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$employee_id]);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$emp) {
    echo "Employee not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Details</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    .container { max-width: 900px; }
    .card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);}
  </style>
</head>
<body class="bg-gray-100 font-sans">

<!-- Header Section -->
<header class="bg-white shadow-md fixed top-0 w-full z-30">
  <div class="flex justify-between items-center p-4">
    <h1 class="text-xl font-bold text-gray-800">Employee Details</h1>
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
<main class="flex-1 lg:ml-64 p-8 mt-20 overflow-y-auto">
  <div class="container mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Employee Details: <?= htmlspecialchars($emp['name']) ?></h2>

    <!-- Employee Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div class="card">
        <p><strong class="text-gray-700">Email:</strong> <?= htmlspecialchars($emp['email']) ?></p>
        <p><strong class="text-gray-700">Employee ID:</strong> <?= htmlspecialchars($emp['emp_id']) ?></p>
        <p><strong class="text-gray-700">Date of Birth:</strong> <?= htmlspecialchars($emp['dob']) ?></p>
      </div>
    </div>

    <hr class="my-6 border-gray-300">

    <!-- Offer Letter Status -->
    <div class="my-6 card">
      <h3 class="text-xl font-semibold text-gray-800 mb-4">Offer Letter Status</h3>
      <p><strong class="text-gray-700">Offer Uploaded:</strong> <?= $emp['offer_letter'] ? 'Yes' : 'No' ?></p>
      <p><strong class="text-gray-700">Offer Accepted:</strong> <?= ($emp['offer_status'] ?? '') === 'accepted' ? 'Yes' : 'No' ?></p>
      <p><strong class="text-gray-700">Accepted/Rejected Date:</strong> <?= $emp['offer_response_date'] ?? 'N/A' ?></p>
    </div>

    <hr class="my-6 border-gray-300">

    <!-- Uploaded Documents -->
    <div class="my-6 card">
      <h3 class="text-xl font-semibold text-gray-800 mb-4">Uploaded Documents</h3>
      <ul class="list-disc list-inside">
        <?php
        $docFields = ['photo', 'aadhar', 'pan', 'resume', 'education', 'experience', 'relieving'];
        foreach ($docFields as $field) {
            if (!empty($emp[$field])) {
                echo "<li><a class='text-blue-600 underline' href='../" . htmlspecialchars($emp[$field]) . "' target='_blank'>" . ucfirst($field) . "</a></li>";
            } else {
                echo "<li>" . ucfirst($field) . ": Not Uploaded</li>";
            }
        }
        ?>
      </ul>
    </div>

    <div class="mt-6 text-center">
      <a href="employee-list.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Back to Employee List</a>
    </div>
  </div>
</main>

<script>
  // Toggle sidebar visibility on small screens
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