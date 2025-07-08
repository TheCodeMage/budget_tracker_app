<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get transaction ID from the URL
$transaction_id = $_GET['id'] ?? null;

if (!$transaction_id) {
    die("Transaction ID is required.");
}

// Fetch transaction details
$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $transaction_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Transaction not found.");
}

$transaction = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST["amount"];
    $type = $_POST["type"];
    $description = $_POST["description"];

    // Update the transaction
    $stmt = $conn->prepare("UPDATE transactions SET amount = ?, type = ?, description = ? WHERE id = ?");
    $stmt->bind_param("dssi", $amount, $type, $description, $transaction_id);

    if ($stmt->execute()) {
        header("Location: index.php?updated=1");
        exit();
    } else {
        $error = "Failed to update transaction. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Edit Transaction</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($transaction['amount']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="type">Type</label>
            <select name="type" class="form-select" required>
                <option value="income" <?= $transaction['type'] == 'income' ? 'selected' : '' ?>>Income</option>
                <option value="expense" <?= $transaction['type'] == 'expense' ? 'selected' : '' ?>>Expense</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="description">Description</label>
            <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($transaction['description']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="../index.php" class="btn btn-link">Cancel</a>
    </form>
</div>
</body>
</html>
