<?php
session_start();
require 'db.php';

// Only commissioners may access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch this commissioner’s commissions
// (Assumes your `commissions` table has a `status` column)
$sql = "
    SELECT
      id,
      category,
      description,
      status,
      created_at
    FROM commissions
    WHERE user_id = ?
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$commissions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Commissions</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:0; }
    nav  { background: #2c3e50; color: white; padding:15px 20px; display:flex; justify-content:space-between; align-items:center; }
    nav .left { font-size: 20px; font-weight: bold; }
    nav .right a { color: white; margin-left:20px; text-decoration:none; }
    nav .right a:hover { text-decoration:underline; }

    .container { max-width: 900px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    h2 { margin-top: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    th { background: #f0f0f0; }
    .status-pending   { color: #e67e22; font-weight: bold; }
    .status-inprogress{ color: #2980b9; font-weight: bold; }
    .status-completed { color: #27ae60; font-weight: bold; }
    .no-records { padding: 20px; text-align: center; color: #666; }
  </style>
</head>
<body>

<nav>
  <div class="left">Commissioner Dashboard</div>
  <div class="right">
    <a href="chome.php">Home</a>
    <a href="freelancer_profile_comm.php">Search</a>
    <a href="mycommissions.php">Commissions</a>
    <a href="logout.php">Logout</a>
  </div>
</nav>

<div class="container">
  <h2>My Posted Commissions</h2>

  <?php if (empty($commissions)): ?>
    <div class="no-records">You haven’t posted any commissions yet.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Category</th>
          <th>Description</th>
          <th>Posted On</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($commissions as $c): ?>
          <tr>
            <td><?php echo htmlspecialchars($c['id']); ?></td>
            <td><?php echo htmlspecialchars($c['category']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($c['description'])); ?></td>
            <td><?php echo date("M j, Y g:i a", strtotime($c['created_at'])); ?></td>
            <td class="
              <?php 
                // map status to CSS class
                $s = strtolower($c['status']);
                echo 'status-' . str_replace(' ', '', $s);
              ?>
            ">
              <?php echo htmlspecialchars(ucfirst($c['status'])); ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

</body>
</html>
