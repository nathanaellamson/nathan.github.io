<?php
// Include your database connection file
require 'connection1.php'; 
include('header.php');  
?>
 <link rel="stylesheet" href="styles.css"> <!-- Optional CSS for styling -->
<div class="container">
    <?php include('sidebar_academic.php'); ?>

    <h1>View Departments</h1>

    <!-- Display success/error message -->
    <?php if (isset($_GET['message'])): ?>
        <p><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Department Name</th>
                <th>Head of Department</th>
                <th>Description</th>
                <th>Established Year</th>
                <th>Contact Number</th>
                <th>Email Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch departments with their heads from the staff table
            $sql = "SELECT d.*, s.full_name, s.email 
                    FROM departments d 
                    LEFT JOIN staff s ON d.email_address = s.email";

            $result = $conn->query($sql); // Corrected variable name from $query to $sql

            // Check for results
            if ($result->num_rows > 0) {
                // Output data for each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['department_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['established_year']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email_address']) . "</td>";
                    echo '<td>
                              <form action="edit_department.php" method="get" style="display:inline;">
                                  <input type="hidden" name="id" value="' . $row['id'] . '">
                                  <button type="submit">Edit</button>
                              </form>
                              <form action="delete_department.php" method="post" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this department?\');">
                                  <input type="hidden" name="id" value="' . $row['id'] . '">
                                  <button type="submit" class="delete-button">Delete</button>
                              </form>
                          </td>';
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No departments found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>
<style>
 /* Styles for table and responsiveness */
.container {
    overflow-x: auto;
    padding-bottom: 120px;
}

form {
    display: inline-block;
}

button {
    padding: 8px 5px;
    font-size: 14px;
}

/* Red delete button */
.delete-button {
    background-color: red;
    color: white;
    border: none;
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 4px;
}

.delete-button:hover {
    background-color: #FF1F1F;
}

/* Responsive table adjustments */
@media (max-width: 768px) {
    th, td {
        font-size: 12px;
        padding: 8px;
    }
}

@media (max-width: 600px) {
    th:nth-child(3), td:nth-child(3),
    th:nth-child(4), td:nth-child(4),
    th:nth-child(5), td:nth-child(5) {
        display: none;
    }
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    align-items: center;
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
</style>
