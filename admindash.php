<?php
session_start();
require 'db.php';

// only admins allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// initialize variables
$announcement_msg   = "";
$upload_dir         = __DIR__ . '/uploads/';
$allowed_types      = ['image/jpeg', 'image/png', 'image/gif'];
$edit_announcement  = null;

// ---- DELETE ANNOUNCEMENT ----
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // fetch the image path so we can unlink it
    $stmt = $conn->prepare("SELECT image_path FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($old_path);
    $stmt->fetch();
    $stmt->close();

    // delete row
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($old_path && file_exists($upload_dir . $old_path)) {
            @unlink($upload_dir . $old_path);
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
    $title     = trim($_POST['title']);
    $message   = trim($_POST['message']);
    $new_image = $_FILES['image'] ?? null;

    if ($title !== "" && $message !== "") {
        // first, fetch existing image_path
        $stmt = $conn->prepare("SELECT image_path FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $update_id);
        $stmt->execute();
        $stmt->bind_result($old_image_path);
        $stmt->fetch();
        $stmt->close();

        $image_path_to_store = $old_image_path;

        // if a new image was uploaded without errors, process it
        if ($new_image && $new_image['error'] === UPLOAD_ERR_OK) {
            if (in_array($new_image['type'], $allowed_types, true)) {
                // generate unique name
                $ext      = pathinfo($new_image['name'], PATHINFO_EXTENSION);
                $basename = bin2hex(random_bytes(8)) . "." . $ext;
                $target   = $upload_dir . $basename;
                if (move_uploaded_file($new_image['tmp_name'], $target)) {
                    // delete old file
                    if ($old_image_path && file_exists($upload_dir . $old_image_path)) {
                        @unlink($upload_dir . $old_image_path);
                    }
                    $image_path_to_store = $basename;
                }
            }
        }

        // update row
        $stmt = $conn->prepare("
            UPDATE announcements
               SET title = ?, message = ?, image_path = ?
             WHERE id    = ?
        ");
        $stmt->bind_param("sssi", $title, $message, $image_path_to_store, $update_id);
        if ($stmt->execute()) {
            $announcement_msg = "Announcement updated successfully.";
        } else {
            $announcement_msg = "Error updating announcement.";
        }
        $stmt->close();
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
        // handle upload if present
        if ($image && $image['error'] === UPLOAD_ERR_OK && in_array($image['type'], $allowed_types, true)) {
            $ext      = pathinfo($image['name'], PATHINFO_EXTENSION);
            $basename = bin2hex(random_bytes(8)) . "." . $ext;
            $target   = $upload_dir . $basename;
            if (move_uploaded_file($image['tmp_name'], $target)) {
                $image_path_to_store = $basename;
            }
        }

        $stmt = $conn->prepare("
            INSERT INTO announcements (title, message, image_path)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $title, $message, $image_path_to_store);
        if ($stmt->execute()) {
            $announcement_msg = "Announcement posted successfully.";
        } else {
            $announcement_msg = "Error posting announcement.";
        }
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
    $result = $stmt->get_result();
    $edit_announcement = $result->fetch_assoc();
    $stmt->close();
}

// ---- FETCH ALL ----
$announcements = [];
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .top-bar { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .logout { color: red; font-weight: bold; text-decoration: none; }
        .logout:hover { text-decoration: underline; }
        h2, h3 { margin-bottom: 10px; }
        .message { color: green; font-weight: bold; margin: 10px 0; }
        form { margin-bottom: 30px; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin: 5px 0 10px; }
        input[type="file"] { margin: 10px 0; }
        button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .announcement { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; background-color: #f9f9f9; }
        .announcement img { display: block; margin-top: 10px; max-width: 200px; }
        .announcement small { display: block; color: #555; margin-top: 5px; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="top-bar">
        <h2>Admin Dashboard</h2>
        <a class="logout" href="logout.php">Logout</a>
    </div>

    <h3><?php echo $edit_announcement ? "Edit Announcement" : "Create New Announcement"; ?></h3>

    <?php if ($announcement_msg): ?>
        <p class="message"><?php echo htmlspecialchars($announcement_msg); ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <?php if ($edit_announcement): ?>
            <input type="hidden" name="update_id" value="<?php echo $edit_announcement['id']; ?>">
        <?php endif; ?>

        <input type="text" name="title" placeholder="Announcement Title" required
               value="<?php echo htmlspecialchars($edit_announcement['title'] ?? ''); ?>">

        <textarea name="message" rows="5" placeholder="Announcement Message" required><?php
            echo htmlspecialchars($edit_announcement['message'] ?? '');
        ?></textarea>

        <input type="file" name="image">
        <?php if (!empty($edit_announcement['image_path'] ?? '')): ?>
            <p>Current Image:</p>
            <img src="uploads/<?php echo htmlspecialchars($edit_announcement['image_path']); ?>"
                 alt="Current Image">
        <?php endif; ?>

        <button type="submit">
            <?php echo $edit_announcement ? "Update" : "Post"; ?> Announcement
        </button>
    </form>

    <h3>All Announcements</h3>

    <?php foreach ($announcements as $a): ?>
        <div class="announcement">
            <h4><?php echo htmlspecialchars($a['title']); ?></h4>
            <p><?php echo nl2br(htmlspecialchars($a['message'])); ?></p>
            <?php if (!empty($a['image_path'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($a['image_path']); ?>" alt="">
            <?php endif; ?>
            <small>Posted on <?php echo htmlspecialchars($a['created_at']); ?></small>
            <p>
                <a href="?edit=<?php echo $a['id']; ?>">Edit</a> |
                <a href="?delete=<?php echo $a['id']; ?>"
                   onclick="return confirm('Are you sure you want to delete this announcement?');">
                   Delete
                </a>
            </p>
        </div>
    <?php endforeach; ?>

</body>
</html>
