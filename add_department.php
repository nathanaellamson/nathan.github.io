
<?php
// Include your database connection file
require 'connection1.php'; 

// Initialize a variable to store success/error messages
$message = "";

// Fetch staff members from the database, including email
$staff_result = $conn->query("SELECT id, full_name, email FROM staff");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $department_name = strtoupper(trim($_POST['department_name']));
    $head_of_department = trim($_POST['head_of_department']); // This will now be an ID from the staff table
    $description = trim($_POST['description']);
    $established_year = trim($_POST['established_year']);
    $contact_number = trim($_POST['contact_number']);
    $email_address = trim($_POST['email_address']); // This will now be set dynamically

    // Prepare the SQL query to insert data into the departments table
    $stmt = $conn->prepare("INSERT INTO departments (department_name, head_of_department, description, established_year, contact_number, email_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $department_name, $head_of_department, $description, $established_year, $contact_number, $email_address);

    // Execute the query and check for success
    if ($stmt->execute()) {
        $message = "Department registered successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Department</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional CSS for styling -->
    <style>
   body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.container {
    flex-grow: 1;
    width: 100%;
    margin: auto;
    background-color: #fff;
    padding: 20px;
    padding-bottom: 160px; /* Additional padding for submit button visibility */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


    h1 {
        text-align: center;
        color: #333;
    }

    

    label {
        font-weight: bold;
    }
    form {
        display: block;
        flex-direction: column;
        gap: 15px;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    select,
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
    }

    input[type="submit"] {
        background-color: #28a745;
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 4px;
        margin-top: 20px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: #218838;
    }

    .message {
        text-align: center;
        color: #d9534f;
        margin-bottom: 20px;
    }

    /* Footer styles */
    footer {
        background-color: #333;
        color: #fff;
        text-align: center;
        padding: 10px;
        position: fixed;
        bottom: 0;
        width: 100%;
    }

    /* Ensure content doesn't overlap with fixed footer */
    .container {
        padding-bottom: 120px; /* Extra padding to accommodate the footer */
    }
</style>

    <script>
        function updateEmail() {
            const headOfDepartmentSelect = document.getElementById('head_of_department');
            const emailInput = document.getElementById('email_address');
            const selectedOption = headOfDepartmentSelect.options[headOfDepartmentSelect.selectedIndex];
            emailInput.value = selectedOption.getAttribute('data-email');
        }
    </script>
</head>
<body>
    <?php include('header.php'); ?>

    <div class="container">
    <?php include('sidebar_academic.php'); ?>
        <h1>Register New Department</h1>

        <!-- Display success/error message -->
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="" method="POST" id="departmentForm">
            <label for="department_name">Department Name:</label>
            <input type="text" id="department_name" name="department_name" required oninput="this.value = this.value.toUpperCase();">

            <label for="head_of_department">Head of Department:</label>
            <select id="head_of_department" name="head_of_department" required onchange="updateEmail()">
                <option value="">Select Head of Department</option>
                <?php if ($staff_result->num_rows > 0): ?>
                    <?php while ($staff = $staff_result->fetch_assoc()): ?>
                        <option value="<?php echo $staff['id']; ?>" data-email="<?php echo $staff['email']; ?>">
                            <?php echo $staff['full_name']; ?>
                        </option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="">No staff available</option>
                <?php endif; ?>
            </select>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <label for="established_year">Established Year:</label>
            <input type="number" id="established_year" name="established_year" required min="1900" max="<?php echo date("Y"); ?>">

            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" >

            <label for="email_address">Email Address:</label>
            <input type="email" id="email_address" name="email_address" readonly required>

            <input type="submit" value="Register Department">
        </form>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>
