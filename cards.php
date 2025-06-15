<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM cards WHERE user_id = ?");
$stmt->execute([$user_id]);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['block_card'])) {
    $card_id = $_POST['card_id'];
    try {
        $conn->prepare("UPDATE cards SET is_blocked = TRUE WHERE card_id = ? AND user_id = ?")->execute([$card_id, $user_id]);
        $success = "Card blocked successfully.";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_card'])) {
    $card_number = sprintf("%04d%04d%04d%04d", rand(1000, 9999), rand(1000, 9999), rand(1000, 9999), rand(1000, 9999));
    $card_holder = $_SESSION['full_name'];
    $expiry_date = date("m/y", strtotime("+2 years"));
    $card_type = 'Debit'; // Simplified for project
    try {
        $conn->prepare("INSERT INTO cards (user_id, card_type, card_number, card_holder, expiry_date) VALUES (?, ?, ?, ?, ?)")
            ->execute([$user_id, $card_type, $card_number, $card_holder, $expiry_date]);
        $success = "New card ordered successfully.";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_card'])) {
    $card_id = $_POST['card_id'];
    try {
        $stmt = $conn->prepare("SELECT is_blocked FROM cards WHERE card_id = ? AND user_id = ?");
        $stmt->execute([$card_id, $user_id]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($card && $card['is_blocked']) {
            $conn->prepare("DELETE FROM cards WHERE card_id = ? AND user_id = ?")->execute([$card_id, $user_id]);
            $success = "Card deleted successfully.";
            // Refresh the cards list after deletion
            $stmt = $conn->prepare("SELECT * FROM cards WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = "Card can only be deleted if blocked, or card not found.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
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
                    <li><a href="deposit.php"><span>💵</span> Deposit/Withdrawal</a></li>
                    <li class="active"><a href="cards.php"><span>💳</span> Card Information</a></li>
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
                <h1>Card Information</h1>
                <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
                <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
                <?php if (empty($cards)): ?>
                    <p style='color: #666;'>You have no cards yet. Click below to order a new one!</p>
                <?php else: ?>
                    <?php foreach ($cards as $card): ?>
                        <div class="dashboard-card">
                            <h3>💳 <?php echo $card['card_type']; ?> Card</h3>
                            <div class="card-display">
                                <div class="card-visual">
                                    <p class="card-number">**** **** **** <?php echo substr($card['card_number'], -4); ?></p>
                                    <p class="card-holder"><?php echo htmlspecialchars($card['card_holder']); ?></p>
                                    <p class="card-expiry"><?php echo $card['expiry_date']; ?></p>
                                    <p class="card-type"><?php echo $card['card_type']; ?></p>
                                </div>
                            </div>
                            <div class="card-controls">
                                <?php if (!$card['is_blocked']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="card_id" value="<?php echo $card['card_id']; ?>">
                                        <button type="submit" name="block_card" class="btn">Block Card</button>
                                    </form>
                                <?php else: ?>
                                    <p>Card is blocked</p>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="card_id" value="<?php echo $card['card_id']; ?>">
                                        <button type="submit" name="delete_card" class="btn" onclick="return confirm('Are you sure you want to delete this card?');">Delete Card</button>
                                    </form>
                                <?php endif; ?>
                                <button class="btn" disabled>Change PIN</button>
                            </div>
                            <?php if ($card['card_type'] == 'Credit'): ?>
                                <p class="credit-info">Credit Limit: $<?php echo number_format($card['credit_limit'], 2); ?></p>
                                <p class="credit-info">Available Credit: $<?php echo number_format($card['available_credit'], 2); ?></p>
                                <div class="card-controls">
                                    <button class="btn" disabled>View Statement</button>
                                    <button class="btn" disabled>Pay Bill</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="card-controls" style="margin-top: 20px;">
                    <form method="POST" style="display:inline;">
                        <button type="submit" name="order_card" class="btn">Order New Card</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</body>
</html>