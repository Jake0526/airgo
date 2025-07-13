<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

require_once 'config/database.php';
$conn = Database::getConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user profile
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    // Handle gracefully if no user found
    $user = [
        'phone' => '',
        'address' => '',
        'profile_picture' => ''
    ];
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    // Profile picture handling
    $profile_picture = $user['profile_picture'];
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Ensure uploads/ folder exists
        }

        $temp_name = $_FILES['profile_picture']['tmp_name'];
        $file_name = basename($_FILES['profile_picture']['name']);
        $profile_picture_new = uniqid() . '_' . $file_name;
        $upload_path = $upload_dir . $profile_picture_new;

        if (move_uploaded_file($temp_name, $upload_path)) {
            // Remove old picture
            if (!empty($user['profile_picture']) && file_exists($upload_dir . $user['profile_picture'])) {
                unlink($upload_dir . $user['profile_picture']);
            }
            $profile_picture = $profile_picture_new;
        } else {
            $error_message = "Error uploading the profile picture.";
        }
    }

    // Update user profile
    $stmt = $conn->prepare("UPDATE users SET phone = ?, address = ?, profile_picture = ? WHERE id = ?");
    $stmt->bind_param("sssi", $phone, $address, $profile_picture, $user_id);

    if ($stmt->execute()) {
        // Update the session data
        $_SESSION['phone'] = $phone;
        $_SESSION['address'] = $address;
        $_SESSION['profile_picture'] = $profile_picture;

        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Failed to update profile.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .container { width: 500px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        .form-group { margin-bottom: 15px; }
        .form-group label { font-weight: bold; display: block; }
        .form-group input, .form-group textarea { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
        .form-group img { margin-top: 10px; max-width: 120px; }
        .btn { background: #07353f; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .btn:hover { background-color: skyblue; }
        .message { padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Profile</h2>

    <?php if (isset($success_message)): ?>
        <div class="message success"><?= $success_message ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message error"><?= $error_message ?></div>
    <?php endif; ?>

    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" rows="4" required><?= htmlspecialchars($user['address']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="profile_picture">Profile Picture (Optional)</label>
            <input type="file" name="profile_picture">
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="uploads/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn">Update Profile</button>
    </form>
</div>
</body>
</html>
