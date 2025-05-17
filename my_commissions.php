<?php
session_start();
require 'db.php';
require 'functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: index.php");
    exit;
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
  JOIN users       u ON c.user_id = u.id
  WHERE c.taken_by = ?
    AND c.status   = 'In Progress'
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
    /* … reuse your existing styles … */
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
        <a
          href="search.php?release=<?php echo $c['id']; ?>"
          class="btn-release"
        >Release Commission</a>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
</body>
</html>
