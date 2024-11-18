
<?php
session_start();
require 'connection1.php'; // Include your database connection
include 'header.php'; 
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userType = $_POST['user_type'];
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier']);
    $image = $_FILES['profile_image'];

    // Check if image was uploaded without errors
    if ($image['error'] === 0) {
        // Define the target directory and file path
        $targetDir = "images/";
        $imageName = basename($image['name']);
        $targetFilePath = $targetDir . $imageName;

        // Move the uploaded file to the images directory
        if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
            // Update the database with the image path based on user type
            $column = $userType === 'student' ? 'registration_number' : 'email';
            $table = $userType === 'student' ? 'students' : 'staff';

            $updateQuery = "UPDATE $table SET profile_image = '$targetFilePath' WHERE $column = '$identifier'";

            if (mysqli_query($conn, $updateQuery)) {
                $success_message = "Profile image uploaded and saved successfully.";
            } else {
                $error_message = "Database error: " . mysqli_error($conn);
            }
        } else {
            $error_message = "Error in moving the uploaded file.";
        }
    } else {
        $error_message = "Error in file upload: " . $image['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Image</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background-color: #f4f4f4;
            height: 100%;
            width: 100%;
        }
        .upload-container {
            width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        select, input[type="text"], input[type="file"], button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
    <?php include 'sidebar_admission.php'; ?>
        
    </div>
    <div style="display: flex; flex: 1;">
        <div class="content">
            <div class="upload-container">
                <h2>Upload Profile Image</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <label for="user_type">User Type:</label>
                    <select id="user_type" name="user_type" required>
                        <option value="student">Student</option>
                        <option value="staff">Staff</option>
                    </select>
                    <label for="identifier">Registration Number (Student) or Email (Staff):</label>
                    <input type="text" id="identifier" name="identifier" required>
                    <label for="profile_image">Upload Profile Image:</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" required>
                    <button type="submit">Upload</button>
                </form>
            </div>
        </div>
    </div>
    
    <?php include('footer.php'); // Include footer ?>
</body>
</html>
