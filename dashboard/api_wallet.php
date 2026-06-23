<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

require_once '../config/koneksi.php';

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Fetch wallets
    $stmt = $conn->prepare("SELECT id, card_name, card_type, last_digits, balance FROM wallets WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $wallets = [];
    while ($row = $result->fetch_assoc()) {
        $wallets[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $wallets]);
    exit();
} elseif ($method === 'POST') {
    // Add new wallet
    $card_name = $_POST['card_name'] ?? '';
    $card_type = $_POST['card_type'] ?? '';
    $last_digits = $_POST['last_digits'] ?? '';
    $balance = $_POST['balance'] ?? 0;
    
    // Basic validation
    if (empty($card_name) || empty($card_type) || empty($last_digits)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO wallets (user_id, card_name, card_type, last_digits, balance) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssd", $user_id, $card_name, $card_type, $last_digits, $balance);
    
    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;
        echo json_encode([
            'status' => 'success', 
            'message' => 'Wallet added successfully.',
            'data' => [
                'id' => $new_id,
                'card_name' => $card_name,
                'card_type' => $card_type,
                'last_digits' => $last_digits,
                'balance' => $balance
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add wallet.']);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
?>
