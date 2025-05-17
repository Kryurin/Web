<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    http_response_code(403);
    exit('<p>Access denied.</p>');
}

$logged_in_user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'freelancer') {
    $freelancer_id = $logged_in_user_id;
    $can_rate = false;
} elseif ($role === 'commissioner' && isset($_SESSION['view_profile_id'])) {
    $freelancer_id = (int)$_SESSION['view_profile_id'];
    $can_rate = $freelancer_id !== $logged_in_user_id;
} else {
    exit('<p>Error: no freelancer selected.</p>');
}

if ($can_rate && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating  = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = trim($_POST['comment'] ?? '');

    if ($rating >= 1 && $rating <= 5) {
        $ins = $conn->prepare("
          INSERT INTO ratings
            (freelancer_id, commissioner_id, rating, comment, created_at)
          VALUES (?, ?, ?, ?, NOW())
        ");
        $ins->bind_param('iiis', $freelancer_id, $logged_in_user_id, $rating, $comment);

        try {
            $ins->execute();
            echo "<p style='color:green;'>Thank you! Your rating has been recorded.</p>";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                echo "<p style='color:orange;'>You’ve already rated this freelancer.</p>";
            } else {
                throw $e;
            }
        }

        $ins->close();
    } else {
        echo "<p style='color:red;'>Invalid rating value.</p>";
    }
}

$stmt = $conn->prepare("
  SELECT COUNT(*) AS total_reviews, ROUND(AVG(rating),2) AS avg_score
  FROM ratings
  WHERE freelancer_id = ?
");
$stmt->bind_param('i', $freelancer_id);
$stmt->execute();
$stmt->bind_result($total_reviews, $avg_score);
$stmt->fetch();
$stmt->close();

$list = $conn->prepare("
  SELECT rating, comment, created_at
  FROM ratings
  WHERE freelancer_id = ?
  ORDER BY created_at DESC
");
$list->bind_param('i', $freelancer_id);
$list->execute();
$res = $list->get_result();
$list->close();

$conn->close();
?>


<style>
  .ratings-summary { margin-bottom:20px; }
  .ratings-summary .star { color: #f39c12; }
  .rating-form { margin-bottom:30px; background:#f9f9f9; padding:15px; border-radius:6px; }
  .rating-form select,
  .rating-form textarea { width:100%; padding:6px; margin-top:4px; border:1px solid #ccc; border-radius:4px; }
  .rating-form button { margin-top:10px; padding:8px 12px; background:#28a745; color:#fff; border:none; border-radius:4px; cursor:pointer; }
  .rating-form button:hover { background:#218838; }
  .review { border-bottom:1px solid #ddd; padding:10px 0; }
  .review:last-child { border:none; }
  .review .date { color:#666; font-size:0.9em; }
</style>

<div class="ratings-summary">
  <h4>Overall Rating</h4>
  <?php if ($total_reviews > 0): ?>
    <p>
      <span class="star">★</span>
      <strong><?= $avg_score ?>/5</strong>
      (<?= $total_reviews ?> review<?= $total_reviews > 1 ? 's' : '' ?>)
    </p>
  <?php else: ?>
    <p><span class="star">★</span> No reviews yet.</p>
  <?php endif; ?>
</div>

<?php if ($can_rate): ?>
  <div class="rating-form">
    <h4>Leave Your Rating</h4>
    <form method="post" action="ratings.php">
      <label for="rating">Your Score:</label>
      <select name="rating" id="rating" required>
        <option value="">— Select 1–5 —</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
      </select>

      <label for="comment">Comment (optional):</label>
      <textarea id="comment" name="comment" rows="3" placeholder="Your thoughts…"></textarea>

      <input type="hidden" name="commission_id" value="1"> <!-- adjust as needed -->
      <button type="submit">Submit</button>
    </form>
  </div>
<?php endif; ?>

<?php if ($res->num_rows): ?>
  <div class="all-reviews">
    <h4>All Reviews</h4>
    <?php while ($row = $res->fetch_assoc()): ?>
      <div class="review">
        <p>
          <span class="star">★</span>
          <strong><?= intval($row['rating']) ?>/5</strong>
        </p>
        <?php if (trim($row['comment'])): ?>
          <p><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
        <?php endif; ?>
        <div class="date">
          <?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
<?php endif; ?>
