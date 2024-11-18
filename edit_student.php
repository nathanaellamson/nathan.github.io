
<?php
include('header.php');
require 'connection1.php'; // Include your database connection

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert the ID to an integer for safety

    // Fetch the existing student data from the database
    $query = "SELECT * FROM students WHERE id = $id"; // Adjust based on your database structure
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        die("Student not found: " . mysqli_error($conn));
    }

    // Fetch the student data
    $student = mysqli_fetch_assoc($result);
} else {
    die("No student ID provided.");
}

// Handle form submission for updating student data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $surname = mysqli_real_escape_string($conn, $_POST['surname']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $program_registered = mysqli_real_escape_string($conn, $_POST['program_registered']);
    $year_of_admission = mysqli_real_escape_string($conn, $_POST['year_of_admission']);

    // Update student data in the database
    $update_query = "UPDATE students SET first_name='$first_name', surname='$surname', gender='$gender', dob='$dob', email='$email', phone='$phone', course='$program_registered', year_of_admission='$year_of_admission' WHERE id = $id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: view_students.php?message=Student updated successfully");
        exit();
    } else {
        echo "Error updating student: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <?php include('sidebar_admission.php'); ?>
    <h4>Edit Student</h4>

    <form method="POST" action="">
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" placeholder="First Name" required>
        <input type="text" name="surname" value="<?php echo htmlspecialchars($student['surname']); ?>" placeholder="Surname" required>
        <select name="gender" required>
            <option value="Male" <?php if ($student['gender'] === 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($student['gender'] === 'Female') echo 'selected'; ?>>Female</option>
        </select>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob']); ?>" placeholder="Date of Birth" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" placeholder="Email" required>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" placeholder="Phone" required>
        <input type="text" name="program_registered" value="<?php echo htmlspecialchars($student['course']); ?>" placeholder="Program Registered" required>
        <input type="text" name="year_of_admission" value="<?php echo htmlspecialchars($student['year_of_admission']); ?>" placeholder="Year of Admission" required>

        <button type="submit">Update Student</button>
    </form>
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0px 0px 10px 0px #000;
        border-radius: 8px;
    }

    h4 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    input, select {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        padding: 10px;
        background-color: #28a745;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 100%;
    }

    button:hover {
        background-color: #218838;
    }
</style>

<?php include('footer.php'); ?>
