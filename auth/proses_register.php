<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $username = $email; // fallback for unique column

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Password tidak cocok!";
        header("Location: login.php?tab=register");
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email sudah digunakan!";
        $stmt->close();
        header("Location: login.php?tab=register");
        exit();
    }
    $stmt->close();

    // Insert new user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, username, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $username, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php?tab=login");
    } else {
        $_SESSION['error'] = "Terjadi kesalahan sistem!";
        header("Location: login.php?tab=register");
    }

    $stmt->close();
    exit();
}
?>
