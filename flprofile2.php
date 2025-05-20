<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM freelancer_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <style>
    body { font-family: Arial; background: #eef1f4; margin:0; padding:0; }
    nav { background: #343a40; color: white; padding:15px 20px; display:flex; justify-content:space-between; }
    nav .right a { color:#fff; margin-left:20px; text-decoration:none; }
    nav .right a:hover { text-decoration:underline; }
    .container { max-width:800px; margin:30px auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    .profile-picture { width:150px; height:150px; border-radius:50%; object-fit:cover; border:2px solid #ccc; }
    .section { margin-top:20px; }
    .buttons { margin-top:30px; display:flex; gap:15px; }
    .buttons button { padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer; }
    .buttons button:hover { background:#0056b3; }
    #dynamic-content { margin-top:30px; background:#fff; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<nav>
  <div class="left">Freelancer Profile</div>
  <div class="right">
    <a href="flhome.php">Home</a>
    <a href="search.php">Search</a>
    <a href="logout.php">Logout</a>
  </div>
</nav>

<div class="container">
  <center>
    <img src="<?php echo htmlspecialchars($profile['profile_picture']); ?>" class="profile-picture">
    <h2><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
  </center>

  <div class="section"><strong>Bio:</strong>
    <p><?php echo nl2br(htmlspecialchars($profile['bio'])); ?></p>
  </div>

  <div class="section"><strong>Skills:</strong>
    <p><?php echo htmlspecialchars($profile['skills']); ?></p>
  </div>

  <div class="section"><strong>Location:</strong>
    <p><?php echo htmlspecialchars($profile['location']); ?></p>
  </div>

  <div class="section"><strong>Payment Details:</strong>
  <p><strong>Method:</strong> <?php echo htmlspecialchars($profile['payment_method']); ?></p>
  <p><strong>Number/Email:</strong> <?php echo htmlspecialchars($profile['payment_number']); ?></p>
  </div>

  <div class="buttons">
    <button id="btn-photos">Photos</button>
    <button id="btn-ratings">Ratings</button>
    <button id="btn-thread">Public Thread</button>
    <a href="flprofile.php" style="text-decoration:none;">
      <button type="button">Edit Profile</button>
    </a>
  </div>

  <div id="dynamic-content"></div>
</div>

<script>
const dynamicContent = document.getElementById('dynamic-content');

function loadContent(name) {
  const urls = {
    photos: 'photos.php',
    ratings: 'ratings.php',
    public_thread: 'public_thread.php'
  };
  const url = urls[name];
  if (!url) return;

  console.log('⌛ Fetching', url);
  fetch(url)
    .then(r => r.text())
    .then(html => {
      dynamicContent.innerHTML = `<div id="public-thread-container">${html}</div>`;
      if (name === 'photos') initPhotoForm();
    })
    .catch(err => {
      console.error('Error loading', url, err);
      dynamicContent.innerHTML = "<p style='color:red;'>Failed to load content.</p>";
    });
}

document.getElementById('btn-photos').addEventListener('click', () => loadContent('photos'));
document.getElementById('btn-ratings').addEventListener('click', () => loadContent('ratings'));
document.getElementById('btn-thread').addEventListener('click', () => loadContent('public_thread'));

function initPhotoForm() {
  const form = document.getElementById('photo-upload-form');
  const input = document.getElementById('photos');
  if (!form || !input) return;

  input.addEventListener('change', () => {
    if (input.files.length > 3) {
      alert('You can only select up to 3 photos.');
      input.value = '';
    }
  });

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(form);
    fetch('photos.php', { method: 'POST', body: fd })
      .then(r => r.text())
      .then(html => {
        dynamicContent.innerHTML = html;
        initPhotoForm();
      })
      .catch(err => {
        console.error('Upload failed', err);
        alert('Upload failed.');
      });
  });
}

// Public thread form submission handler
$(document).on("submit", "#public-thread-container form", function(e) {
  e.preventDefault();
  var form = $(this);
  $.post("public_thread.php", form.serialize(), function(response) {
  $("#public-thread-container").html(response);
}).fail(function() {
  alert("Failed to submit the thread.");
});

});
</script>

</body>
</html>