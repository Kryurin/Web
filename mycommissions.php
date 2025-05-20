<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT
      id,
      category,
      description,
      status,
      created_at,
      completed_file,
      temp_file
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
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    th { background: #f0f0f0; }
    .btn-confirm, .btn-reject {
      border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;
    }
    .btn-confirm { background: #28a745; color: white; }
    .btn-confirm:hover { background: #218838; }
    .btn-reject { background: #dc3545; color: white; margin-left: 5px; }
    .btn-reject:hover { background: #c82333; }
    form.inline { display: inline-block; margin-top: 5px; }
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
    <p>You haven’t posted any commissions yet.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Category</th>
          <th>Description</th>
          <th>Posted On</th>
          <th>Status</th>
          <th>Files</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($commissions as $c): ?>
          <tr>
            <td><?php echo $c['id']; ?></td>
            <td><?php echo htmlspecialchars($c['category']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($c['description'])); ?></td>
            <td><?php echo date("M j, Y g:i a", strtotime($c['created_at'])); ?></td>
            <td><?php echo htmlspecialchars($c['status']); ?></td>
            <td>
              <?php if ($c['status'] === 'Under Review' && !empty($c['temp_file'])): ?>
                <a href="uploads/completions/<?php echo htmlspecialchars($c['temp_file']); ?>" target="_blank">View Temp</a>
                <form method="POST" action="confirm_completion.php" class="inline">
                  <input type="hidden" name="commission_id" value="<?php echo $c['id']; ?>">
                  <button type="submit" class="btn-confirm">Confirm</button>
                </form>
                <form method="POST" action="reject_temp.php" class="inline">
                  <input type="hidden" name="commission_id" value="<?php echo $c['id']; ?>">
                  <button type="submit" class="btn-reject">Not Approve</button>
                </form>
              <?php elseif ($c['status'] === 'Completed' && !empty($c['completed_file'])): ?>
                <a href="uploads/completions/<?php echo htmlspecialchars($c['completed_file']); ?>" download>Download Final</a>
              <?php else: ?>
                N/A
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
</body>
</html>
