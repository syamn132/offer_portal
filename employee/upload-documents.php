<?php
session_start();
if (!isset($_SESSION['employee_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once("../config/db.php");
$employee_id = $_SESSION['employee_id'];
$success = "";
$error = "";

// Fetch employee info and documents
$stmt = $conn->prepare("SELECT name, email, emp_id, dob, photo, aadhar, pan, resume, education, experience, relieving FROM employees WHERE id = ?");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

$already_uploaded = false;
foreach (['photo','aadhar','pan','resume','education','experience','relieving'] as $doc) {
    if (!empty($employee[$doc])) {
        $already_uploaded = true;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$already_uploaded) {
    $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
    $fields = [
        'photo' => 'photos',
        'aadhar' => 'aadhar',
        'pan' => 'pan',
        'resume' => 'resumes',
        'education' => 'education',
        'experience' => 'experience',
        'relieving' => 'relieving'
    ];

    $file_paths = [];
    foreach ($fields as $field => $folder) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $file_name = time() . "_" . basename($_FILES[$field]['name']);
            $target_dir = "../uploads/documents/" . $folder . "/";
            $target_file = $target_dir . $file_name;
            $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_types)) {
                $error = "Invalid file type for $field.";
                break;
            }

            if (move_uploaded_file($_FILES[$field]['tmp_name'], $target_file)) {
                $file_paths[$field] = $target_file;
            } else {
                $error = "Failed to upload $field.";
                break;
            }
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE employees SET photo = ?, aadhar = ?, pan = ?, resume = ?, education = ?, experience = ?, relieving = ? WHERE id = ?");
        $stmt->execute([
            $file_paths['photo'] ?? null,
            $file_paths['aadhar'] ?? null,
            $file_paths['pan'] ?? null,
            $file_paths['resume'] ?? null,
            $file_paths['education'] ?? null,
            $file_paths['experience'] ?? null,
            $file_paths['relieving'] ?? null,
            $employee_id
        ]);
        $success = "Documents uploaded successfully.";
        $already_uploaded = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Documents</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    .sidebar-overlay { background: rgba(0,0,0,0.3);}
  </style>
</head>
<body class="bg-gray-100 font-sans">

<!-- Header -->
<header class="bg-white shadow fixed top-0 w-full z-30 flex items-center justify-between px-4 py-3">
  <div class="flex items-center gap-2">
    <button id="sidebar-toggle" class="lg:hidden text-gray-600 focus:outline-none mr-2">
      <i class="fas fa-bars fa-lg"></i>
    </button>
    <h1 class="text-lg font-bold text-gray-800">Documents Upload</h1>
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
    <a href="upload-documents.php" class="flex items-center gap-3 <?php if (basename($_SERVER['PHP_SELF'])=='upload-documents.php') echo 'bg-indigo-50 text-indigo-600 border-l-4 border-indigo-500'; else echo 'text-gray-700'; ?> hover:bg-indigo-50 rounded px-4 py-2 font-medium">
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
<main class="lg:ml-64 pt-20 px-6 min-h-screen">
  <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
      <?= $success ?>
    </div>
  <?php elseif ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <?= $error ?>
    </div>
  <?php endif; ?>

  <?php if ($already_uploaded): ?>
    <div class="max-w-xl mx-auto mt-10 bg-white rounded-xl shadow-lg px-8 py-8">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center gap-2 mb-2">
        <i class="fas fa-upload text-indigo-500"></i>
        Documents Upload
      </h2>
      <p class="text-sm text-gray-500 mb-4">You have already uploaded your documents.</p>
      <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded flex items-center">
        <i class="fas fa-info-circle mr-2"></i>
        Documents already uploaded. Please contact the admin for updates.
      </div>
    </div>
  <?php else: ?>
    <div class="max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-lg px-8 py-8">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center gap-2 mb-6">
        <i class="fas fa-upload text-indigo-500"></i>
        Documents Upload
      </h2>
      <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <?php
        $labels = [
          'photo' => 'Profile Photo',
          'aadhar' => 'Aadhar Card',
          'pan' => 'PAN Card',
          'resume' => 'Resume',
          'education' => 'Education Certificates',
          'experience' => 'Experience Letter',
          'relieving' => 'Relieving Letter'
        ];
        foreach ($labels as $name => $label): ?>
          <div>
            <label class="block mb-1 font-semibold text-gray-700"><?= $label ?></label>
            <input type="file" name="<?= $name ?>" class="w-full border border-gray-300 rounded px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400" accept=".jpg,.jpeg,.png,.pdf">
          </div>
        <?php endforeach; ?>
        <div class="pt-4">
          <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition duration-200">Upload Documents</button>
        </div>
      </form>
    </div>
  <?php endif; ?>
</main>

<script>
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
sidebar.querySelectorAll('a').forEach(function(link) {
  link.addEventListener('click', function() { 
      if (window.innerWidth < 1024) closeSidebar(); 
  });
});
</script>
</body>
</html>