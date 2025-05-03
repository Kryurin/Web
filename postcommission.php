<?php
session_start();
require 'db.php';

// only commissioners
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// handle form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_select = $_POST['category_select'] ?? '';
    $cat_custom = trim($_POST['category_custom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // decide category
    if ($cat_select && $cat_select !== 'other') {
        $category = $cat_select;
    } elseif ($cat_custom !== '') {
        $category = $cat_custom;
    } else {
        $message = "Please choose or enter a category.";
    }

    if ($message === '' && $description === '') {
        $message = "Please enter a description.";
    }

    if ($message === '') {
        $stmt = $conn->prepare("
            INSERT INTO commissions (user_id, category, description)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $user_id, $category, $description);
        if ($stmt->execute()) {
            $message = "Commission posted successfully.";
            // clear form fields
            $cat_select = $cat_custom = $description = '';
        } else {
            $message = "Error posting commission. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Post a Commission</title>
  <style>
    body { font-family: Arial,sans-serif; background: #f4f6f9; margin:0; padding:20px; }
    .container { max-width:600px; margin: auto; background: #fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    h2 { margin-top:0; }
    .message { margin-bottom:15px; color: green; }
    .error   { color: red; }
    label { display:block; margin:10px 0 5px; }
    select, input[type="text"], textarea { width:100%; padding:8px; box-sizing:border-box; }
    textarea { resize: vertical; }
    .btn { margin-top:15px; padding:10px 20px; background:#27ae60; color:#fff; border:none; cursor:pointer; border-radius:4px; }
    .btn:hover { background:#219150; }
    #custom-cat { display: none; }
  </style>
</head>
<body>

<div class="container">
  <h2>Post a New Commission</h2>

  <?php if ($message): ?>
    <p class="message<?php echo strpos($message, 'Error') === 0 || strpos($message,'Please')===0 ? ' error' : ''; ?>">
      <?php echo htmlspecialchars($message); ?>
    </p>
  <?php endif; ?>

  <form method="POST" action="">
    <label for="category_select">Category</label>
    <select id="category_select" name="category_select">
      <option value="">-- Select a category --</option>
      <option value="Artwork"    <?php if (!empty($cat_select) && $cat_select==='Artwork') echo 'selected'; ?>>Artwork</option>
      <option value="Writing"    <?php if (!empty($cat_select) && $cat_select==='Writing') echo 'selected'; ?>>Writing</option>
      <option value="Design"     <?php if (!empty($cat_select) && $cat_select==='Design') echo 'selected'; ?>>Design</option>
      <option value="Development"<?php if (!empty($cat_select) && $cat_select==='Development') echo 'selected'; ?>>Development</option>
      <option value="other"      <?php if (isset($cat_select) && $cat_select==='other') echo 'selected'; ?>>Other</option>
    </select>

    <div id="custom-cat">
      <label for="category_custom">Specify Category</label>
      <input
        type="text"
        id="category_custom"
        name="category_custom"
        placeholder="Enter custom category"
        value="<?php echo htmlspecialchars($cat_custom ?? ''); ?>"
      >
    </div>

    <label for="description">Description</label>
    <textarea
      id="description"
      name="description"
      rows="6"
      placeholder="Describe what you need..."
      required
    ><?php echo htmlspecialchars($description ?? ''); ?></textarea>

    <button type="submit" class="btn">Post Commission</button>
  </form>
</div>

<script>
// Show/hide custom category field
document.getElementById('category_select').addEventListener('change', function() {
  var custom = document.getElementById('custom-cat');
  if (this.value === 'other') {
    custom.style.display = 'block';
  } else {
    custom.style.display = 'none';
    document.getElementById('category_custom').value = '';
  }
});
// on page load, if “other” was selected, display the field
if (document.getElementById('category_select').value === 'other') {
  document.getElementById('custom-cat').style.display = 'block';
}
</script>

</body>
</html>
