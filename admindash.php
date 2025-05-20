<?php
session_start();
require 'db.php';

// Only admins allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$announcement_msg = "";
$upload_dir = __DIR__ . '/uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$edit_announcement = null;
$announcements = [];

// ---- DELETE ANNOUNCEMENT ----
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("SELECT image_path FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old_image = $result->fetch_assoc()['image_path'] ?? null;
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($old_image && file_exists($upload_dir . $old_image)) {
            @unlink($upload_dir . $old_image);
        }
        $announcement_msg = "Announcement deleted successfully.";
    } else {
        $announcement_msg = "Error deleting announcement.";
    }
    $stmt->close();
}

// ---- UPDATE ANNOUNCEMENT ----
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $image = $_FILES['image'] ?? null;

    if ($title && $message) {
        $stmt = $conn->prepare("SELECT image_path FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $update_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $old_image = $result->fetch_assoc()['image_path'] ?? null;
        $stmt->close();

        $new_image_path = $old_image;

        if ($image && $image['error'] === UPLOAD_ERR_OK && in_array($image['type'], $allowed_types)) {
            $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            $filename = bin2hex(random_bytes(8)) . "." . $ext;
            $target = $upload_dir . $filename;
            if (move_uploaded_file($image['tmp_name'], $target)) {
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    @unlink($upload_dir . $old_image);
                }
                $new_image_path = $filename;
            }
        }

        $stmt = $conn->prepare("UPDATE announcements SET title = ?, message = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $message, $new_image_path, $update_id);
        $announcement_msg = $stmt->execute() ? "Announcement updated successfully." : "Error updating announcement.";
        $stmt->close();
    } else {
        $announcement_msg = "Both title and message are required.";
    }
}

// ---- CREATE ANNOUNCEMENT ----
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['update_id'])) {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $image = $_FILES['image'] ?? null;
    $image_path = null;

    if ($title && $message) {
        if ($image && $image['error'] === UPLOAD_ERR_OK && in_array($image['type'], $allowed_types)) {
            $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            $filename = bin2hex(random_bytes(8)) . "." . $ext;
            $target = $upload_dir . $filename;
            if (move_uploaded_file($image['tmp_name'], $target)) {
                $image_path = $filename;
            }
        }

        $stmt = $conn->prepare("INSERT INTO announcements (title, message, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $message, $image_path);
        $announcement_msg = $stmt->execute() ? "Announcement posted successfully." : "Error posting announcement.";
        $stmt->close();
    } else {
        $announcement_msg = "Both title and message are required.";
    }
}

// ---- FETCH FOR EDIT ----
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_announcement = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ---- FETCH ALL ANNOUNCEMENTS ----
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admindash.css">
</head>
<body>
<div class="container">
    <div class="top-bar">
        <h2>Admin Dashboard</h2>
        <div class="nav-buttons">
            <a href="admin_dashboard.php">Announcements</a>
            <a href="cmadmin.php">View Commissioners</a>
            <a href="fladmin.php">View Freelancers</a>
            <a href="reports.php">View Reports</a>
            <a class="logout" href="logout.php">Logout</a>
        </div>
    </div>

    <h3><?php echo $edit_announcement ? "Edit Announcement" : "Create New Announcement"; ?></h3>

    <?php if ($announcement_msg): ?>
        <p class="message"><?php echo htmlspecialchars($announcement_msg); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <?php if ($edit_announcement): ?>
            <input type="hidden" name="update_id" value="<?php echo $edit_announcement['id']; ?>">
        <?php endif; ?>

        <div>
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($edit_announcement['title'] ?? ''); ?>">
        </div>

        <div>
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($edit_announcement['message'] ?? ''); ?></textarea>
        </div>

        <div>
            <label for="image">Image (optional)</label>
            <input type="file" id="image" name="image">
            <?php if (!empty($edit_announcement['image_path'])): ?>
                <p>Current Image:</p>
                <img src="uploads/<?php echo htmlspecialchars($edit_announcement['image_path']); ?>" alt="Current Image">
            <?php endif; ?>
        </div>

        <button type="submit"><?php echo $edit_announcement ? "Update" : "Post"; ?> Announcement</button>
    </form>

    <h3>All Announcements</h3>
    <?php foreach ($announcements as $a): ?>
        <div class="announcement">
            <h4><?php echo htmlspecialchars($a['title']); ?></h4>
            <p><?php echo nl2br(htmlspecialchars($a['message'])); ?></p>
            <?php if (!empty($a['image_path'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($a['image_path']); ?>" alt="Announcement Image">
            <?php endif; ?>
            <small>Posted on <?php echo htmlspecialchars($a['created_at']); ?></small>
            <p class="actions">
                <a href="?edit=<?php echo $a['id']; ?>">Edit</a> |
                <a href="?delete=<?php echo $a['id']; ?>" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
            </p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>