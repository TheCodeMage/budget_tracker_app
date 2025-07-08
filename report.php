<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Get transactions for the user (excluding deleted transactions)
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? AND is_deleted = 0 ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
$total_income = 0;
$total_expense = 0;
$income_by_month = [];
$expense_by_month = [];

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    $month = date("Y-m", strtotime($row['created_at']));
    
    if ($row['type'] === 'income') {
        $total_income += $row['amount'];
        $income_by_month[$month] = ($income_by_month[$month] ?? 0) + $row['amount'];
    } else {
        $total_expense += $row['amount'];
        $expense_by_month[$month] = ($expense_by_month[$month] ?? 0) + $row['amount'];
    }
}

$balance = $total_income - $total_expense;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report - Budget Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Transaction Report</h2>
        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <!-- CSV Export Button -->
    <form method="POST" action="export_csv.php" class="mb-4"> <!-- <-- NEW -->
        <button type="submit" class="btn btn-success">Export to CSV</button> <!-- <-- NEW -->
    </form> <!-- <-- NEW -->

    <!-- Income and Expense Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Income</h5>
                    <p class="card-text">$<?= number_format($total_income, 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Expenses</h5>
                    <p class="card-text">$<?= number_format($total_expense, 2) ?></p>
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

    <!-- Monthly Income & Expense Chart -->
    <div class="card mb-4">
        <div class="card-header">Monthly Income vs Expense</div>
        <div class="card-body">
            <canvas id="monthlyReportChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Detailed Transaction List -->
    <div class="card">
        <div class="card-header">All Transactions</div>
        <div class="card-body">
            <?php if (empty($transactions)): ?>
                <p class="text-muted">No transactions available.</p>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td>$<?= number_format($txn['amount'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $txn['type'] === 'income' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($txn['type']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($txn['description']) ?></td>
                                <td><?= date("M d, Y H:i", strtotime($txn['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('monthlyReportChart').getContext('2d');
    const labels = <?= json_encode(array_keys($income_by_month)) ?>;
    const incomeData = <?= json_encode(array_values($income_by_month)) ?>;
    const expenseData = <?= json_encode(array_values($expense_by_month)) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Income',
                data: incomeData,
                backgroundColor: 'rgba(0, 255, 0, 0.5)',
                borderColor: 'green',
                borderWidth: 1
            }, {
                label: 'Expense',
                data: expenseData,
                backgroundColor: 'rgba(255, 0, 0, 0.5)',
                borderColor: 'red',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
