<?php
$host = 'localhost';
$db = 'u555781181_Portal42025';
$user = 'u555781181_Portal42025';
$pass = 'Portal42025';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
