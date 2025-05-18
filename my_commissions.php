<?php
session_start();
require 'db.php';
require 'functions.php';

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
        $stmt = $conn->prepare("UPDATE commissions SET status = 'Complete', completed_file = ? WHERE id = ? AND taken_by = ?");
        $stmt->bind_param('sii', $file_name, $commission_id, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        header("Location: my_commissions.php?completed=1");
        exit;
    } else {
        echo "<p>Failed to upload the file. Please try again.</p>";
    }
}

// Fetch in-progress commissions taken by this freelancer
$stmt = $conn->prepare("
  SELECT 
    c.id,
    c.category,
    c.description,
    c.created_at,
    u.username AS commissioner_name
  FROM commissions c
  JOIN users u ON c.user_id = u.id
  WHERE c.taken_by = ?
    AND c.status = 'In Progress'
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
    .btn-release {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 12px;
      background: #dc3545;
      color: white;
      text-decoration: none;
      border-radius: 4px;
    }
    .btn-release:hover { background: #c82333; }
    .btn-complete {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 12px;
      background: #28a745;
      color: white;
      text-decoration: none;
      border-radius: 4px;
    }
    .btn-complete:hover { background: #218838; }
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
    <p>You have no commissions “In Progress.”</p>
  <?php else: ?>
    <?php foreach ($results as $c): ?>
      <div class="commission">
        <h4><?php echo htmlspecialchars($c['category']); ?></h4>
        <small>
          Posted by <?php echo htmlspecialchars($c['commissioner_name']); ?> on
          <?php echo date("F j, Y, g:i a", strtotime($c['created_at'])); ?>
        </small>
        <p><?php echo nl2br(htmlspecialchars($c['description'])); ?></p>
        <a href="search.php?release=<?php echo $c['id']; ?>" class="btn-release">Release Commission</a>
        <form method="POST" action="complete_commission.php" enctype="multipart/form-data">
        <input type="hidden" name="commission_id" value="<?php echo $c['id']; ?>">
        <input type="file" name="completed_file" required>
        <button type="submit" name="complete_commission" class="btn-complete">Complete</button>
</form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>
</body>
</html>
