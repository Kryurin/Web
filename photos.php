<?php
// photos.php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role']!=='freelancer') {
  http_response_code(403);
  exit('Forbidden');
}
$user_id = $_SESSION['user_id'];

// ensure upload dir
$uploadDir = __DIR__.'/uploadsfl/';
if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);

// handle upload
if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_FILES['photos'])) {
  $count = count($_FILES['photos']['name']);
  if ($count>3) {
    echo "<p style='color:red;'>You can upload up to 3 photos.</p>";
  } else {
    for($i=0;$i<$count;$i++){
      if ($_FILES['photos']['error'][$i]===UPLOAD_ERR_OK){
        $ext=strtolower(pathinfo($_FILES['photos']['name'][$i],PATHINFO_EXTENSION));
        if (in_array($ext,['jpg','jpeg','png','gif'])){
          $new=uniqid('ph_',true).".$ext";
          if (move_uploaded_file($_FILES['photos']['tmp_name'][$i],"$uploadDir$new")){
            $rel="uploadsfl/$new";
            $stmt=$conn->prepare(
              "INSERT INTO freelancer_photos (user_id,file_path) VALUES (?,?)"
            );
            $stmt->bind_param("is",$user_id,$rel);
            $stmt->execute();
            $stmt->close();
          }
        }
      }
    }
  }
}

// fetch latest 3
$stmt=$conn->prepare(
  "SELECT file_path FROM freelancer_photos 
   WHERE user_id=? ORDER BY uploaded_at DESC LIMIT 3"
);
$stmt->bind_param("i",$user_id);
$stmt->execute();
$photos=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<div style="background:#fff;padding:15px;border-radius:8px;
            box-shadow:0 1px 4px rgba(0,0,0,0.1);">
  <form id="photo-upload-form" method="post" enctype="multipart/form-data"
        style="display:flex;gap:10px;align-items:center;">
    <input type="file" name="photos[]" id="photos" accept="image/*" multiple required>
    <button type="submit" style="padding:8px 16px;
            background:#007bff;color:#fff;border:none;
            border-radius:4px;cursor:pointer;">Upload</button>
  </form>
</div>

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
