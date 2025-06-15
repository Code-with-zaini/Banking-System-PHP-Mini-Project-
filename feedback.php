<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);
    $comments = filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_STRING);

    if ($rating < 1 || $rating > 5) {
        $error = "Rating must be between 1 and 5.";
    } else {
        try {
            $conn->prepare("INSERT INTO feedback (user_id, category, rating, comments) VALUES (?, ?, ?, ?)")
                ->execute([$user_id, $category, $rating, $comments]);
            $success = "Feedback submitted successfully.";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

$stmt = $conn->prepare("SELECT category, rating, comments, submitted_at FROM feedback WHERE user_id = ? ORDER BY submitted_at DESC");
$stmt->execute([$user_id]);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <li class="active"><a href="feedback.php"><span>📝</span> Feedback</a></li>
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
                <h1>Feedback</h1>
                <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
                <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
                <div class="dashboard-card">
                    <h3>📝 Submit Feedback</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" required>
                                <option value="General Feedback">General Feedback</option>
                                <option value="Bug Report">Bug Report</option>
                                <option value="Feature Request">Feature Request</option>
                                <option value="Complaint">Complaint</option>
                                <option value="Compliment">Compliment</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Rating</label>
                            <div class="rating">
                                <span class="star" onclick="setRating(1)">⭐</span>
                                <span class="star" onclick="setRating(2)">⭐</span>
                                <span class="star" onclick="setRating(3)">⭐</span>
                                <span class="star" onclick="setRating(4)">⭐</span>
                                <span class="star" onclick="setRating(5)">⭐</span>
                            </div>
                            <input type="hidden" name="rating" id="rating" required>
                        </div>
                        <div class="form-group">
                            <label>Your Feedback</label>
                            <textarea name="comments" required></textarea>
                        </div>
                        <button type="submit" class="btn">Submit Feedback</button>
                    </form>
                    <script>
                        function setRating(value) {
                            document.getElementById('rating').value = value;
                            document.querySelectorAll('.star').forEach((star, index) => {
                                star.classList.toggle('active', index < value);
                            });
                        }
                    </script>
                </div>
                <div class="dashboard-card">
                    <h3>📝 Your Previous Feedback</h3>
                    <?php if (empty($feedbacks)): ?>
                        <p>No feedback submitted yet.</p>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $f): ?>
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span><?php echo htmlspecialchars($f['category']); ?></span>
                                    <span class="feedback-date"><?php echo $f['submitted_at']; ?></span>
                                </div>
                                <p class="feedback-rating">Rating: <?php echo str_repeat('⭐', $f['rating']); ?></p>
                                <p><?php echo htmlspecialchars($f['comments']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>