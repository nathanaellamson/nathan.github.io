<?php
session_start(); // Start the session

include('header.php');
require 'connection1.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['registration_number'])) {
    // Redirect to index.php (login page) if not logged in
    header('Location: index.php');
    exit();
}

// Use the session variable to filter the logged-in user's information
$registration_number = $_SESSION['registration_number']; // Assuming the registration number is stored in the session

// Fetch only the logged-in student's data from the database
$query = "SELECT * FROM students WHERE registration_number = ?"; // Adjust the WHERE clause to match your database structure
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $registration_number); // 's' denotes a string type parameter
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if there are results
if (!$result || mysqli_num_rows($result) == 0) {
    die("No data found for this student: " . htmlspecialchars($registration_number));
}
?>
<link rel="stylesheet" href="styles.css">
<div class="container">
    <?php include('sidebar_student.php'); ?>
    <main>
        <h4>Student Information</h4>
        <table>
            <thead>
                <tr>
                    <th>Registration Number</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Program Registered</th>
                    <th>Year of Admission</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display the logged-in student's records
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['registration_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['surname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['course']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['year_of_admission']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</div>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        display: flex;
        height: 100vh;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        height: 100%;
        overflow-y: auto;
    }

    main {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        flex-grow: 1;
        overflow-y: auto;
    }

    h4 {
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ccc;
    }

    th {
        background-color: #1C4E80;
        color: white;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    /* Responsive design adjustments */
    @media (max-width: 768px) {
        main {
            padding: 15px;
        }

        th, td {
            font-size: 14px;
        }

        h4 {
            font-size: 1.5em;
        }
    }
</style>
<?php include('footer.php'); ?>
