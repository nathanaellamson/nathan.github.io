<?php 
include('header.php');  
require 'connection1.php'; // Include your database connection

// Fetch all students from the database
$query = "SELECT * FROM students";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <!-- Include the Sidebar -->
    <?php include 'sidebar_admission.php'; ?>

    <h2>View Students</h2>

    <!-- Display success/error messages -->
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Check if there are any students to display -->
    <?php if ($result->num_rows > 0): ?>
        <table id="studentsTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Registration Number</th>
                    <th>Full Name</th>
                    <th>Program Registered</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['registration_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['surname']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td>
                            <button class="btn btn-info toggle-btn" onclick="toggleDetails('<?php echo htmlspecialchars($row['registration_number']); ?>')">More Info</button>
                        </td>
                    </tr>
                    <tr class="details-row" id="details-<?php echo htmlspecialchars($row['registration_number']); ?>" style="display: none;">
                        <td colspan="5">
                            <div>
                                <strong>Year of Admission:</strong> <?php echo htmlspecialchars($row['year_of_admission']); ?><br>
                                <strong>Campus:</strong> <?php echo htmlspecialchars($row['campus']); ?><br>
                                <strong>Department:</strong> <?php echo htmlspecialchars($row['department']); ?><br>
                                <strong>Stream:</strong> <?php echo htmlspecialchars($row['stream']); ?><br>
                                <strong>Level of Study:</strong> <?php echo htmlspecialchars($row['level']); ?><br>
                                <strong>Date of Birth:</strong> <?php echo htmlspecialchars($row['dob']); ?><br>
                                <strong>District of Birth:</strong> <?php echo htmlspecialchars($row['district_of_birth']); ?><br>
                                <strong>Region of Birth:</strong> <?php echo htmlspecialchars($row['region_of_birth']); ?><br>
                                <strong>National ID:</strong> <?php echo htmlspecialchars($row['national_id']); ?><br>
                                <strong>Permanent Address:</strong> <?php echo htmlspecialchars($row['permanent_address']); ?><br>
                                <strong>Current Address:</strong> <?php echo htmlspecialchars($row['current_address']); ?><br>
                                <strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?><br>
                                <strong>Next of Kin Name:</strong> <?php echo htmlspecialchars($row['next_of_kin_name']); ?><br>
                                <strong>Next of Kin Phone:</strong> <?php echo htmlspecialchars($row['next_of_kin_phone']); ?><br>
                                <strong>Next of Kin Occupation:</strong> <?php echo htmlspecialchars($row['next_of_kin_occupation']); ?><br>
                                <strong>Bank Name:</strong> <?php echo htmlspecialchars($row['bank_name']); ?><br>
                                <strong>Branch Name:</strong> <?php echo htmlspecialchars($row['branch_name']); ?><br>
                                <strong>Account Number:</strong> <?php echo htmlspecialchars($row['account_number']); ?><br>
                                <strong>NHIF Card Number:</strong> <?php echo htmlspecialchars($row['nhif_card_number']); ?><br>
                                <strong>Form IV School Name:</strong> <?php echo htmlspecialchars($row['form_iv_school_name']); ?><br>
                                <strong>Form IV NECTA Number:</strong> <?php echo htmlspecialchars($row['form_iv_necta_number']); ?><br>
                                <strong>Form VI School Name:</strong> <?php echo htmlspecialchars($row['form_vi_school_name']); ?><br>
                                <strong>Form VI NECTA Number:</strong> <?php echo htmlspecialchars($row['form_vi_necta_number']); ?><br>
                                <strong>Academic Year:</strong> <?php echo htmlspecialchars($row['academic_year']); ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No students found.</p>
    <?php endif; ?>
</div>

<!-- JavaScript to handle the toggle functionality -->
<script>
    function toggleDetails(registrationNumber) {
        var detailsRow = document.getElementById('details-' + registrationNumber);
        if (detailsRow.style.display === "none") {
            detailsRow.style.display = "table-row"; // Show the details row
        } else {
            detailsRow.style.display = "none"; // Hide the details row
        }
    }

    $(document).ready(function() {
        // Initialize DataTables
        $('#studentsTable').DataTable({
            paging: true, // Enable pagination
            searching: true, // Enable search
            ordering: true, // Enable sorting
            order: [[0, "asc"]], // Default sorting by Registration Number ascending
            pageLength: 10, // Number of entries to display per page
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]] // Length menu options
        });
    });
</script>

<?php 
$conn->close(); // Close the database connection
include('footer.php'); 
?>
