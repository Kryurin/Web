<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';

// Determine the profile being viewed
$view_id = intval($_GET['id'] ?? $_POST['view_id'] ?? $_SESSION['user_id'] ?? 0);
if ($view_id <= 0) {
    die("Invalid user ID.");
}

// Handle form submission (new thread or reply)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    $parent_id = intval($_POST['parent_id'] ?? 0);

    if ($content === '') {
        die("Content cannot be empty.");
    }

    $stmt = $conn->prepare("INSERT INTO public_threads (user_id, freelancer_id, parent_id, content, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $user_id, $view_id, $parent_id, $content);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error posting message.";
    }
    exit;
}

// Fetch thread messages for the viewed profile
$stmt = $conn->prepare("
    SELECT pt.*, u.username 
    FROM public_threads pt
    JOIN users u ON pt.user_id = u.id
    WHERE pt.freelancer_id = ?
    ORDER BY pt.created_at ASC
");
$stmt->bind_param("i", $view_id);
$stmt->execute();
$result = $stmt->get_result();

$threads = [];
while ($row = $result->fetch_assoc()) {
    $threads[] = $row;
}
$stmt->close();

// Group replies under parent messages
$grouped = [];
foreach ($threads as $msg) {
    if ($msg['parent_id'] == 0) {
        $grouped[$msg['id']] = ['msg' => $msg, 'replies' => []];
    } else {
        $grouped[$msg['parent_id']]['replies'][] = $msg;
    }
}
?>

<div style="padding:10px;">
  <h3>Public Thread</h3>

  <form method="post">
    <input type="hidden" name="parent_id" value="0">
    <input type="hidden" name="view_id" value="<?php echo $view_id; ?>">
    <textarea name="content" placeholder="Post something…" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;"></textarea>
    <button type="submit" style="margin-top:10px; padding:8px 16px;">Post</button>
  </form>

  <hr>

  <?php if (empty($grouped)): ?>
    <p style="color:gray;">No posts yet.</p>
  <?php else: ?>
    <?php foreach ($grouped as $entry): ?>
      <div style="margin-top:20px; padding:10px; border:1px solid #ddd; border-radius:5px;">
        <strong><?php echo htmlspecialchars($entry['msg']['username']); ?></strong>
        <p><?php echo nl2br(htmlspecialchars($entry['msg']['content'])); ?></p>
        <small style="color:gray;"><?php echo $entry['msg']['created_at']; ?></small>

        <!-- Reply form -->
        <form method="post" style="margin-top:10px;">
          <input type="hidden" name="parent_id" value="<?php echo $entry['msg']['id']; ?>">
          <input type="hidden" name="view_id" value="<?php echo $view_id; ?>">
          <textarea name="content" required style="width:100%; padding:6px; border-radius:4px; border:1px solid #ccc;" placeholder="Reply…"></textarea>
          <button type="submit" style="margin-top:5px; padding:5px 12px;">Reply</button>
        </form>

        <?php if (!empty($entry['replies'])): ?>
          <div style="margin-top:10px; padding-left:15px; border-left:2px solid #ccc;">
            <?php foreach ($entry['replies'] as $reply): ?>
              <div style="margin-top:10px;">
                <strong><?php echo htmlspecialchars($reply['username']); ?></strong>
                <p><?php echo nl2br(htmlspecialchars($reply['content'])); ?></p>
                <small style="color:gray;"><?php echo $reply['created_at']; ?></small>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
