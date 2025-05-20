<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit;
}

$report_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $image_path = null;

    // Handle image upload if exists
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/reports/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = uniqid('report_', true) . '.' . $file_ext;
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $target_path)) {
                $image_path = $target_path;
            }
        }
    }

    if ($subject !== "" && $message !== "") {
        $stmt = $conn->prepare("INSERT INTO reports (user_id, subject, message, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $subject, $message, $image_path);
        if ($stmt->execute()) {
            $report_msg = "Report submitted successfully.";
        } else {
            $report_msg = "Error submitting report.";
        }
        $stmt->close();
    } else {
        $report_msg = "Subject and message are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Report</title>
</head>
<body>
<h2>Submit a Report</h2>
<?php if ($report_msg): ?>
    <p><?php echo htmlspecialchars($report_msg); ?></p>
<?php endif; ?>
<form method="POST" action="" enctype="multipart/form-data">
    <label>Subject:</label><br>
    <input type="text" name="subject" required><br><br>

    <label>Message:</label><br>
    <textarea name="message" rows="5" required></textarea><br><br>

    <label>Upload Image (optional):</label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <button type="submit">Submit Report</button>
</form>
</body>
</html>
