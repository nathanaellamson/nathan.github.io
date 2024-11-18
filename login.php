<?php
session_start();
require 'connection1.php'; // Include your database connection
include 'header1.php'; 
// Check if the user is already logged in, redirect if true
if (isset($_SESSION['email'])) {
    // Redirect based on user role
    switch ($_SESSION['role']) {
        case 'Admin':
            header("Location: admin_dashboard.php");
            break;
        case 'Academic Head':
            header("Location: academic_dashboard.php");
            break;
        case 'Head of Department':
            header("Location: hod_dashboard.php");
            break;
        case 'Lecturer':
            header("Location: teacher_dashboard.php");
            break;
        case 'Admission Officer':
            header("Location: admission_dashboard.php");
            break;
        default:
            header("Location: unauthorized.php"); // Fallback for unrecognized roles
            break;
    }
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Query to check if the user exists with the provided email and password
    $query = "SELECT * FROM staff WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Check if the password matches
        if ($user['password'] === $password) {

            // Store user information in session
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['department'] = $user['department']; // Store department in session


            // Redirect based on user role
            switch ($_SESSION['role']) {
                case 'Admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'Academic Head':
                    header("Location: academic_dashboard.php");
                    break;
                case 'Head of Department':
                    header("Location: hod_dashboard.php");
                    break;
                case 'Lecturer':
                    header("Location: teacher_dashboard.php");
                    break;
                case 'Admission Officer':
                    header("Location: admission_dashboard.php");
                    break;
                default:
                    header("Location: login.php"); // Fallback for unrecognized roles
                    break;
            }
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px #000;
            border-radius: 8px;
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

        .footer {
            margin-top: 15px;
            text-align: center;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
        /* Logo Styling */
.college-logo {
    width: 80px; /* Adjust width as needed */
    height: auto; /* Maintain aspect ratio */
    display: block;
    margin: 0 auto 20px auto; /* Center and add space below the logo */
}

    </style>
</head>
<body>

<div class="login-container">
<img src="images/logo.png" alt="College Logo" class="college-logo">
    <h2>Staff Login</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert">
            <?= $error_message; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <input type="email" id="email" name="email" placeholder="Username" required>

        <input type="password" id="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>
    <div class="footer">
            <p>Not a stuff? <a href="index.php">Login as Student</a></p>
        </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
