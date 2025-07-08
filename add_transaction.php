<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']);
    $type = $_POST['type']; // income or expense
    $description = trim($_POST['description']);

    if (!in_array($type, ['income', 'expense']) || $amount <= 0) {
        header("Location: index.php?error=Invalid+data");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $amount, $type, $description);
    $stmt->execute();
    header("Location: index.php?success=1");
    exit();
}
?>
