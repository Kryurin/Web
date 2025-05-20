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
    $payment_amount = trim($_POST['payment_amount'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    $watermark = isset($_POST['watermark']) ? 1 : 0;

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

    if ($message === '' && !is_numeric($payment_amount)) {
        $message = "Please enter a valid payment amount.";
    }

    if ($message === '' && $payment_method === '') {
        $message = "Please select a payment method.";
    }

    if ($message === '') {
        $stmt = $conn->prepare("
            INSERT INTO commissions (user_id, category, description, payment_amount, payment_method, watermark)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issdsi", $user_id, $category, $description, $payment_amount, $payment_method, $watermark);
        if ($stmt->execute()) {
            $message = "Commission posted successfully.";
            $cat_select = $cat_custom = $description = $payment_amount = $payment_method = '';
            $watermark = 0;
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
    <label for="main_category">Main Category</label>
<select id="main_category" name="category_select" required>
    <option value="">-- Select a main category --</option>
    <option value="Artwork"      <?php if (!empty($cat_select) && $cat_select==='Artwork') echo 'selected'; ?>>Artwork</option>
    <option value="Design"       <?php if (!empty($cat_select) && $cat_select==='Design') echo 'selected'; ?>>Design</option>
    <option value="Development"  <?php if (!empty($cat_select) && $cat_select==='Development') echo 'selected'; ?>>Development</option>
    <option value="other"        <?php if (!empty($cat_select) && $cat_select==='other') echo 'selected'; ?>>Other</option>
  </select>

  <div id="subcategory-wrapper" style="display:none;">
    <label for="subcategory">Subcategory</label>
    <select id="subcategory" name="subcategory_select">
      <option value="">-- Select a subcategory --</option>
    </select>
  </div>

  <div id="custom-cat" style="display:none;">
    <label for="category_custom">Specify Category</label>
    <input
      type="text"
      id="category_custom"
      name="category_custom"
      placeholder="Enter custom category"
      value="<?php echo htmlspecialchars($cat_custom ?? ''); ?>"
    >
  </div>


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

    <label for="payment_amount">Payment Amount (₱)</label>
<input
  type="text"
  id="payment_amount"
  name="payment_amount"
  value="<?php echo htmlspecialchars($payment_amount ?? ''); ?>"
  required
>

<label for="payment_method">Preferred Payment Method</label>
  <select id="payment_method" name="payment_method" required>
    <option value="">-- Select a method --</option>
    <option value="PayPal"        <?php if (!empty($payment_method) && $payment_method==='PayPal') echo 'selected'; ?>>PayPal</option>
    <option value="Bank Transfer" <?php if (!empty($payment_method) && $payment_method==='Bank Transfer') echo 'selected'; ?>>Bank Transfer</option>
    <option value="Venmo"         <?php if (!empty($payment_method) && $payment_method==='Venmo') echo 'selected'; ?>>Venmo</option>
    <option value="Other"         <?php if (!empty($payment_method) && $payment_method==='Other') echo 'selected'; ?>>Other</option>
  </select>

  <label>
    <input
      type="checkbox"
      name="watermark"
      <?php if (!empty($watermark)) echo 'checked'; ?>
    >
    Add watermark to delivered work
  </label>


    <button type="submit" class="btn">Post Commission</button>
  </form>
  
</div>

<script>
const subcategories = {
  Artwork: [
    "Digital Artwork", "Sketch", "Comic", "Self Portrait", "Illustration",
    "Sculpture", "Abstract", "Fan Art", "Oil Painting", "Photorealism"
  ],
  Design: [
    "UI Web Designing", "Logo Designing", "T-Shirt Merchandise Designing", "Animation",
    "Infographic", "Poster Design", "Album/Book Cover Design",
    "Graphic Designing", "Business Identity Designing", "Icon Design"
  ],
  Development: [
    "Debugging", "Front-End Web/App Development", "Back-End Web/App Development",
    "Full-Stack Web/App Development", "2D Game Development", "3D Game Development",
    "Database Development", "Mobile App Development", "Cloud Computing", "Api Developement"
  ]
};

const mainCategory = document.getElementById('main_category');
const subCategory = document.getElementById('subcategory');
const subCategoryWrapper = document.getElementById('subcategory-wrapper');
const customCat = document.getElementById('custom-cat');

function populateSubcategories(category) {
  subCategory.innerHTML = '<option value="">-- Select a subcategory --</option>';
  if (subcategories[category]) {
    subcategories[category].forEach(sub => {
      const option = document.createElement('option');
      option.value = sub;
      option.textContent = sub;
      subCategory.appendChild(option);
    });
    subCategoryWrapper.style.display = 'block';
  } else {
    subCategoryWrapper.style.display = 'none';
  }
}

mainCategory.addEventListener('change', function () {
  const selected = this.value;
  if (selected === 'other') {
    customCat.style.display = 'block';
    subCategoryWrapper.style.display = 'none';
  } else {
    customCat.style.display = 'none';
    populateSubcategories(selected);
  }
});

// On page load, repopulate subcategories if Artwork/Design/Development was already selected
window.addEventListener('DOMContentLoaded', () => {
  const selected = mainCategory.value;
  if (subcategories[selected]) {
    populateSubcategories(selected);
  }
  if (selected === 'other') {
    customCat.style.display = 'block';
  }
});
</script>


</body>
</html>
