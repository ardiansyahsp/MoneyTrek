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
    // Fetch savings
    $stmt = $conn->prepare("SELECT id, item_name, target_amount, current_amount FROM savings WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $savings = [];
    while ($row = $result->fetch_assoc()) {
        $savings[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $savings]);
    exit();
} elseif ($method === 'POST') {
    // Add new savings goal
    $item_name = $_POST['item_name'] ?? '';
    $target_amount = $_POST['target_amount'] ?? 0;
    $current_amount = $_POST['current_amount'] ?? 0;
    
    if (empty($item_name) || empty($target_amount)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit();
    }
    
    $target_amount = (float) $target_amount;
    $current_amount = (float) $current_amount;
    
    $stmt = $conn->prepare("INSERT INTO savings (user_id, item_name, target_amount, current_amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isdd", $user_id, $item_name, $target_amount, $current_amount);
    
    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;
        echo json_encode([
            'status' => 'success', 
            'message' => 'Savings goal added successfully.',
            'data' => [
                'id' => $new_id,
                'item_name' => $item_name,
                'target_amount' => $target_amount,
                'current_amount' => $current_amount
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add savings goal.']);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
?>
