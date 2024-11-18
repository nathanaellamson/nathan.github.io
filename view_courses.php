<?php 
include('header.php'); 
require 'connection1.php'; // Include the database connection
?>

<div class="container">
    <?php include('sidebar_academic.php'); ?>
    <main>
        <h2>Registered Courses</h2>
        
        <!-- Filtering Form -->
        <form action="view_courses.php" method="GET" class="form-inline mb-3">
            <div class="form-group mr-2">
                <label for="course_name" class="mr-2">Course Name:</label>
                <input type="text" name="course_name" id="course_name" class="form-control" value="<?php echo isset($_GET['course_name']) ? $_GET['course_name'] : ''; ?>" placeholder="Enter course name">
            </div>
            
            <div class="form-group mr-2">
                <label for="course_level" class="mr-2">Course Level:</label>
                <select name="course_level" id="course_level" class="form-control">
                    <option value="">Select Level</option>
                    <option value="certificate" <?php if (isset($_GET['course_level']) && $_GET['course_level'] == 'certificate') echo 'selected'; ?>>Certificate</option>
                    <option value="diploma" <?php if (isset($_GET['course_level']) && $_GET['course_level'] == 'diploma') echo 'selected'; ?>>Diploma</option>
                    <option value="bachelor" <?php if (isset($_GET['course_level']) && $_GET['course_level'] == 'bachelor') echo 'selected'; ?>>Bachelor</option>
                    <option value="masters" <?php if (isset($_GET['course_level']) && $_GET['course_level'] == 'masters') echo 'selected'; ?>>Masters</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="view_courses.php" class="btn btn-secondary ml-2">Reset</a>
        </form>

        <!-- Display Courses in a Table -->
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Course ID</th>
                    <th>Course Level</th>
                    <th>Course Name</th>
                    <th>Duration</th>
                    <th>Student Limit</th>
                    <th>Actions</th> <!-- New column for action buttons -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Build query based on filter criteria
                $conditions = [];
                $params = [];

                if (isset($_GET['course_name']) && !empty($_GET['course_name'])) {
                    $course_name = $_GET['course_name'];
                    $conditions[] = "course_name LIKE ?";
                    $params[] = "%$course_name%";
                }

                if (isset($_GET['course_level']) && !empty($_GET['course_level'])) {
                    $course_level = $_GET['course_level'];
                    $conditions[] = "course_level = ?";
                    $params[] = $course_level;
                }

                // Prepare query
                $sql = "SELECT * FROM courses";
                if (!empty($conditions)) {
                    $sql .= " WHERE " . implode(" AND ", $conditions);
                }

                // Execute query with prepared statement
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    if (!empty($params)) {
                        $types = str_repeat("s", count($params)); // string types
                        $stmt->bind_param($types, ...$params);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Check if there are any courses to display
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo ucfirst($row['course_level']); ?></td> <!-- ucfirst capitalizes the first letter -->
                                <td><?php echo $row['course_name']; ?></td>
                                <td><?php echo $row['duration']; ?> years</td>
                                <td><?php echo $row['student_limit']; ?></td>
                                <td>
                                    <a href="edit_course.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_course.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                                </td> <!-- Action buttons for editing and deleting -->
                            </tr>
                        <?php endwhile; 
                    else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No courses found.</td>
                        </tr>
                    <?php endif;
                } else {
                    echo "Error in query execution.";
                }
                ?>
            </tbody>
        </table>
    </main>
</div>

<?php include('footer.php'); ?>
