
<?php
include('header.php');
require 'connection1.php'; // Include your database connection

// Fetch students data from the database
$query = "SELECT * FROM students"; // Adjust as necessary for your database structure
$result = mysqli_query($conn, $query);

// Check if there are results
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>
<link rel="stylesheet" href="styles.css">
<div class="container">
    <?php include('sidebar_admission.php'); ?>
    <main>
        <h4>Student List</h4>

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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display the student records
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
                    
                    // Add Edit and Delete buttons
                    echo "<td>";
                    echo "<a href='edit_student.php?id=" . htmlspecialchars($row['id']) . "' class='btn edit-btn'>Edit</a> ";
                    echo "<a href='delete_student.php?id=" . htmlspecialchars($row['id']) . "' class='btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this student?\");'>Delete</a>";
                    echo "</td>";

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
        flex-direction: column;
        height: 100vh; /* Full height of the viewport */
    }

    .container {
        display: flex;
        flex-grow: 1; /* Allow the container to take the remaining space */
    }

    main {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        overflow-y: auto; /* Enable vertical scrolling if needed */
    }

    h4 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        color: white;
    }

    .edit-btn {
        background-color: #007bff; /* Blue for edit */
    }

    .delete-btn {
        background-color: #dc3545; /* Red for delete */
    }

    .edit-btn:hover {
        background-color: #0056b3; /* Darker blue on hover */
    }

    .delete-btn:hover {
        background-color: #c82333; /* Darker red on hover */
    }

</style>

<?php include('footer.php'); ?>
