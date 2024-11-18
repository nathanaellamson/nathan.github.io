<?php 
include('header.php'); 
require 'connection1.php'; // Include the database connection
?>

<div class="container">
    <?php include('sidebar_academic.php'); ?>
    <main>
        <h2>Registered Modules</h2>
        
        <!-- Filtering Form -->
        <form action="view_modules.php" method="GET" class="form-inline mb-3">
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

            <div class="form-group mr-2">
                <label for="year_of_study" class="mr-2">Year of Study:</label>
                <select name="year_of_study" id="year_of_study" class="form-control">
                    <option value="">Select Year</option>
                    <option value="1" <?php if (isset($_GET['year_of_study']) && $_GET['year_of_study'] == '1') echo 'selected'; ?>>1</option>
                    <option value="2" <?php if (isset($_GET['year_of_study']) && $_GET['year_of_study'] == '2') echo 'selected'; ?>>2</option>
                    <option value="3" <?php if (isset($_GET['year_of_study']) && $_GET['year_of_study'] == '3') echo 'selected'; ?>>3</option>
                    <option value="4" <?php if (isset($_GET['year_of_study']) && $_GET['year_of_study'] == '4') echo 'selected'; ?>>4</option>
                </select>
            </div>

            <div class="form-group mr-2">
                <label for="module_semester" class="mr-2">Semester:</label>
                <select name="module_semester" id="module_semester" class="form-control">
                    <option value="">Select Semester</option>
                    <option value="1" <?php if (isset($_GET['module_semester']) && $_GET['module_semester'] == '1') echo 'selected'; ?>>1</option>
                    <option value="2" <?php if (isset($_GET['module_semester']) && $_GET['module_semester'] == '2') echo 'selected'; ?>>2</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="view_modules.php" class="btn btn-secondary ml-2">Reset</a>
        </form>

        <!-- Display Modules in a Table -->
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Module Code</th>
                    <th>Module Name</th>
                    <th>Course Name</th>
                    <th>Course Level</th>
                    <th>Year of Study</th>
                    <th>Module Type</th>
                    <th>Semester</th>
                    <th>Actions</th> <!-- Edit/Delete actions column -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Build query based on filter criteria
                $conditions = [];
                $params = [];

                if (isset($_GET['course_name']) && !empty($_GET['course_name'])) {
                    $course_name = $_GET['course_name'];
                    $conditions[] = "course LIKE ?";
                    $params[] = "%$course_name%";
                }

                if (isset($_GET['course_level']) && !empty($_GET['course_level'])) {
                    $course_level = $_GET['course_level'];
                    $conditions[] = "level = ?";
                    $params[] = $course_level;
                }

                if (isset($_GET['year_of_study']) && !empty($_GET['year_of_study'])) {
                    $year_of_study = $_GET['year_of_study'];
                    $conditions[] = "year_of_study = ?";
                    $params[] = $year_of_study;
                }

                if (isset($_GET['module_semester']) && !empty($_GET['module_semester'])) {
                    $module_semester = $_GET['module_semester'];
                    $conditions[] = "semester = ?";
                    $params[] = $module_semester;
                }

                // Prepare query
                $sql = "SELECT * FROM modules";
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

                    // Check if there are any modules to display
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['module_code']; ?></td>
                                <td><?php echo $row['module_name']; ?></td>
                                <td><?php echo $row['course']; ?></td>
                                <td><?php echo ucfirst($row['level']); ?></td> <!-- ucfirst capitalizes the first letter -->
                                <td><?php echo $row['year_of_study']; ?></td>
                                <td><?php echo ucfirst($row['module_type']); ?></td> <!-- Capitalize module type (Core/Elective) -->
                                <td><?php echo $row['semester']; ?></td>
                                <td>
                                    <a href="edit_module.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_module.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this module?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; 
                    else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No modules found.</td>
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
