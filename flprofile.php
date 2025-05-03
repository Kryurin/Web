<?php
session_start();
require 'db.php';

// Redirect if user is not logged in or not a freelancer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: index.php");
    exit;
}

// Check if the user has already created their profile
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM freelancer_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Profile already exists, redirect to the freelancer home page
    header("Location: flhome.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = $_POST['bio'];
    $skills = $_POST['skills'];
    $location = $_POST['location'];

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = 'uploads/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    } else {
        $profile_picture = null;
    }

    // Insert freelancer profile data into the database
    $insert_query = "INSERT INTO freelancer_profiles (user_id, profile_picture, bio, skills, location) 
                     VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("issss", $user_id, $profile_picture, $bio, $skills, $location);
    if ($stmt->execute()) {
        // Redirect to the freelancer home page after successful profile creation
        header("Location: flhome.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Create Your Freelancer Profile</h2>
        <form action="flprofile.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </div>

            <div class="form-group">
                <label for="bio">Bio:</label>
                <textarea name="bio" id="bio" rows="4" placeholder="Tell us about yourself..."></textarea>
            </div>

            <div class="form-group">
                <label for="skills">Skills:</label>
                <input type="text" name="skills" id="skills" placeholder="List your skills (comma separated)">
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" id="location" placeholder="Your location (city, country)">
            </div>

            <button type="submit">Create Profile</button>
        </form>
    </div>

</body>
</html>
