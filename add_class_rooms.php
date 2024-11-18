
<?php
// Include database connection
require 'connection1.php';

// Include header
include 'header.php';

$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $building = $_POST['building'];

    // Check for empty fields
    if (!empty($name) && !empty($capacity) && !empty($building)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO class_rooms (name, capacity, building) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $name, $capacity, $building);

        if ($stmt->execute()) {
            $message = "Classroom added successfully!";
        } else {
            $message = "Error: Could not add classroom.";
        }
        $stmt->close();
    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Optional CSS for styling -->
    <title>Add Classroom</title>
    <style>
           body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        display: flex;
        height: 100vh; /* Full height of the viewport */
    }

    .container {
        display: flex;
        flex-direction: column;
        justify-content: flex-start; /* Align items at the start */
        align-items: stretch; /* Allow items to stretch to full width */
        width: 100%;
        height: 100%; /* Full height */
    }

    main {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
      
        width: 80%;
        max-width: 1200px; /* Set a maximum width for larger devices */
        margin: 0 auto; /* Center the main content */
        flex-grow: 1; /* Allow main to grow and fill available space */
        overflow-y: auto; /* Enable vertical scrolling if needed */
    }

       

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .message {
            text-align: center;
            color: green;
            font-size: 1em;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }

        button:hover {
            background-color: #218838;
        }

        
    </style>
</head>
<body>

    <div class="container">
           <!-- Include Sidebar -->
    <?php include 'sidebar_academic.php'; ?>
        <main>
            <h2>Add New Classroom</h2>
            
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Classroom Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="capacity">Capacity</label>
                    <input type="number" id="capacity" name="capacity" required>
                </div>
                <div class="form-group">
                    <label for="building">Building</label>
                    <input type="text" id="building" name="building" required>
                </div>
                <button type="submit">Add Classroom</button>
            </form>
        </main>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
