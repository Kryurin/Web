<?php
session_start();
require 'db.php';
require 'functions.php';    // <-- pull in addNotification()

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: index.php");
    exit;
}

$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Rebuild query string without `take` or `release` params
parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
unset($qs['take'], $qs['release']);
$qs_string = http_build_query($qs);

// —————————————— TAKE handler ——————————————
if (isset($_GET['take'])) {
    $cid = (int)$_GET['take'];

    // (a) Fetch the original commissioner
    $q = $conn->prepare("
      SELECT user_id
      FROM commissions
      WHERE id = ? AND status = 'Pending'
    ");
    $q->bind_param('i', $cid);
    $q->execute();
    $q->bind_result($ownerId);
    if ($q->fetch()) {
        $q->close();

        // (b) Mark commission as in-progress
        $u = $conn->prepare("
          UPDATE commissions
          SET status   = 'In Progress',
              taken_by = ?
          WHERE id = ?
        ");
        $u->bind_param('ii', $user_id, $cid);
        $u->execute();
        $u->close();

        // (c) Notify the commissioner
        $msg = sprintf(
            "Your commission #%d has been taken by %s.",
            $cid,
            $username
        );
        addNotification($conn, $ownerId, $msg);
    }

    header("Location: search.php?$qs_string");
    exit;
}

// ————————————— RELEASE handler —————————————
if (isset($_GET['release'])) {
    $cid = (int)$_GET['release'];

    // (a) Ensure this freelancer actually took it
    $q = $conn->prepare("
      SELECT user_id
      FROM commissions
      WHERE id = ? 
        AND status   = 'In Progress'
        AND taken_by = ?
    ");
    $q->bind_param('ii', $cid, $user_id);
    $q->execute();
    $q->bind_result($ownerId);
    if ($q->fetch()) {
        $q->close();

        // (b) Reset status to pending
        $u = $conn->prepare("
          UPDATE commissions
          SET status   = 'Pending',
              taken_by = NULL
          WHERE id = ?
        ");
        $u->bind_param('i', $cid);
        $u->execute();
        $u->close();

        // (c) Notify the commissioner
        $msg = sprintf(
            "Your commission #%d has been released by %s.",
            $cid,
            $username
        );
        addNotification($conn, $ownerId, $msg);
    }

    header("Location: search.php?$qs_string");
    exit;
}

/**
 * Search open (Pending) commissions by keyword/category.
 */
function searchCommissions(mysqli $conn, string $term, string $category): array {
  $clauses = ["c.status = 'Pending'", "u.status = 'active'"];  // ← Added this line
  $types   = '';
  $params  = [];
  
  if ($term !== '') {
      $clauses[] = "(c.category LIKE ? OR c.description LIKE ?)";
      $like      = '%' . $term . '%';
      $params[]  = &$like;
      $params[]  = &$like;
      $types    .= 'ss';
  }
  if ($category !== '') {
      $clauses[] = "c.category = ?";
      $params[]  = &$category;
      $types    .= 's';
  }

  $where = 'WHERE ' . implode(' AND ', $clauses);
  $sql = "
    SELECT
      c.id,
      c.category,
      c.description,
      c.created_at,
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


// Read filters
$query      = trim($_GET['q']               ?? '');
$cat_select = $_GET['category_select']      ?? '';
$cat_custom = trim($_GET['category_custom'] ?? '');

if ($cat_select && $cat_select !== 'other') {
    $category = $cat_select;
} elseif ($cat_select === 'other' && $cat_custom !== '') {
    $category = $cat_custom;
} else {
    $category = '';
}

// Fetch search results
$results = [];
if ($query !== '' || $category !== '') {
    $results = searchCommissions($conn, $query, $category);
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
    .container { max-width: 800px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    form.filters { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
    form.filters label { margin-bottom: 5px; }
    form.filters input[type="text"],
    form.filters select { width: 100%; padding:10px; border:1px solid #ccc; border-radius:4px; }
    #custom-cat { display: none; }
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

  <form id="searchForm" class="filters" action="search.php" method="get">
    <div>
      <label for="q">Keyword</label>
      <input
        type="text"
        id="q"
        name="q"
        placeholder="Keyword…"
        value="<?php echo htmlspecialchars($query); ?>"
        autocomplete="off"
      >
    </div>

    <div>
      <label for="category_select">Category</label>
      <select id="category_select" name="category_select">
        <option value="">-- Any category --</option>
        <option value="Artwork"     <?php if ($cat_select==='Artwork')     echo 'selected'; ?>>Artwork</option>
        <option value="Writing"     <?php if ($cat_select==='Writing')     echo 'selected'; ?>>Writing</option>
        <option value="Design"      <?php if ($cat_select==='Design')      echo 'selected'; ?>>Design</option>
        <option value="Development" <?php if ($cat_select==='Development') echo 'selected'; ?>>Development</option>
        <option value="other"       <?php if ($cat_select==='other')       echo 'selected'; ?>>Other</option>
      </select>
      <div id="custom-cat">
        <label for="category_custom">Specify Category</label>
        <input
          type="text"
          id="category_custom"
          name="category_custom"
          placeholder="Enter custom category"
          value="<?php echo htmlspecialchars($cat_custom); ?>"
          autocomplete="off"
        >
      </div>
    </div>
  </form>

  <?php if ($query === '' && $category === ''): ?>
    <p>Start typing or choose a category to see results instantly.</p>

  <?php elseif (empty($results)): ?>
    <p>No commissions found for "<strong><?php echo htmlspecialchars($query); ?></strong>"
       in category "<strong><?php echo htmlspecialchars($category ?: 'any'); ?></strong>".</p>

  <?php else: ?>
    <p>Found <?php echo count($results); ?> commission<?php echo count($results)>1?'s':''; ?>:</p>
    <?php foreach ($results as $c): ?>
      <div class="commission">
        <h4><?php echo htmlspecialchars($c['category']); ?></h4>
        <small>
          Posted by <?php echo htmlspecialchars($c['commissioner_name']); ?> on
          <?php echo date("F j, Y, g:i a", strtotime($c['created_at'])); ?>
        </small>
        <p><?php echo nl2br(htmlspecialchars($c['description'])); ?></p>

        <!-- Show Take or Release depending on status -->
        <?php if ($c['taken_by'] === NULL): ?>
          <a
            href="search.php?<?php echo http_build_query(array_merge($qs, ['take'=>$c['id']]));?>"
            class="btn-take"
          >Take Commission</a>
        <?php elseif ($c['taken_by'] == $user_id): ?>
          <a
            href="search.php?<?php echo http_build_query(array_merge($qs, ['release'=>$c['id']]));?>"
            class="btn-release"
          >Release Commission</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
  const form    = document.getElementById('searchForm');
  const inputQ  = document.getElementById('q');
  const selectC = document.getElementById('category_select');
  const custom  = document.getElementById('custom-cat');
  const inputCustom = document.getElementById('category_custom');
  let debounceTimer;

  function submitForm() {
    form.submit();
  }

  inputQ.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(submitForm, 500);
  });

  selectC.addEventListener('change', () => {
    if (selectC.value === 'other') {
      custom.style.display = 'block';
    } else {
      custom.style.display = 'none';
      inputCustom.value = '';
    }
    submitForm();
  });

  inputCustom.addEventListener('blur', submitForm);

  if (selectC.value === 'other') {
    custom.style.display = 'block';
  }
</script>

</body>
</html>
