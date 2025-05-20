<?php
session_start();
require 'db.php';

// Redirect if user is not logged in or not a freelancer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if the user has a profile already
$query = "SELECT * FROM freelancer_profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile_exists = $result->num_rows > 0;
$existing_profile = $profile_exists ? $result->fetch_assoc() : null;
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = $_POST['bio'];
    $skills = $_POST['skills'];
    $location = $_POST['location'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $payment_method = $_POST['payment_method'];
    $payment_number = $_POST['payment_number'];

    // Handle profile picture upload
    $profile_picture = $existing_profile['profile_picture'] ?? null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = 'uploads/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    }

    if ($profile_exists) {
        // UPDATE profile
        $update_query = "UPDATE freelancer_profiles SET profile_picture=?, bio=?, skills=?, location=?, fname=?, lname=?, payment_method=?, payment_number=? WHERE user_id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssssssi", $profile_picture, $bio, $skills, $location, $fname, $lname, $payment_method, $payment_number, $user_id);
    } else {
        // INSERT new profile
        $insert_query = "INSERT INTO freelancer_profiles (user_id, profile_picture, bio, skills, location, fname, lname, payment_method, payment_number)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("issssssss", $user_id, $profile_picture, $bio, $skills, $location, $fname, $lname, $payment_method, $payment_number);
    }

    if ($stmt->execute()) {
        if ($profile_exists) {
            header("Location: flprofile2.php"); // After editing
        } else {
            header("Location: flhome.php"); // After first-time creation
        }
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
    <title><?php echo $profile_exists ? 'Edit Profile' : 'Create Profile'; ?></title>
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
        input[type="text"], textarea, input[type="file"], select {
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
    <h2><?php echo $profile_exists ? 'Edit Your Freelancer Profile' : 'Create Your Freelancer Profile'; ?></h2>
    <form action="flprofile.php" method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*" <?php echo $profile_exists ? '' : 'required'; ?>>
        </div>

        <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" name="fname" id="fname" required value="<?php echo htmlspecialchars($existing_profile['fname'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" name="lname" id="lname" required value="<?php echo htmlspecialchars($existing_profile['lname'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="bio">Bio:</label>
            <textarea name="bio" id="bio" rows="4" required><?php echo htmlspecialchars($existing_profile['bio'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="skills">Skills:</label>
            <input type="text" name="skills" id="skills" required value="<?php echo htmlspecialchars($existing_profile['skills'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" name="location" id="location" required value="<?php echo htmlspecialchars($existing_profile['location'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="payment_number">Payment Number / Email:</label>
            <input type="text" name="payment_number" id="payment_number" required value="<?php echo htmlspecialchars($existing_profile['payment_number'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="payment_method">Preferred Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">-- Select Payment Method --</option>
                <option value="GCash" <?php if (($existing_profile['payment_method'] ?? '') === 'GCash') echo 'selected'; ?>>GCash</option>
                <option value="PayMaya" <?php if (($existing_profile['payment_method'] ?? '') === 'PayMaya') echo 'selected'; ?>>PayMaya</option>
                <option value="PayPal" <?php if (($existing_profile['payment_method'] ?? '') === 'PayPal') echo 'selected'; ?>>PayPal</option>
                <option value="Cash" <?php if (($existing_profile['payment_method'] ?? '') === 'Cash') echo 'selected'; ?>>Cash</option>
            </select>
        </div>

        <button type="submit"><?php echo $profile_exists ? 'Update Profile' : 'Create Profile'; ?></button>
    </form>
</div>

</body>
</html>
