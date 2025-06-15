<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT SUM(balance) as total_balance FROM accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_balance = $stmt->fetch(PDO::FETCH_ASSOC)['total_balance'] ?? 0.00;

$stmt = $conn->prepare("SELECT t.*, a.account_type, a.account_number FROM transactions t JOIN accounts a ON t.account_id = a.account_id WHERE a.user_id = ? ORDER BY t.transaction_date DESC LIMIT 5");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BANKING SYSTEM</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>BANKING SYSTEM</h2>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="dashboard.php"><span>📊</span> Dashboard</a></li>
                    <li><a href="accounts.php"><span>🧾</span> Accounts</a></li>
                    <li><a href="transfers.php"><span>🔄</span> Transfers</a></li>
                    <li><a href="deposit.php"><span>💵</span> Deposit/Withdrawal</a></li>
                    <li><a href="cards.php"><span>💳</span> Card Information</a></li>
                    <li><a href="feedback.php"><span>📝</span> Feedback</a></li>
                    <li><a href="profile.php"><span>👤</span> Profile</a></li>
                    <li><a href="support.php"><span>💬</span> Support</a></li>
                </ul>
            </nav>
        </aside>
        <div class="main-content">
            <header class="main-header">
                <span>BANKING SYSTEM</span>
                <div>
                    <a href="support.php">Help</a>
                    <a href="logout.php" class="signout">Sign Out</a>
                </div>
            </header>
            <section class="page-section active">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
                <div class="dashboard-card">
                    <h3>📊 Dashboard</h3>
                    <p class="label">Total Balance</p>
                    <p class="balance">$<?php echo number_format($total_balance, 2); ?></p>
                    <p class="recent">Recent Transactions</p>
                    <div class="transaction-list">
                        <?php if (empty($transactions)): ?>
                            <p>No transactions yet.</p>
                        <?php else: ?>
                            <?php foreach ($transactions as $t): ?>
                                <p><?php echo $t['transaction_type'] . ': $' . number_format($t['amount'], 2) . ' (' . $t['account_type'] . ' - ' . substr($t['account_number'], -4) . ') on ' . $t['transaction_date']; ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="actions">
                    <a href="accounts.php"><button class="btn">🧾 View Accounts</button></a>
                    <a href="transfers.php"><button class="btn">🔄 Send Money</button></a>
                    <a href="deposit.php"><button class="btn">💵 Deposit/Withdraw</button></a>
                    <a href="cards.php"><button class="btn">💳 My Cards</button></a>
                </div>
            </section>
        </div>
    </div>
</body>
</html>