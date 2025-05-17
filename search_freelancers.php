<?php
session_start();
require 'db.php';

// Only commissioners can use this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    header("Location: index.php");
    exit;
}

// Search function
function searchFreelancers(mysqli $conn, string $term): array {
  $like = '%' . $term . '%';
  $sql = "
      SELECT 
        u.id,
        u.username,
        p.profile_picture,
        p.skills,
        p.location
      FROM users AS u
      JOIN freelancer_profiles AS p ON u.id = p.user_id
      WHERE u.status = 'active'   -- Only active freelancers
        AND (u.username LIKE ? OR p.skills LIKE ? OR p.location LIKE ?)
      ORDER BY u.username ASC
  ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $like, $like, $like);
  $stmt->execute();
  $res = $stmt->get_result();
  $out = $res->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  return $out;
}


$query   = trim($_GET['q'] ?? '');
$results = [];
if ($query !== '') {
    $results = searchFreelancers($conn, $query);
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Freelancers</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:0; }
    nav  { background: #2c3e50; color: white; padding:15px 20px; display:flex; justify-content:space-between; }
    nav .right a { color:#fff; margin-left:20px; text-decoration:none; }
    nav .right a:hover { text-decoration:underline; }
    .container { max-width:800px; margin:30px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    form { margin-bottom:20px; }
    input[type="text"] { width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; }
    button { margin-top:10px; padding:10px 20px; background:#27ae60; color:#fff; border:none; border-radius:4px; cursor:pointer; }
    button:hover { background:#219150; }
    .result { display:flex; align-items:center; padding:10px 0; border-bottom:1px solid #eee; }
    .result img { width:60px; height:60px; object-fit:cover; border-radius:50%; margin-right:15px; }
    .result a { font-size:18px; color:#333; text-decoration:none; }
    .result a:hover { text-decoration:underline; }
    .no-results { color:#666; padding:20px 0; text-align:center; }
  </style>
</head>
<body>

<nav>
  <div class="left">Commissioner Dashboard</div>
  <div class="right">
    <a href="chome.php">Home</a>
    <a href="search_freelancers.php">Search</a>
    <a href="mycommissions.php">Commissions</a>
    <a href="logout.php">Logout</a>
  </div>
</nav>

<div class="container">
  <form action="search_freelancers.php" method="get">
    <input
      type="text"
      name="q"
      placeholder="Search freelancers by name, skills, or location…"
      value="<?php echo htmlspecialchars($query); ?>"
      autofocus
    >
    <button type="submit">Search</button>
  </form>

  <?php if ($query === ''): ?>
    <p class="no-results">Enter a search term to find freelancers.</p>
  <?php elseif (empty($results)): ?>
    <p class="no-results">No freelancers found for “<?php echo htmlspecialchars($query); ?>”.</p>
  <?php else: ?>
    <?php foreach ($results as $f): ?>
      <div class="result">
        <img src="<?php echo htmlspecialchars($f['profile_picture']); ?>" alt="Profile pic">
        <div>
        <a href="freelancer_profile_comm.php?id=<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['username']); ?></a>
            
          </a>
          <div style="font-size:14px; color:#666;">
            Skills: <?php echo htmlspecialchars($f['skills']); ?><br>
            Location: <?php echo htmlspecialchars($f['location']); ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>
