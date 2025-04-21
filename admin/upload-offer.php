<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once("../config/db.php");

$success = $error = "";

// Get all employees
$employees = $conn->query("SELECT id, name, emp_id FROM employees")->fetchAll(PDO::FETCH_ASSOC);

// Handle file upload and updating employee offer letter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['offer_pdf'])) {
    $employee_id = $_POST['employee_id'];
    $targetDir = "../uploads/offer_letters/";

    // Check if the uploaded file is a valid PDF
    if ($_FILES["offer_pdf"]["error"] == 0) {
        $fileName = basename($_FILES["offer_pdf"]["name"]);
        $targetFilePath = $targetDir . time() . "_" . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (strtolower($fileType) != "pdf") {
            $error = "Only PDF files are allowed.";
        } else {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["offer_pdf"]["tmp_name"], $targetFilePath)) {
                // Update the offer letter in the database
                $stmt = $conn->prepare("UPDATE employees SET offer_letter = ? WHERE id = ?");
                $stmt->execute([$targetFilePath, $employee_id]);
                $success = "✅ Offer letter uploaded successfully.";
            } else {
                $error = "Error uploading the file.";
            }
        }
    } else {
        $error = "Please select a valid PDF file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Offer Letter</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome Icons -->
  <style>
    /* Custom styling for Sidebar and FAB */
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
    <h1 class="text-xl font-bold text-gray-800">Upload Offer Letter</h1>
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
  <h2 class="text-3xl font-semibold text-gray-800 mb-4">Upload Offer Letter</h2>

  <!-- Success/Error Messages -->
  <?php if ($success): ?>
    <div class="bg-green-100 text-green-800 p-4 mb-4 rounded">
      <?= $success ?>
    </div>
  <?php elseif ($error): ?>
    <div class="bg-red-100 text-red-800 p-4 mb-4 rounded">
      <?= $error ?>
    </div>
  <?php endif; ?>

  <!-- Upload Offer Letter Form -->
  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <div class="flex flex-col">
      <label for="employee_id" class="font-semibold text-gray-700">Select Employee</label>
      <select name="employee_id" id="employee_id" required class="p-2 border border-gray-300 rounded-lg">
        <option value="">-- Select Employee --</option>
        <?php foreach ($employees as $emp): ?>
          <option value="<?= $emp['id'] ?>"><?= $emp['name'] ?> (<?= $emp['emp_id'] ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="flex flex-col">
      <label for="offer_pdf" class="font-semibold text-gray-700">Upload Offer PDF</label>
      <input type="file" name="offer_pdf" id="offer_pdf" accept="application/pdf" required class="p-2 border border-gray-300 rounded-lg">
    </div>

    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-200">Upload Offer Letter</button>
  </form>

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
