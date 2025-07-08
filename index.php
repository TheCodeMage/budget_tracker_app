<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? '';

// Fetch summary (excluding deleted transactions)
$income_result = $conn->prepare("SELECT SUM(amount) AS total_income FROM transactions WHERE user_id = ? AND type = 'income' AND is_deleted = 0");
$income_result->bind_param("i", $user_id);
$income_result->execute();
$income = $income_result->get_result()->fetch_assoc()['total_income'] ?? 0;

$expense_result = $conn->prepare("SELECT SUM(amount) AS total_expense FROM transactions WHERE user_id = ? AND type = 'expense' AND is_deleted = 0");
$expense_result->bind_param("i", $user_id);
$expense_result->execute();
$expense = $expense_result->get_result()->fetch_assoc()['total_expense'] ?? 0;

$balance = $income - $expense;

// Fetch recent transactions (excluding deleted transactions)
$transactions_stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? AND is_deleted = 0 ORDER BY created_at DESC LIMIT 5");
$transactions_stmt->bind_param("i", $user_id);
$transactions_stmt->execute();
$transactions = $transactions_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Budget Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, <?= htmlspecialchars($user_name) ?></h2>
        <div>
            <a href="report.php" class="btn btn-info me-2">View Report</a> <!-- View report button -->
            <a href="auth/logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Income</h5>
                    <p class="card-text">$<?= number_format($income, 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Expense</h5>
                    <p class="card-text">$<?= number_format($expense, 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Balance</h5>
                    <p class="card-text">$<?= number_format($balance, 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Transaction Form -->
    <div class="card mb-4">
        <div class="card-header">Add Transaction</div>
        <div class="card-body">
            <form method="POST" action="add_transaction.php">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required>
                    </div>
                    <div class="col-md-4">
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="description" class="form-control" placeholder="Description">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Add Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header">Recent Transactions</div>
        <div class="card-body">
            <?php if ($transactions->num_rows === 0): ?>
                <p class="text-muted">No transactions found.</p>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($txn = $transactions->fetch_assoc()): ?>
                            <tr>
                                <td>$<?= number_format($txn['amount'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $txn['type'] === 'income' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($txn['type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($txn['description']) ?></td>
                                <td><?= date("M d, Y H:i", strtotime($txn['created_at'])) ?></td>
                                <td>
                                    <a href="edit_transaction.php?id=<?= $txn['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_transaction.php?id=<?= $txn['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div class="alert alert-success mt-3">Transaction deleted successfully.</div>
    <?php endif; ?>
</div>

</body>
</html>
