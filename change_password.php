
<?php
session_start();
require 'connection1.php'; // Include your database connection
 include 'header.php'; 
// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $email = $_SESSION['email'];

    // Fetch the current password from the database
    $query = "SELECT password FROM staff WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Check if the current password matches
        if ($user['password'] === $current_password) {
            // Update the password in the database
            $update_query = "UPDATE staff SET password = '$new_password' WHERE email = '$email'";
            if (mysqli_query($conn, $update_query)) {
                $_SESSION['message'] = "Password changed successfully.";
                $_SESSION['msg_type'] = "success";
            } else {
                $_SESSION['message'] = "Error updating password: " . mysqli_error($conn);
                $_SESSION['msg_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Current password is incorrect.";
            $_SESSION['msg_type'] = "error";
        }
        
        // Redirect to avoid form resubmission
        header('Location: change_password.php');
        exit();
    } else {
        $_SESSION['message'] = "User not found.";
        $_SESSION['msg_type'] = "error";
        header('Location: change_password.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        .container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        input {
            width: 70%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .alert {
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            margin-bottom: 15px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<div class="container">
<?php include 'sidebar_teacher.php'; ?>
    <h2>Change Password</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?= $_SESSION['msg_type']; ?>">
            <?= $_SESSION['message']; ?>
        </div>
        <?php 
            unset($_SESSION['message']); // Clear message after displaying
            unset($_SESSION['msg_type']);
        endif; 
    ?>

    <form action="change_password.php" method="POST">
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>

        <button type="submit">Change Password</button>
    </form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
