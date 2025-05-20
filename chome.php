<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1) Fetch notifications for this commissioner
$notifications = [];
$stmt = $conn->prepare("
    SELECT id, message, is_read, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    // Make commission completion messages clearer
    if (strpos($row['message'], 'commission completed') !== false) {
        $row['message'] = '✅ Your commission has been marked as completed.';
    }
    $notifications[] = $row;
}
$stmt->close();

// 2) Mark all as read (so next page load they appear as read)
$stmt = $conn->prepare("
    UPDATE notifications
    SET is_read = 1
    WHERE user_id = ? AND is_read = 0
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->close();

// 3) Get admin announcements (including image_path)
$announcements = [];
$sql = "
    SELECT title, message, created_at, image_path
    FROM announcements
    ORDER BY created_at DESC
";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
    $result->free();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Commissioner Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        nav {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav .left { font-size: 20px; font-weight: bold; }
        nav .right a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
        }
        nav .right a:hover { text-decoration: underline; }

        .container {
            padding: 20px;
        }
        .top-actions {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-post {
            display: inline-block;
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .btn-post:hover {
            background-color: #219150;
        }
        .welcome {
            font-size: 22px;
            margin-bottom: 20px;
        }

        /* Notifications */
        .notifications {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .notifications h3 {
            margin-top: 0;
        }
        .notification-item {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-item.unread .message {
            font-weight: bold;
        }
        .notification-item .timestamp {
            display: block;
            color: #666;
            font-size: 0.9em;
            margin-top: 4px;
        }

        /* Announcements */
        .feed {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .announcement {
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
        }
        .announcement h4 {
            margin: 0 0 5px;
            color: #333;
        }
        .announcement small {
            color: #666;
        }
        .announcement p {
            margin: 10px 0;
        }
        .announcement img {
            display: block;
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<nav>
    <div class="left">Commissioner Dashboard</div>
    <div class="right">
        <a href="chome.php">Home</a>
        <a href="search_freelancers.php">Search</a>
        <a href="mycommissions.php">Commissions</a>
        <a href="submit_report.php">Submit Report</a>

        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="welcome">
        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
    </div>

    <div class="top-actions">
        <a href="postcommission.php" class="btn-post">Post Commission</a>
    </div>

    <!-- Notifications section -->
    <?php if (!empty($notifications)): ?>
    <div class="notifications">
        <h3>Notifications</h3>
        <?php foreach ($notifications as $note): ?>
            <div class="notification-item<?php echo $note['is_read'] ? '' : ' unread'; ?>">
                <div class="message">
                    <?php echo htmlspecialchars($note['message']); ?>
                </div>
                <span class="timestamp">
                    <?php echo date("F j, Y, g:i a", strtotime($note['created_at'])); ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Admin Announcements -->
    <div class="feed">
        <h3>Admin Announcements</h3>

        <?php if (empty($announcements)): ?>
            <p>No announcements at the moment.</p>
        <?php else: ?>
            <?php foreach ($announcements as $a): ?>
                <div class="announcement">
                    <h4><?php echo htmlspecialchars($a['title']); ?></h4>
                    <small>
                        <?php echo date("F j, Y, g:i a", strtotime($a['created_at'])); ?>
                    </small>
                    <p><?php echo nl2br(htmlspecialchars($a['message'])); ?></p>

                    <?php if (!empty($a['image_path'])): ?>
                        <img 
                          src="uploads/<?php echo htmlspecialchars($a['image_path']); ?>" 
                          alt="Announcement Image"
                        >
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
