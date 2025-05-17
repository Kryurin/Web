<?php
session_start();
require 'db.php';

// only commissioners can view
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'commissioner') {
    header("Location: index.php");
    exit;
}

// get freelancer ID from query
$view_id = intval($_GET['id'] ?? 0);
if ($view_id <= 0) {
    die("Invalid freelancer ID.");
}

// fetch freelancer profile
$stmt = $conn->prepare("
    SELECT 
      u.username,
      p.profile_picture,
      p.bio,
      p.skills,
      p.location
    FROM users AS u
    JOIN freelancer_profiles AS p ON u.id = p.user_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $view_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$profile) {
    die("Freelancer not found.");
}

// store for AJAX endpoints
$_SESSION['view_profile_id'] = $view_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($profile['username']); ?> — Profile</title>
  <style>
    body { font-family: Arial, sans-serif; background: #eef1f4; margin:0; padding:0; }
    nav { background: #2c3e50; color: white; padding:15px 20px; display:flex; justify-content:space-between; }
    nav a { color: #fff; margin-left:15px; text-decoration:none; }
    nav a:hover { text-decoration:underline; }
    .container { max-width:800px; margin:30px auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    .profile-pic { width:150px; height:150px; border-radius:50%; object-fit:cover; border:2px solid #ccc; }
    .section { margin-top:20px; }
    .buttons { margin-top:30px; display:flex; gap:10px; }
    .buttons button { padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer; }
    .buttons button:hover { background:#0056b3; }
    #dynamic-content { margin-top:30px; background:#fff; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); min-height:100px; }
  </style>
</head>
<body>

<nav>
  <div><strong>Commissioner Dashboard</strong></div>
  <div>
    <a href="chome.php">Home</a>
    <a href="search_freelancers.php">Search</a>
    <a href="mycommissions.php">Commissions</a>
    <a href="logout.php">Logout</a>
  </div>
</nav>

<div class="container">
  <center>
    <img src="<?php echo htmlspecialchars($profile['profile_picture']); ?>" class="profile-pic" alt="">
    <h2><?php echo htmlspecialchars($profile['username']); ?></h2>
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

  <div class="buttons">
    <button id="btn-photos">Photos</button>
    <button id="btn-ratings">Ratings</button>
    <button id="btn-thread">Public Thread</button>
  </div>

  <div id="dynamic-content">
    <p>Select one of the tabs above to view additional content.</p>
  </div>
</div>

<script>
  const dynamicContent = document.getElementById('dynamic-content');

  function loadContent(name) {
    const urls = {
      photos: 'photos_view.php',
      ratings: 'ratings.php',
      public_thread: 'public_thread.php?id=<?php echo $view_id; ?>'
    };

    const url = urls[name];
    if (!url) return;

    dynamicContent.innerHTML = '<p>Loading…</p>';
    fetch(url, { credentials: 'same-origin' })
      .then(res => {
        if (!res.ok) throw new Error(res.statusText);
        return res.text();
      })
      .then(html => {
        dynamicContent.innerHTML = html;
      })
      .catch(err => {
        console.error(err);
        dynamicContent.innerHTML = '<p style="color:red;">Failed to load content.</p>';
      });
  }

  // Delegate form submit on dynamically loaded content
document.addEventListener('submit', function(e) {
  if (e.target && e.target.matches('#dynamic-content form')) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch('public_thread.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
    })
    .then(res => res.text())
    .then(text => {
      if (text.trim() === 'success') {
        // Reload thread content after successful post
        fetch('public_thread.php?id=<?php echo $view_id; ?>', { credentials: 'same-origin' })
          .then(res => res.text())
          .then(html => {
            document.getElementById('dynamic-content').innerHTML = html;
          });
      } else {
        alert('Failed to post: ' + text);
      }
    })
    .catch(err => {
      alert('Error submitting post');
      console.error(err);
    });
  }
});


  document.getElementById('btn-photos').addEventListener('click', () => loadContent('photos'));
  document.getElementById('btn-ratings').addEventListener('click', () => loadContent('ratings'));
  document.getElementById('btn-thread').addEventListener('click', () => loadContent('public_thread'));
</script>

</body>
</html>
