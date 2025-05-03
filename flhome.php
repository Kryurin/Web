<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if freelancer has a profile
$stmt = $conn->prepare("SELECT * FROM freelancer_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: flprofile.php");
    exit;
}
$profile = $result->fetch_assoc();
$stmt->close();

// Get admin announcements (including image_path)
$announcements = [];
$announce_query = "
    SELECT title, message, created_at, image_path
      FROM announcements
  ORDER BY created_at DESC
";
$result_announce = $conn->query($announce_query);
if ($result_announce && $result_announce->num_rows > 0) {
    while ($row = $result_announce->fetch_assoc()) {
        $announcements[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Freelancer Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        nav {
            background: #343a40;
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
        .container { padding: 20px; }
        .welcome { font-size: 22px; margin-bottom: 20px; }
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
    <div class="left">Freelancer Dashboard</div>
    <div class="right">
        <a href="flhome.php">Home</a>
        <a href="search.php">Search</a>
        <a href="flprofile2.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="welcome">
        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
    </div>

    <div class="feed">
        <h3>Admin Announcements</h3>

        <?php if (empty($announcements)): ?>
            <p>No announcements at the moment.</p>
        <?php else: ?>
            <?php foreach ($announcements as $announce): ?>
                <div class="announcement">
                    <h4><?php echo htmlspecialchars($announce['title']); ?></h4>
                    <small>
                        <?php 
                            echo date(
                                "F j, Y, g:i a",
                                strtotime($announce['created_at'])
                            );
                        ?>
                    </small>
                    <p><?php echo nl2br(htmlspecialchars($announce['message'])); ?></p>

                    <?php if (!empty($announce['image_path'])): ?>
                        <img 
                          src="uploads/<?php 
                            echo htmlspecialchars($announce['image_path']); 
                          ?>" 
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
