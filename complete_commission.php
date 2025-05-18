<?php
session_start();
require 'db.php';
require 'functions.php';  // Ensure this file contains your addNotification function

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: index.php");
    exit;
}

// Handle commission completion
if (isset($_POST['complete_commission'])) {
    $commission_id = $_POST['commission_id'];
    $uploaded_file = $_FILES['completed_file'];

    // File upload handling
    $target_dir = "uploads/completions/";
    $file_name = time() . "_" . basename($uploaded_file['name']);
    $target_file = $target_dir . $file_name;

    // Ensure the directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($uploaded_file['tmp_name'], $target_file)) {
        // Update commission status
        $stmt = $conn->prepare("UPDATE commissions SET status = 'Completed', completed_file = ? WHERE id = ? AND taken_by = ?");
        $stmt->bind_param('sii', $file_name, $commission_id, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

        // Send notification to commissioner
        $commissioner_id = $conn->query("SELECT user_id FROM commissions WHERE id = $commission_id")->fetch_assoc()['user_id'];
        addNotification($conn, $commissioner_id, "✅ Your commission has been marked as completed!");

        // Redirect back to the commissions page
        header("Location: my_commissions.php?completed=1");
        exit;
    } else {
        echo "<p>Failed to upload the file. Please try again.</p>";
    }
}

$conn->close();
?>
