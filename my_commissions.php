<?php
session_start();
require 'db.php';
require 'functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: index.php");
    exit;
}

// Handle temp file upload (for review)
if (isset($_POST['submit_temp'])) {
    $commission_id = $_POST['commission_id'];
    $uploaded_file = $_FILES['completed_file'];

    $target_dir = "uploads/completions/";
    $file_name = time() . "_" . basename($uploaded_file['name']);
    $target_file = $target_dir . $file_name;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($uploaded_file['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("UPDATE commissions SET status = 'Under Review', temp_file = ? WHERE id = ? AND taken_by = ?");
        $stmt->bind_param('sii', $file_name, $commission_id, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        header("Location: my_commissions.php?submitted=1");
        exit;
    } else {
        echo "<p>Failed to upload temporary file.</p>";
    }
}

// Handle final file upload (after confirmation)
if (isset($_POST['submit_final'])) {
    $commission_id = $_POST['commission_id'];
    $uploaded_file = $_FILES['final_file'];

    $target_dir = "uploads/completions/";
    $file_name = time() . "_" . basename($uploaded_file['name']);
    $target_file = $target_dir . $file_name;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($uploaded_file['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("UPDATE commissions SET status = 'Completed', completed_file = ? WHERE id = ? AND taken_by = ?");
        $stmt->bind_param('sii', $file_name, $commission_id, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        header("Location: my_commissions.php?final=1");
        exit;
    } else {
        echo "<p>Failed to upload final file.</p>";
    }
}

// Fetch in-progress and awaiting final commissions
$stmt = $conn->prepare("
  SELECT 
    c.id,
    c.category,
    c.description,
    c.created_at,
    c.status,
    u.username AS commissioner_name
  FROM commissions c
  JOIN users u ON c.user_id = u.id
  WHERE c.taken_by = ?
    AND c.status IN ('In Progress', 'Awaiting Final')
  ORDER BY c.created_at DESC
");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My In-Progress Commissions</title>
  <style>
    .btn {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 12px;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      border: none;
    }
    .btn:hover { background: #0056b3; }
    .btn-release { background: #dc3545; }
    .btn-release:hover { background: #c82333; }
    form { margin-bottom: 20px; }
  </style>
</head>
<body>
<nav>
  <a href="flhome.php">Home</a>
  <a href="search_freelancers.php">Search</a>
  <a href="my_commissions.php">My Commissions</a>
  <a href="logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Commissions I’m Working On</h2>

  <?php if (empty($results)): ?>
    <p>You have no commissions to work on.</p>
  <?php else: ?>
    <?php foreach ($results as $c): ?>
      <div class="commission">
        <h4><?php echo htmlspecialchars($c['category']); ?></h4>
        <small>
          Posted by <?php echo htmlspecialchars($c['commissioner_name']); ?> on
          <?php echo date("F j, Y, g:i a", strtotime($c['created_at'])); ?>
        </small>
        <p><?php echo nl2br(htmlspecialchars($c['description'])); ?></p>

        <a href="search.php?release=<?php echo $c['id']; ?>" class="btn btn-release">Release Commission</a>

        <?php if ($c['status'] === 'In Progress'): ?>
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="commission_id" value="<?php echo $c['id']; ?>">
            <label>Submit for Review (Temp File):</label><br>
            <input type="file" name="completed_file" required>
            <button type="submit" name="submit_temp" class="btn">Submit for Review</button>
          </form>

        <?php elseif ($c['status'] === 'Awaiting Final'): ?>
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="commission_id" value="<?php echo $c['id']; ?>">
            <label>Upload Final File:</label><br>
            <input type="file" name="final_file" required>
            <button type="submit" name="submit_final" class="btn">Upload Final</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>
</body>
</html>
