<?php
session_start();
require_once 'db.php'; // your DB connection

if (isset($_GET['resolve'])) {
    $reportId = $_GET['resolve'];
    $conn->query("UPDATE reports SET status = 'resolved' WHERE id = $reportId");
    header("Location: reports.php");
    exit();
}

// Delete report
if (isset($_GET['delete'])) {
    $reportId = $_GET['delete'];
    $conn->query("DELETE FROM reports WHERE id = $reportId");
    header("Location: reports.php");
    exit();
}

// Fetch reports
$reports = [];
$result = $conn->query("SELECT reports.*, users.username FROM reports JOIN users ON reports.user_id = users.id ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
    $result->free(); // Important: free result before any other queries
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Reports</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 20px; }
        .report-card {
            background: white; padding: 15px; margin-bottom: 15px;
            border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .report-image { max-width: 300px; margin-top: 10px; }
        .actions a {
            margin-right: 10px; text-decoration: none; padding: 5px 10px; border-radius: 4px;
        }
        .resolve { background-color: green; color: white; }
        .delete { background-color: red; color: white; }
    </style>
</head>
<body>

    <h1>User Reports</h1>

    <?php foreach ($reports as $report): ?>
        <div class="report-card">
            <p><strong>User:</strong> <?= htmlspecialchars($report['username']) ?></p>
            <p><strong>Subject:</strong> <?= htmlspecialchars($report['subject']) ?></p>
            <p><strong>Message:</strong><br><?= nl2br(htmlspecialchars($report['message'])) ?></p>
            <?php if (!empty($report['image_path']) && file_exists($report['image_path'])): ?>
                <img class="report-image" src="<?= htmlspecialchars($report['image_path']) ?>" alt="Attached image">
            <?php endif; ?>
            <p><strong>Status:</strong> <?= htmlspecialchars($report['status']) ?></p>
            <p><strong>Reported on:</strong> <?= $report['created_at'] ?></p>
            <div class="actions">
                <?php if ($report['status'] !== 'resolved'): ?>
                    <a class="resolve" href="?resolve=<?= $report['id'] ?>" onclick="return confirm('Mark as resolved?')">Mark as Resolved</a>
                <?php endif; ?>
                <a class="delete" href="?delete=<?= $report['id'] ?>" onclick="return confirm('Are you sure you want to delete this report?')">Delete</a>
            </div>
        </div>
    <?php endforeach; ?>

</body>
</html>
