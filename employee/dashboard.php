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

$offer_uploaded = !empty($employee['offer_letter']);
$offer_status = $employee['offer_status'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome Icons -->
  <style>
    .sidebar-overlay { background: rgba(0,0,0,0.3); }
  </style>
</head>
<body class="bg-gray-100 font-sans">

<!-- Header -->
<header class="bg-white shadow fixed top-0 w-full z-30 flex items-center justify-between px-4 py-3">
  <div class="flex items-center gap-2">
    <button id="sidebar-toggle" class="lg:hidden text-gray-600 focus:outline-none mr-2">
      <i class="fas fa-bars fa-lg"></i>
    </button>
    <h1 class="text-lg font-bold text-gray-800">Employee Dashboard</h1>
  </div>
  <div class="flex items-center gap-4">
    <!-- You can add notifications/profile icons here -->
    <span class="hidden md:block font-medium text-gray-600">Hi, <?= htmlspecialchars($employee['name']) ?></span>
    <img src="https://ui-avatars.com/api/?name=<?= urlencode($employee['name']) ?>&background=4f46e5&color=fff" alt="avatar" class="h-10 w-10 rounded-full object-cover border-2 border-indigo-200">
  </div>
</header>

<!-- Sidebar & Overlay -->
<div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 z-30 hidden lg:hidden"></div>
<aside id="sidebar" class="bg-white shadow-md fixed inset-y-0 left-0 w-64 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 z-40 flex flex-col">
  <div class="px-6 py-5 border-b flex flex-col items-center">
    <img src="https://ui-avatars.com/api/?name=<?= urlencode($employee['name']) ?>&background=4f46e5&color=fff" class="h-14 w-14 rounded-full mb-2 border-2 border-indigo-200" alt="avatar">
    <span class="text-indigo-600 font-bold text-lg mb-1"><?= htmlspecialchars($employee['name']) ?></span>
    <span class="text-xs text-gray-400 mb-1"><?= htmlspecialchars($employee['email']) ?></span>
    <span class="text-xs text-gray-400 hidden lg:inline">ID: <?= htmlspecialchars($employee['emp_id']) ?></span>
    <span class="text-xs text-gray-400 hidden lg:inline">DOB: <?= htmlspecialchars($employee['dob']) ?></span>
  </div>
  <nav class="flex-1 py-6 px-2 flex flex-col gap-2">
    <a href="dashboard.php" class="flex items-center gap-3 text-gray-700 hover:bg-indigo-100 rounded px-4 py-2 font-medium <?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php')echo 'bg-indigo-50 border-l-4 border-indigo-500'; ?>">
      <i class="fas fa-columns w-5"></i> Dashboard
    </a>
    <a href="upload-documents.php" class="flex items-center gap-3 text-gray-700 hover:bg-indigo-100 rounded px-4 py-2 font-medium">
      <i class="fas fa-folder-open w-5"></i> Upload Documents
    </a>
    <a href="offer-letter.php" class="flex items-center gap-3 text-gray-700 hover:bg-indigo-100 rounded px-4 py-2 font-medium">
      <i class="fas fa-file-contract w-5"></i> Offer Letter
    </a>
    <a href="logout.php" class="flex items-center gap-3 text-red-600 hover:bg-red-100 rounded px-4 py-2 font-medium">
      <i class="fas fa-sign-out-alt w-5"></i> Logout
    </a>
  </nav>
</aside>

<!-- Main Content -->
<main class="lg:ml-64 pt-20 px-4 pb-8 min-h-screen">
  <div class="mb-2 text-xs text-gray-400">Dashboard / Employee Dashboard</div>
  <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1">Welcome, <?= htmlspecialchars($employee['name']) ?>!</h2>
  <p class="text-sm text-gray-500 mb-6">Here’s everything you need to get started today.</p>

  <!-- Dashboard Functionalities Overview -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <!-- Offer Status Card -->
    <div class="bg-white p-6 rounded shadow flex flex-col items-start border-l-4 <?php
         if($offer_status==='accepted') echo 'border-green-500';
         elseif($offer_status==='rejected') echo 'border-red-500';
         else echo 'border-yellow-400';
    ?>">
      <div class="text-gray-500 flex items-center gap-2"><i class="fas fa-file-contract"></i> Offer Status</div>
      <div class="text-xl font-bold text-gray-700 mt-2 "><?= ucfirst($offer_status) ?></div>
    </div>
    <!-- Document Status Card -->
    <div class="bg-white p-6 rounded shadow flex flex-col items-start border-l-4 border-blue-500">
      <div class="text-gray-500 flex items-center gap-2"><i class="fas fa-folder-open"></i> Documents</div>
      <div class="text-xl font-bold text-gray-700 mt-2 "><?= $offer_uploaded ? 'Uploaded' : 'Pending' ?></div>
    </div>
    <!-- Next Step Card -->
    <div class="bg-white p-6 rounded shadow flex flex-col items-start border-l-4 border-indigo-500">
      <div class="text-gray-500 flex items-center gap-2"><i class="fas fa-shoe-prints"></i> Next Step</div>
      <div class="text-xl font-bold text-gray-700 mt-2">
        <?php
          if ($offer_status === 'pending') echo "Review Offer";
          elseif ($offer_status === 'accepted') echo "Complete Onboarding";
          else echo "Contact Admin";
        ?>
      </div>
    </div>
  </div>

  <!-- Main Functionalities (just mentions/links for now) -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg p-6 shadow flex flex-col gap-2">
      <h3 class="text-lg font-semibold mb-1 flex items-center gap-2"><i class="fas fa-id-badge text-indigo-500"></i> My Profile</h3>
      <p class="text-gray-500 mb-2">View and update your employee details and contact info.</p>
      <a href="#" class="inline-block bg-indigo-50 text-indigo-700 px-4 py-2 rounded hover:bg-indigo-100 text-sm">Coming Soon</a>
    </div>
    <div class="bg-white rounded-lg p-6 shadow flex flex-col gap-2">
      <h3 class="text-lg font-semibold mb-1 flex items-center gap-2"><i class="fas fa-calendar-check text-green-600"></i> Attendance</h3>
      <p class="text-gray-500 mb-2">Check your attendance records and working hours.</p>
      <a href="#" class="inline-block bg-indigo-50 text-indigo-700 px-4 py-2 rounded hover:bg-indigo-100 text-sm">Coming Soon</a>
    </div>
    <div class="bg-white rounded-lg p-6 shadow flex flex-col gap-2">
      <h3 class="text-lg font-semibold mb-1 flex items-center gap-2"><i class="fas fa-upload text-blue-600"></i> Upload Documents</h3>
      <p class="text-gray-500 mb-2">Upload your KYC, educational and experience documents.</p>
      <a href="upload-documents.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Go to Document Upload</a>
    </div>
    <div class="bg-white rounded-lg p-6 shadow flex flex-col gap-2">
      <h3 class="text-lg font-semibold mb-1 flex items-center gap-2"><i class="fas fa-file-contract text-yellow-500"></i> Offer Letter</h3>
      <?php if ($offer_uploaded): ?>
        <?php if ($offer_status === 'pending'): ?>
          <a href="offer-letter.php" class="inline-block bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm mt-1">Review & Accept Offer</a>
        <?php elseif ($offer_status === 'accepted'): ?>
          <a href="<?= htmlspecialchars($employee['offer_letter']) ?>" download class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800 text-sm mt-1">Download Offer Letter</a>
        <?php else: ?>
          <span class="text-red-600 font-semibold mt-1">You have rejected the offer.</span>
        <?php endif; ?>
      <?php else: ?>
        <span class="text-gray-500 mt-1">Your offer letter has not been uploaded yet.</span>
      <?php endif; ?>
    </div>
  </div>
</main>

<script>
// Sidebar Toggle Logic
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');
const toggleBtn = document.getElementById('sidebar-toggle');

function openSidebar() {
  sidebar.classList.remove('-translate-x-full');
  overlay.classList.remove('hidden');
}
function closeSidebar() {
  sidebar.classList.add('-translate-x-full');
  overlay.classList.add('hidden');
}

if (toggleBtn) {
  toggleBtn.addEventListener('click', openSidebar);
}
if (overlay) {
  overlay.addEventListener('click', closeSidebar);
}
// Optionally close on link click in mobile
sidebar.querySelectorAll('a').forEach(function(link) {
  link.addEventListener('click', function() {
    if (window.innerWidth < 1024) closeSidebar();
  });
});
</script>

</body>
</html>
