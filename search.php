<?php
session_start();
require 'db.php';
require 'functions.php';



if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
unset($qs['take'], $qs['release']);
$qs_string = http_build_query($qs);

if (isset($_GET['take'])) {
    $cid = (int)$_GET['take'];

    $q = $conn->prepare("SELECT user_id FROM commissions WHERE id = ? AND status = 'Pending'");
    $q->bind_param('i', $cid);
    $q->execute();
    $q->bind_result($ownerId);
    if ($q->fetch()) {
        $q->close();

        $u = $conn->prepare("UPDATE commissions SET status = 'In Progress', taken_by = ? WHERE id = ?");
        $u->bind_param('ii', $user_id, $cid);
        $u->execute();
        $u->close();

        $msg = sprintf("Your commission #%d has been taken by %s.", $cid, $username);
        addNotification($conn, $ownerId, $msg);
    }

    header("Location: search.php?$qs_string");
    exit;
}

if (isset($_GET['release'])) {
    $cid = (int)$_GET['release'];

    $q = $conn->prepare("SELECT user_id FROM commissions WHERE id = ? AND status = 'In Progress' AND taken_by = ?");
    $q->bind_param('ii', $cid, $user_id);
    $q->execute();
    $q->bind_result($ownerId);
    if ($q->fetch()) {
        $q->close();

        $u = $conn->prepare("UPDATE commissions SET status = 'Pending', taken_by = NULL WHERE id = ?");
        $u->bind_param('i', $cid);
        $u->execute();
        $u->close();

        $msg = sprintf("Your commission #%d has been released by %s.", $cid, $username);
        addNotification($conn, $ownerId, $msg);
    }

    header("Location: search.php?$qs_string");
    exit;
}

function searchCommissions(mysqli $conn, string $term, string $category, string $subcategory, string $priceRange): array {
    $clauses = ["c.status = 'Pending'", "u.status = 'active'"];
    $types = '';
    $params = [];

    if ($term !== '') {
        $clauses[] = "(c.category LIKE ? OR c.description LIKE ?)";
        $like = '%' . $term . '%';
        $params[] = &$like;
        $params[] = &$like;
        $types .= 'ss';
    }

    if ($category !== '') {
        $clauses[] = "c.category = ?";
        $params[] = &$category;
        $types .= 's';
    }

    if ($subcategory !== '') {
        $clauses[] = "c.subcategory = ?";
        $params[] = &$subcategory;
        $types .= 's';
    }

    if ($priceRange !== '') {
        if ($priceRange === '1-250') {
            $clauses[] = "c.payment_amount BETWEEN 1 AND 250";
        } elseif ($priceRange === '251-500') {
            $clauses[] = "c.payment_amount BETWEEN 251 AND 500";
        } elseif ($priceRange === '501-750') {
            $clauses[] = "c.payment_amount BETWEEN 501 AND 750";
        } elseif ($priceRange === '751-1000') {
            $clauses[] = "c.payment_amount BETWEEN 751 AND 1000";
        }
    }

    $where = 'WHERE ' . implode(' AND ', $clauses);
    $sql = "
        SELECT
            c.id,
            c.category,
            c.subcategory,
            c.description,
            c.created_at,
            c.payment_amount,
            c.payment_method,
            c.watermark,
            c.taken_by,
            u.username AS commissioner_name
        FROM commissions c
        JOIN users u ON c.user_id = u.id
        $where
        ORDER BY c.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $out = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $out;
}

$query       = trim($_GET['q'] ?? '');
$cat_select  = $_GET['category_select'] ?? '';
$subcategory = $_GET['subcategory'] ?? '';
$price_range = $_GET['price_range'] ?? '';

$category = $cat_select ?: '';

$results = [];
if ($query !== '' || $category !== '' || $subcategory !== '' || $price_range !== '') {
    $results = searchCommissions($conn, $query, $category, $subcategory, $price_range);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Commissions</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:0; }
    nav  { background: #343a40; color: white; padding:15px 20px; display:flex; justify-content:space-between; align-items:center; }
    nav .left { font-size: 20px; font-weight: bold; }
    nav .right a { color: white; margin-left:20px; text-decoration:none; }
    nav .right a:hover { text-decoration:underline; }
    .container { max-width: 900px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    form.filters { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px; }
    form.filters input[type="text"], form.filters select {
      width: 100%; padding:10px; border:1px solid #ccc; border-radius:4px;
    }
    .commission { border-bottom:1px solid #ddd; padding:15px 0; }
    .commission:last-child { border-bottom: none; }
    .commission h4 { margin: 0 0 5px; color:#333; }
    .commission small { color: #666; }
    .commission p { margin:10px 0; }
    .btn-take, .btn-release {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 12px;
      color: white;
      text-decoration: none;
      border-radius: 4px;
    }
    .btn-take { background: #28a745; }
    .btn-take:hover { background: #218838; }
    .btn-release { background: #dc3545; }
    .btn-release:hover { background: #c82333; }
  </style>
</head>
<body>

<nav>
  <div class="left">Freelancer Dashboard</div>
  <div class="right">
    <a href="flhome.php">Home</a>
    <a href="search.php">Search</a>
    <a href="my_commissions.php">My Commissions</a>
    <a href="flprofile2.php">Profile</a>
    <a href="logout.php">Logout</a>
  </div>
</nav>

<div class="container">
  <h2>Search Commissions</h2>

  <form class="filters" method="get">
    <input type="text" name="q" placeholder="Keyword…" value="<?php echo htmlspecialchars($query); ?>">
    <select name="category_select">
      <option value="">Any Category</option>
      <option value="Artwork" <?php if ($cat_select === 'Artwork') echo 'selected'; ?>>Artwork</option>
      <option value="Design" <?php if ($cat_select === 'Design') echo 'selected'; ?>>Design</option>
      <option value="Development" <?php if ($cat_select === 'Development') echo 'selected'; ?>>Development</option>
    </select>
    <select name="subcategory">
      <option value="">Any Subcategory</option>
      <option value="Fan Art" <?php if ($subcategory === 'Fan Art') echo 'selected'; ?>>Fan Art</option>
      <option value="Logo Designing" <?php if ($subcategory === 'Logo Designing') echo 'selected'; ?>>Logo Designing</option>
      <option value="Front-End Web/App Development" <?php if ($subcategory === 'Front-End Web/App Development') echo 'selected'; ?>>Front-End Web/App Development</option>
    </select>
    <select name="price_range">
      <option value="">Any Price</option>
      <option value="1-250" <?php if ($price_range === '1-250') echo 'selected'; ?>>$1 - $250</option>
      <option value="251-500" <?php if ($price_range === '251-500') echo 'selected'; ?>>$251 - $500</option>
      <option value="501-750" <?php if ($price_range === '501-750') echo 'selected'; ?>>$501 - $750</option>
      <option value="751-1000" <?php if ($price_range === '751-1000') echo 'selected'; ?>>$751 - $1000</option>
    </select>
  </form>

  <?php if (empty($results)): ?>
    <p>No commissions found.</p>
  <?php else: ?>
    <?php foreach ($results as $c): ?>
      <div class="commission">
        <h4><?php echo htmlspecialchars($c['category']); ?> — <?php echo htmlspecialchars($c['subcategory'] ?? ''); ?></h4>
        <small>
          Posted by <?php echo htmlspecialchars($c['commissioner_name']); ?> on
          <?php echo date("F j, Y, g:i a", strtotime($c['created_at'])); ?>
        </small>
        <p><?php echo nl2br(htmlspecialchars($c['description'])); ?></p>
        <p><strong>Payment:</strong> $<?php echo htmlspecialchars($c['payment_amount']); ?> (<?php echo htmlspecialchars($c['payment_method']); ?>)</p>
        <p><strong>Watermark:</strong> <?php echo $c['watermark'] ? 'Yes' : 'No'; ?></p>

        <?php if ($c['taken_by'] === NULL): ?>
          <a href="search.php?<?php echo http_build_query(array_merge($qs, ['take'=>$c['id']])); ?>" class="btn-take">Take Commission</a>
        <?php elseif ($c['taken_by'] == $user_id): ?>
          <a href="search.php?<?php echo http_build_query(array_merge($qs, ['release'=>$c['id']])); ?>" class="btn-release">Release Commission</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>
