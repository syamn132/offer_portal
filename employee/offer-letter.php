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

// Track if offer is available
$offer_available = $employee && $employee['offer_letter'];

// Accept or Reject Logic (only runs if offer is available)
if ($offer_available && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $response_date = date("Y-m-d H:i:s");

    if (isset($_POST['accept'])) {
        $update = $conn->prepare("UPDATE employees SET offer_status = 'accepted', offer_response_date = ? WHERE id = ?");
        $update->execute([$response_date, $employee_id]);
        $employee['offer_status'] = 'accepted';
        $employee['offer_response_date'] = $response_date;
    } elseif (isset($_POST['reject'])) {
        $update = $conn->prepare("UPDATE employees SET offer_status = 'rejected', offer_response_date = ? WHERE id = ?");
        $update->execute([$response_date, $employee_id]);
        $employee['offer_status'] = 'rejected';
        $employee['offer_response_date'] = $response_date;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Offer Letter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>.sidebar-overlay {background: rgba(0,0,0,0.3);}</style>
</head>
<body class="bg-gray-100 font-sans">

<!-- Header -->
<header class="bg-white shadow fixed top-0 w-full z-30 flex items-center justify-between px-4 py-3">
  <div class="flex items-center gap-2">
    <button id="sidebar-toggle" class="lg:hidden text-gray-600 focus:outline-none mr-2">
      <i class="fas fa-bars fa-lg"></i>
    </button>
    <h1 class="text-lg font-bold text-gray-800">Offer Letter</h1>
  </div>
  <div class="flex items-center gap-4">
    <span class="hidden md:block font-medium text-gray-600">
      Hi, <?= htmlspecialchars($employee['name']) ?>
    </span>
    <img src="https://ui-avatars.com/api/?name=<?= urlencode($employee['name']) ?>&background=6366f1&color=fff" alt="avatar" class="h-10 w-10 rounded-full object-cover border-2 border-indigo-200">
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
    <a href="upload-documents.php" class="flex items-center gap-3 text-gray-700 hover:bg-indigo-100 rounded px-4 py-2 font-medium <?php if(basename($_SERVER['PHP_SELF'])=='upload-documents.php')echo 'bg-indigo-50 border-l-4 border-indigo-500'; ?>">
      <i class="fas fa-folder-open w-5"></i> Upload Documents
    </a>
    <a href="offer-letter.php" class="flex items-center gap-3 text-gray-700 hover:bg-indigo-100 rounded px-4 py-2 font-medium <?php if(basename($_SERVER['PHP_SELF'])=='offer-letter.php')echo 'bg-indigo-50 border-l-4 border-indigo-500'; ?>">
      <i class="fas fa-file-contract w-5"></i> Offer Letter
    </a>
    <a href="logout.php" class="flex items-center gap-3 text-red-600 hover:bg-red-100 rounded px-4 py-2 font-medium">
      <i class="fas fa-sign-out-alt w-5"></i> Logout
    </a>
  </nav>
</aside>

<!-- Main Content -->
<main class="lg:ml-64 pt-20 px-6 min-h-screen">
  <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow-lg">
    <div class="mb-6 border-b pb-4">
      <h2 class="text-3xl font-bold text-gray-800 hidden lg:inline">Offer Letter</h2>
      <p class="text-sm text-gray-500 mt-1">Review and respond to your offer below</p>
    </div>

    <?php if (!$offer_available): ?>
      <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-6 py-8 rounded text-center text-xl font-semibold">
        <i class="fas fa-info-circle mr-2"></i>
        Offer Letter not yet uploaded. Please wait or contact HR.
      </div>
    <?php else: ?>
      <div class="mb-4 grid md:grid-cols-2 gap-6">
        <p><span class="font-semibold text-gray-700">Name:</span> <?= htmlspecialchars($employee['name']) ?></p>
        <p><span class="font-semibold text-gray-700">Email:</span> <?= htmlspecialchars($employee['email']) ?></p>
      </div>
      <?php
      // Very simple mobile detect
      $is_mobile = preg_match('/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', strtolower($_SERVER['HTTP_USER_AGENT']));
      ?>
      <?php if (!$is_mobile): ?>
        <div class="border rounded overflow-hidden mb-6" style="height: 80vh;">
          <iframe src="<?= $employee['offer_letter'] ?>" class="w-full h-full border-0"></iframe>
        </div>
      <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-4 mb-6 rounded">
          <i class="fas fa-file-pdf mr-2"></i>
          PDF preview not available on mobile. Please
          <a href="<?= $employee['offer_letter'] ?>" target="_blank" class="text-blue-700 underline font-semibold">download or open your offer letter</a>
          using a PDF reader app.
        </div>
      <?php endif; ?>
      <?php if ($employee['offer_status'] == 'pending'): ?>
        <div class="flex gap-4">
          <form method="POST">
            <button type="submit" name="accept" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded shadow">Accept Offer</button>
          </form>
          <form method="POST">
            <button type="submit" name="reject" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded shadow">Reject Offer</button>
          </form>
        </div>
      <?php elseif ($employee['offer_status'] == 'accepted'): ?>
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">
          ✅ You have <strong>accepted</strong> the offer on <?= date('F j, Y', strtotime($employee['offer_response_date'])) ?>.
        </div>
        <a href="<?= $employee['offer_letter'] ?>" download class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">Download Offer Letter</a>
      <?php elseif ($employee['offer_status'] == 'rejected'): ?>
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
          ❌ You have <strong>rejected</strong> the offer on <?= date('F j, Y', strtotime($employee['offer_response_date'])) ?>. Please contact HR for further assistance.
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <div class="mt-6 lg:hidden">
      <a href="dashboard.php" class="inline-block mt-4 bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded">⬅ Back to Dashboard</a>
    </div>
  </div>
</main>
<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');
const toggleBtn = document.getElementById('sidebar-toggle');
function openSidebar() {
  sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden');
}
function closeSidebar() { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }
if (toggleBtn) { toggleBtn.addEventListener('click', openSidebar);}
if (overlay) { overlay.addEventListener('click', closeSidebar);}
sidebar.querySelectorAll('a').forEach(function(link) {
  link.addEventListener('click', function() { if (window.innerWidth < 1024) closeSidebar(); });
});
</script>
</body>
</html>