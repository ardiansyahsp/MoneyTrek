<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, username, password FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            // Set session username to the user's name for display in dashboard
            $_SESSION['username'] = !empty($row['name']) ? $row['name'] : $row['username'];
            header("Location: ../dashboard/dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Password salah!";
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan!";
    }

    $stmt->close();
    header("Location: login.php");
    exit();
}
?>
