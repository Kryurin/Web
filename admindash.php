<?php
session_start();
require 'db.php';

// only admins allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$announcement_msg   = "";
$upload_dir         = __DIR__ . '/uploads/';
$allowed_types      = ['image/jpeg', 'image/png', 'image/gif'];
$edit_announcement  = null;

// ---- DELETE ANNOUNCEMENT ----
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt_select = $conn->prepare("SELECT image_path FROM announcements WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $old_image_data = $result_select->fetch_assoc();
    $old_path = $old_image_data['image_path'] ?? null;
    $stmt_select->close();

    $stmt_delete = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt_delete->bind_param("i", $id);
    if ($stmt_delete->execute()) {
        if ($old_path && file_exists($upload_dir . $old_path)) {
            @unlink($upload_dir . $old_path);
        }
        $announcement_msg = "Announcement deleted successfully.";
    } else {
        $announcement_msg = "Error deleting announcement.";
    }
    $stmt_delete->close();
}

// ---- UPDATE ANNOUNCEMENT ----
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);
    $title     = trim($_POST['title']);
    $message   = trim($_POST['message']);
    $new_image = $_FILES['image'] ?? null;

    if ($title !== "" && $message !== "") {
        $stmt_select = $conn->prepare("SELECT image_path FROM announcements WHERE id = ?");
        $stmt_select->bind_param("i", $update_id);
        $stmt_select->execute();
        $result_select = $stmt_select->get_result();
        $current_image_data = $result_select->fetch_assoc();
        $old_image_path = $current_image_data['image_path'] ?? null;
        $stmt_select->close();

        $image_path_to_store = $old_image_path;

        if ($new_image && $new_image['error'] === UPLOAD_ERR_OK) {
            if (in_array($new_image['type'], $allowed_types, true)) {
                $ext      = strtolower(pathinfo($new_image['name'], PATHINFO_EXTENSION));
                $basename = bin2hex(random_bytes(8)) . "." . $ext;
                $target   = $upload_dir . $basename;
                if (move_uploaded_file($new_image['tmp_name'], $target)) {
                    if ($old_image_path && file_exists($upload_dir . $old_image_path)) {
                        @unlink($upload_dir . $old_image_path);
                    }
                    $image_path_to_store = $basename;
                }
            }
        }

        $stmt_update = $conn->prepare("
            UPDATE announcements
               SET title = ?, message = ?, image_path = ?
             WHERE id    = ?
        ");
        $stmt_update->bind_param("sssi", $title, $message, $image_path_to_store, $update_id);
        if ($stmt_update->execute()) {
            $announcement_msg = "Announcement updated successfully.";
        } else {
            $announcement_msg = "Error updating announcement.";
        }
        $stmt_update->close();
    } else {
        $announcement_msg = "Both title and message are required for update.";
    }
}

// ---- CREATE NEW ANNOUNCEMENT ----
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['update_id'])) {
    $title   = trim($_POST['title']);
    $message = trim($_POST['message']);
    $image   = $_FILES['image'] ?? null;
    $image_path_to_store = null;

    if ($title !== "" && $message !== "") {
        if ($image && $image['error'] === UPLOAD_ERR_OK && in_array($image['type'], $allowed_types, true)) {
            $ext      = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            $basename = bin2hex(random_bytes(8)) . "." . $ext;
            $target   = $upload_dir . $basename;
            if (move_uploaded_file($image['tmp_name'], $target)) {
                $image_path_to_store = $basename;
            }
        }

        $stmt_insert = $conn->prepare("
            INSERT INTO announcements (title, message, image_path)
            VALUES (?, ?, ?)
        ");
        $stmt_insert->bind_param("sss", $title, $message, $image_path_to_store);
        if ($stmt_insert->execute()) {
            $announcement_msg = "Announcement posted successfully.";
        } else {
            $announcement_msg = "Error posting announcement.";
        }
        $stmt_insert->close();
    } else {
        $announcement_msg = "Both title and message are required.";
    }
}

// ---- FETCH FOR EDIT ----
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt_edit = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
    $stmt_edit->bind_param("i", $edit_id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    $edit_announcement = $result_edit->fetch_assoc();
    $stmt_edit->close();
}

// ---- FETCH BY ROLE ----
$role_filter = $_GET['role'] ?? null;
$announcements = [];
$users_by_role = [];

if ($role_filter === 'commissioner' || $role_filter === 'freelancer') {
    $stmt_users = $conn->prepare("SELECT id, username, email, role FROM users WHERE role = ? ORDER BY username ASC");
    $stmt_users->bind_param("s", $role_filter);
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();
    while ($row_user = $result_users->fetch_assoc()) {
        $users_by_role[] = $row_user;
    }
    $stmt_users->close();
} else {
    $result_announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
    if ($result_announcements) {
        while ($row_announcement = $result_announcements->fetch_assoc()) {
            $announcements[] = $row_announcement;
        }
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
            <a href="?role=">Announcements</a>
            <a href="?role=commissioner">View Commissioners</a>
            <a href="?role=freelancer">View Freelancers</a>
            <a href="reports.php">View Reports</a>
            <a class="logout" href="logout.php">Logout</a>
        </div>
    </div>

    <?php if (!($role_filter === 'commissioner' || $role_filter === 'freelancer')): ?>
        <h3><?php echo $edit_announcement ? "Edit Announcement" : "Create New Announcement"; ?></h3>

        <?php if ($announcement_msg): ?>
            <p class="message"><?php echo htmlspecialchars($announcement_msg); ?></p>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <?php if ($edit_announcement): ?>
                <input type="hidden" name="update_id" value="<?php echo $edit_announcement['id']; ?>">
            <?php endif; ?>

            <div>
                <label for="title">Announcement Title</label>
                <input type="text" id="title" name="title" placeholder="Enter title" required
                       value="<?php echo htmlspecialchars($edit_announcement['title'] ?? ''); ?>">
            </div>

            <div>
                <label for="message">Announcement Message</label>
                <textarea id="message" name="message" rows="5" placeholder="Enter message" required><?php
                    echo htmlspecialchars($edit_announcement['message'] ?? '');
                ?></textarea>
            </div>

            <div>
                <label for="image">Upload Image</label>
                <input type="file" id="image" name="image">
                <?php if (!empty($edit_announcement['image_path'] ?? '')): ?>
                    <p>Current Image:</p>
                    <img src="uploads/<?php echo htmlspecialchars($edit_announcement['image_path']); ?>" alt="Current Image">
                <?php endif; ?>
            </div>

            <button type="submit">
                <?php echo $edit_announcement ? "Update" : "Post"; ?> Announcement
            </button>
        </form>
    <?php endif; ?>

    <?php if ($users_by_role): ?>
        <h3>Users with role: <?php echo htmlspecialchars(ucfirst($role_filter)); ?></h3>
        <ul>
            <?php foreach ($users_by_role as $user): ?>
                <li><?php echo htmlspecialchars($user['username'] . " (" . $user['email'] . ")"); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <h3>All Announcements</h3>

        <?php foreach ($announcements as $a): ?>
            <div class="announcement">
                <h4><?php echo htmlspecialchars($a['title']); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($a['message'])); ?></p>
                <?php if (!empty($a['image_path'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($a['image_path']); ?>" alt="">
                <?php endif; ?>
                <small>Posted on <?php echo htmlspecialchars($a['created_at']); ?></small>
                <p class="actions">
                    <a href="?edit=<?php echo $a['id']; ?>">Edit</a> |
                    <a href="?delete=<?php echo $a['id']; ?>" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>