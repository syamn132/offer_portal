<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['employee_logged_in'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];
$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">My Profile</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <p><strong class="text-gray-700">Full Name:</strong> <?= htmlspecialchars($employee['name']) ?></p>
        <p><strong class="text-gray-700">Employee ID:</strong> <?= htmlspecialchars($employee['emp_id']) ?></p>
        <p><strong class="text-gray-700">Email:</strong> <?= htmlspecialchars($employee['email']) ?></p>
        <p><strong class="text-gray-700">Date of Birth:</strong> <?= htmlspecialchars($employee['dob']) ?></p>
        <p><strong class="text-gray-700">Gender:</strong> <?= htmlspecialchars($employee['gender']) ?></p>
        <p><strong class="text-gray-700">Phone:</strong> <?= htmlspecialchars($employee['phone']) ?></p>
        <p><strong class="text-gray-700">Alternate Phone:</strong> <?= htmlspecialchars($employee['alt_phone']) ?></p>
      </div>
      <div>
        <p><strong class="text-gray-700">Aadhar Number:</strong> <?= htmlspecialchars($employee['aadhar_number']) ?></p>
        <p><strong class="text-gray-700">PAN Number:</strong> <?= htmlspecialchars($employee['pan_number']) ?></p>
        <p><strong class="text-gray-700">Bank Account:</strong> <?= htmlspecialchars($employee['bank_account']) ?></p>
        <p><strong class="text-gray-700">IFSC Code:</strong> <?= htmlspecialchars($employee['ifsc_code']) ?></p>
        <p><strong class="text-gray-700">Branch:</strong> <?= htmlspecialchars($employee['bank_branch']) ?></p>
        <p><strong class="text-gray-700">Address:</strong> <?= htmlspecialchars($employee['address']) ?></p>
      </div>
    </div>

    <div class="mt-6 text-center">
      <a href="dashboard.php" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
