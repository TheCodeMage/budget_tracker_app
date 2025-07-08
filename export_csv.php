<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Open output stream
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=transactions.csv');
$output = fopen('php://output', 'w');

// Add column headers
fputcsv($output, ['Amount', 'Type', 'Description', 'Date']);

// Add rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['amount'],
        ucfirst($row['type']),
        $row['description'],
        $row['created_at']
    ]);
}

fclose($output);
exit();
?>
