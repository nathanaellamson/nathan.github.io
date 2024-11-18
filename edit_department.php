
<?php
// Include your database connection file
require 'connection1.php'; 
include('header.php');  

// Initialize variables for form fields
$department_name = "";
$head_of_department = "";
$description = "";
$established_year = "";
$contact_number = "";
$email_address = "";

// Check if the department ID is set
if (isset($_GET['id'])) {
    $department_id = $_GET['id'];

    // Fetch department details from the database
    $stmt = $conn->prepare("SELECT d.*, s.email AS email_address FROM departments d 
                            JOIN staff s ON d.head_of_department = s.email
                            WHERE d.id = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $department_name = $row['department_name'];
        $head_of_department = $row['head_of_department'];
        $description = $row['description'];
        $established_year = $row['established_year'];
        $contact_number = $row['contact_number'];
        $email_address = $row['email_address'];
    } else {
        echo "Department not found.";
        exit;
    }
}

// Update department details if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $department_name = strtoupper(trim($_POST['department_name']));
    $head_of_department = trim($_POST['head_of_department']); // This should be the email
    $description = trim($_POST['description']);
    $established_year = trim($_POST['established_year']);
    $contact_number = trim($_POST['contact_number']);

    // Debugging output to confirm POST data
    // This line is useful for debugging; remove it in production
    error_log("Department Name: $department_name, Head of Department: $head_of_department, Description: $description, Established Year: $established_year, Contact Number: $contact_number");

    // Prepare the SQL query to update department details
    $stmt = $conn->prepare("UPDATE departments SET department_name=?, head_of_department=?, description=?, established_year=?, contact_number=? WHERE id=?");
    $stmt->bind_param("sssssi", $department_name, $head_of_department, $description, $established_year, $contact_number, $department_id);

    // Execute the query and check for success
    if ($stmt->execute()) {
        header("Location: view_departments.php?message=Department updated successfully!");
        exit();
    } else {
        $message = "Error: " . $stmt->error; // This should give you feedback if the update fails
    }

    // Close the statement
    $stmt->close();
}
?>

<div class="container">
    <?php include('sidebar_academic.php'); ?>
    <h1>Edit Department</h1>

    <!-- Display success/error message -->
    <?php if (isset($message)): ?>
        <h4><?php echo htmlspecialchars($message); ?></h4>
    <?php endif; ?>

    <form action="" method="POST" id="departmentForm">
        <label for="department_name">Department Name:</label>
        <input type="text" id="department_name" name="department_name" value="<?php echo htmlspecialchars($department_name); ?>" required oninput="this.value = this.value.toUpperCase();">

        <label for="head_of_department">Head of Department (Email):</label>
        <select id="head_of_department" name="head_of_department" required>
            <?php
            // Fetch staff emails from the database
            $staff_query = "SELECT email, full_name FROM staff";
            $staff_result = $conn->query($staff_query);

            while ($staff_row = $staff_result->fetch_assoc()) {
                $selected = ($staff_row['email'] == $head_of_department) ? "selected" : "";
                $style = ($selected) ? "style='color:red;'" : "";
                echo "<option value='" . htmlspecialchars($staff_row['email']) . "' $selected $style>" . htmlspecialchars($staff_row['full_name']) . "</option>";
            }
            ?>
        </select>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>

        <label for="established_year">Established Year:</label>
        <input type="text" id="established_year" name="established_year" value="<?php echo htmlspecialchars($established_year); ?>" required>

        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($contact_number); ?>" required>

        <input type="submit" value="Update Department">
    </form>
</div>

<?php include('footer.php'); ?>

<style>
    .container {
        width: 80%;
        margin: 20px auto;
        padding: 20px;
    }

    h1 {
        text-align: center;
        color: #333;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    input[type="text"], 
    select, 
    textarea {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 24px;
        font-size: 14px;
    }

    input[type="text"]:focus, 
    select:focus, 
    textarea:focus {
        border-color: #007bff;
        outline: none;
    }

    input[type="submit"] {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    input[type="submit"]:hover {
        background-color: #0056b3;
    }

    /* Message styling */
    h4 {
        color: orange;
        text-align: center;
    }
</style>
