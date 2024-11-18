

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
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
<?php include 'header1.php'; ?>
    <div class="login-container">
    <img src="images/logo.png" alt="College Logo" class="college-logo">
        <h2>Student Login</h2>
        <?php if (isset($_GET['error'])): ?>
            <p class="message"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <form action="loginprocess.php" method="POST">
            <input type="text" name="student_username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- Footer for other users' login -->
        <div class="footer">
            <p>Not a student? <a href="login.php">Login as Staff user</a></p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
