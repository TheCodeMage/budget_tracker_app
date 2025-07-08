<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Get the transaction ID from the URL
$transaction_id = $_GET['id'] ?? null;

if ($transaction_id) {
    // Prepare the SQL query to perform a soft delete (set is_deleted = 1)
    $stmt = $conn->prepare("UPDATE transactions SET is_deleted = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $transaction_id, $_SESSION['user_id']);
    $stmt->execute();
}

// Redirect to the index page with a success message
header("Location: index.php?deleted=1");
exit();
?>
