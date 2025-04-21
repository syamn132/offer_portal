<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once("../config/db.php");

$success = $error = "";

// Employee ID generation function
function generate_employee_id($conn) {
    $prefix = "ASOT0";
    $start_number = 142;
    $stmt = $conn->prepare("SELECT emp_id FROM employees WHERE emp_id REGEXP '^ASOT0[0-9]+$'");
    $stmt->execute();
    $max_num = $start_number - 1;
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $eid) {
        $num = (int)substr($eid, strlen($prefix));
        if ($num > $max_num)
            $max_num = $num;
    }
    return $prefix . ($max_num + 1);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];
    $emp_id = generate_employee_id($conn);

    echo "<!-- DEBUG GENERATED EMPLOYEE ID: '$emp_id' -->";

    if (!$emp_id || !preg_match('/^ASOT0\d+$/', $emp_id)) {
        $error = "❌ Could not generate a valid Employee ID.";
    } else {
        $password = "TemporaryPassword";
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO employees (emp_id, name, email, dob, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$emp_id, $name, $email, $dob, $hashed_password]);
            $success = "✅ Employee created successfully!<br>Employee ID: <strong>" . htmlspecialchars($emp_id) . "</strong>";
        } catch (PDOException $e) {
            $error = "❌ Failed to create employee: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Employee</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    .fab-button {
      position: fixed; bottom: 30px; right: 30px;
      background-color: #34D399; border-radius: 50%; padding: 16px;
      color: white; font-size: 24px; box-shadow: 0 4px 6px rgba(0,0,0,.1);
    }
  </style>
</head>
<body class="bg-gray-100 font-sans">

<!-- Header Section -->
<header class="bg-white shadow-md fixed top-0 w-full z-30">
  <div class="flex justify-between items-center p-4">
    <h1 class="text-xl font-bold text-gray-800">Create Employee</h1>
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
    <a href="../" class="flex items-center text-red-600 px-4 py-2 hover:bg-red-100 rounded">
      <i class="fas fa-sign-out-alt mr-2"></i> Logout
    </a>
  </nav>
</aside>

<!-- Main Content Section -->
<main class="content lg:ml-64 p-6 mt-20">
  <h2 class="text-3xl font-semibold text-gray-800 mb-4">Create New Employee</h2>

  <?php if ($success): ?>
    <div class="bg-green-100 text-green-800 p-4 mb-4 rounded">
      <?= $success ?>
    </div>
  <?php elseif ($error): ?>
    <div class="bg-red-100 text-red-800 p-4 mb-4 rounded">
      <?= $error ?>
    </div>
  <?php endif; ?>

  <form method="POST" class="space-y-4">
    <div class="flex flex-col">
      <label for="name" class="font-semibold text-gray-700">Employee Name</label>
      <input type="text" name="name" id="name" required class="p-2 border border-gray-300 rounded-lg" placeholder="Enter employee name">
    </div>
    <div class="flex flex-col">
      <label for="email" class="font-semibold text-gray-700">Employee Email</label>
      <input type="email" name="email" id="email" required class="p-2 border border-gray-300 rounded-lg" placeholder="Enter employee email">
    </div>
    <div class="flex flex-col">
      <label for="dob" class="font-semibold text-gray-700">Employee DOB</label>
      <input type="date" name="dob" id="dob" required class="p-2 border border-gray-300 rounded-lg">
    </div>
    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-200">Create Employee</button>
  </form>

  <div class="fab-button">
    <a href="create-employee.php" class="text-white">
      <i class="fas fa-plus"></i>
    </a>
  </div>
</main>

<script>
  // Sidebar toggle for mobile
  document.getElementById('sidebar-toggle').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('hidden');
  });
</script>

</body>
</html>