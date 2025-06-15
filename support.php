<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
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
                    <li><a href="cards.php"><span>💳</span> Card Information</a></li>
                    <li><a href="feedback.php"><span>📝</span> Feedback</a></li>
                    <li><a href="profile.php"><span>👤</span> Profile</a></li>
                    <li class="active"><a href="support.php"><span>💬</span> Support</a></li>
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
                <h1>Support</h1>
                <div class="dashboard-card">
                    <h3>💬 Contact Support</h3>
                    <div class="support-options">
                        <div class="support-item">
                            <h4>📞 Phone Support</h4>
                            <p>Call us at: +1-800-555-1234</p>
                            <p>Available 24/7</p>
                        </div>
                        <div class="support-item">
                            <h4>💬 Live Chat</h4>
                            <p>Chat with our support team</p>
                            <button class="btn" disabled>Start Chat</button>
                        </div>
                        <div class="support-item">
                            <h4>📧 Email Support</h4>
                            <p>Send us an email at: support@bankingsystem.com</p>
                            <p>Response within 24 hours</p>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card">
                    <h3>❓ Frequently Asked Questions</h3>
                    <div class="faq">
                        <div class="faq-item">
                            <h4>How do I reset my password?</h4>
                            <p>Click on "Forgot Password" on the login page and follow the instructions.</p>
                        </div>
                        <div class="faq-item">
                            <h4>What are the transaction limits?</h4>
                            <p>Please contact support for information about transaction limits.</p>
                        </div>
                        <div class="faq-item">
                            <h4>How do I report a lost card?</h4>
                            <p>Call our 24/7 support line immediately or use the "Block Card" feature in your account.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>