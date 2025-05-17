<?php
session_start();
require 'db.php';

// only commissioners can view
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    http_response_code(403);
    exit('Forbidden');
}

// which freelancer are we viewing?
$freelancer_id = intval($_SESSION['view_profile_id'] ?? 0);
if ($freelancer_id <= 0) {
    exit('No freelancer selected.');
}

// fetch photos
$stmt=$conn->prepare(
    "SELECT file_path FROM freelancer_photos 
     WHERE user_id=? ORDER BY uploaded_at DESC LIMIT 5"
  );
  $stmt->bind_param("i",$freelancer_id);
  $stmt->execute();
  $photos=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  $conn->close();
  ?>



<div style="display:flex;gap:10px;margin-top:20px;flex-wrap:wrap;">
  <?php if (empty($photos)): ?>
    <p>No photos yet.</p>
  <?php else: foreach($photos as $p): ?>
    <div style="width:120px;height:120px;overflow:hidden;
                border:1px solid #ccc;border-radius:4px;">
      <img src="<?php echo htmlspecialchars($p['file_path']);?>"
           style="width:100%;height:100%;object-fit:cover;">
    </div>
  <?php endforeach; endif; ?>
</div>
