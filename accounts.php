<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT account_type, account_number, balance FROM accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <li><a href="dashboard.php"><span>📊</span> Dashboard</a></li>
                    <li class="active"><a href="accounts.php"><span>🧾</span> Accounts</a></li>
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
                <h1>My Accounts</h1>
                <?php foreach ($accounts as $account): ?>
                    <div class="dashboard-card">
                        <h3>🧾 <?php echo $account['account_type']; ?> Account</h3>
                        <p class="balance">$<?php echo number_format($account['balance'], 2); ?></p>
                        <p class="account-number">Account: <?php echo $account['account_number']; ?></p>
                    </div>
                <?php endforeach; ?>
            </section>
        </div>
    </div>
</body>
</html>