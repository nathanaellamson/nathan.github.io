<?php
session_start();
include('header.php'); // Include header
require 'connection1.php'; // Include your database connection
// Ensure the student is logged in using their registration number or username
if (!isset($_SESSION['registration_number'])) {
    header("Location: index.php"); // Redirect to login if session not set
    exit();
}

$student_username = $_SESSION['registration_number'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the current and new passwords from the form
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Fetch the current password for the student
    $query = "SELECT password FROM students WHERE registration_number = '$student_username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $stored_password = $row['password'];

        // Verify the current password
        if ($stored_password === $current_password) {
            // Ensure new passwords match
            if ($new_password === $confirm_password) {
                // Update the password in the database
                $update_query = "UPDATE students SET password = '$new_password' WHERE registration_number = '$student_username'";
                if (mysqli_query($conn, $update_query)) {
                    $success_message = "Password changed successfully!";
                } else {
                    $error_message = "Error updating password. Please try again.";
                }
            } else {
                $error_message = "New password and confirmation do not match.";
            }
        } else {
            $error_message = "Current password is incorrect.";
        }
    } else {
        $error_message = "Student not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
           
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        button {
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

        .message {
            text-align: center;
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
    <?php include 'sidebar_student.php'; ?>
        <h2>Change Password</h2>
        <?php if (isset($error_message)): ?>
            <p class="message"><?php echo $error_message; ?></p>
        <?php elseif (isset($success_message)): ?>
            <p class="message success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit">Change Password</button>
        </form>
    </div>

    <?php include('footer.php'); // Include footer ?>
</body>
</html>
