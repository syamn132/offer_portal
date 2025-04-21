<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['employee_logged_in']) || !isset($_SESSION['force_password_change'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];
$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE employees SET password = ?, password_raw = NULL, reset_required = 0 WHERE id = ?");
        $stmt->execute([$hashed, $employee_id]);

        unset($_SESSION['force_password_change']);
        $success = "Password updated successfully. Redirecting to dashboard...";
        header("refresh:2;url=dashboard.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Set New Password</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-2 mb-4 rounded">
                <?= $error ?>
            </div>
        <?php elseif ($success): ?>
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 mb-4 rounded">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">New Password</label>
                <input type="password" name="new_password" class="w-full mt-1 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium">Confirm Password</label>
                <input type="password" name="confirm_password" class="w-full mt-1 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Update Password</button>
        </form>
    </div>
</body>
</html>
