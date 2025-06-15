<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT account_id, account_type, account_number FROM accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deposit'])) {
    $account_id = $_POST['account'];
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if ($amount <= 0) {
        $error = "Amount must be greater than zero.";
    } else {
        $conn->beginTransaction();
        try {
            $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ? AND user_id = ?")
                ->execute([$amount, $account_id, $user_id]);
            $conn->prepare("INSERT INTO transactions (account_id, transaction_type, amount, description) VALUES (?, 'Deposit', ?, 'Deposit via online banking')")
                ->execute([$account_id, $amount]);
            $conn->commit();
            $success = "Deposit successful.";
        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Deposit failed: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['withdraw'])) {
    $account_id = $_POST['account'];
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if ($amount <= 0) {
        $error = "Amount must be greater than zero.";
    } else {
        $stmt = $conn->prepare("SELECT balance FROM accounts WHERE account_id = ? AND user_id = ?");
        $stmt->execute([$account_id, $user_id]);
        $balance = $stmt->fetch(PDO::FETCH_ASSOC)['balance'];

        if ($balance < $amount) {
            $error = "Insufficient balance.";
        } else {
            $conn->beginTransaction();
            try {
                $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ? AND user_id = ?")
                    ->execute([$amount, $account_id, $user_id]);
                $conn->prepare("INSERT INTO transactions (account_id, transaction_type, amount, description) VALUES (?, 'Withdrawal', ?, 'Withdrawal via online banking')")
                    ->execute([$account_id, $amount]);
                $conn->commit();
                $success = "Withdrawal successful.";
            } catch (PDOException $e) {
                $conn->rollBack();
                $error = "Withdrawal failed: " . $e->getMessage();
            }
        }
    }
}
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
                    <li><a href="accounts.php"><span>🧾</span> Accounts</a></li>
                    <li><a href="transfers.php"><span>🔄</span> Transfers</a></li>
                    <li class="active"><a href="deposit.php"><span>💵</span> Deposit/Withdrawal</a></li>
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
                <h1>Deposit/Withdrawal</h1>
                <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
                <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
                <div class="dashboard-card">
                    <h3>💵 Deposit Money</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Select Account</label>
                            <select name="account" required>
                                <option value="">Select Account</option>
                                <?php foreach ($accounts as $account): ?>
                                    <option value="<?php echo $account['account_id']; ?>">
                                        <?php echo $account['account_type'] . ' - ****' . substr($account['account_number'], -4); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" name="amount" step="0.01" required>
                        </div>
                        <button type="submit" name="deposit" class="btn">Deposit</button>
                    </form>
                </div>
                <div class="dashboard-card">
                    <h3>💵 Withdraw Money</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Select Account</label>
                            <select name="account" required>
                                <option value="">Select Account</option>
                                <?php foreach ($accounts as $account): ?>
                                    <option value="<?php echo $account['account_id']; ?>">
                                        <?php echo $account['account_type'] . ' - ****' . substr($account['account_number'], -4); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" name="amount" step="0.01" required>
                        </div>
                        <button type="submit" name="withdraw" class="btn">Withdraw</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</body>
</html>