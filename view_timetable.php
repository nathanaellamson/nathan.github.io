<?php
// Include database connection
require 'connection1.php';

// Include header
include 'header.php';

// Initialize filter variables
$selected_level = isset($_POST['course_level']) ? $_POST['course_level'] : '';
$selected_course = isset($_POST['course_name']) ? $_POST['course_name'] : '';
$selected_year = isset($_POST['year_of_study']) ? $_POST['year_of_study'] : '';

// Fetch distinct course levels, course names, and years of study for the filters
$levels = $conn->query("SELECT DISTINCT course_level FROM courses");
$courses = $conn->query("SELECT DISTINCT course_name FROM courses");
$years = $conn->query("SELECT DISTINCT year_of_study FROM timetable");

// Fetch timetable entries based on filters
$timetable_groups = [];
$query = "SELECT tt.*, cr.name AS classroom_name, c.course_name, c.course_level, m.module_name 
          FROM timetable tt 
          JOIN class_rooms cr ON tt.classroom_id = cr.id 
          JOIN courses c ON tt.course_name = c.course_name 
          JOIN modules m ON tt.module_id = m.module_code 
          WHERE 1=1"; // Add a base condition

if ($selected_level) {
    $query .= " AND c.course_level = '" . $conn->real_escape_string($selected_level) . "'";
}

if ($selected_course) {
    $query .= " AND c.course_name = '" . $conn->real_escape_string($selected_course) . "'";
}

if ($selected_year) {
    $query .= " AND tt.year_of_study = '" . $conn->real_escape_string($selected_year) . "'";
}

$query .= " ORDER BY c.course_level, c.course_name, tt.year_of_study, tt.time";

$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Group by level, course name, and year of study
        $timetable_groups[$row['course_level']][$row['course_name']][$row['year_of_study']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Timetable</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form select {
            padding: 5px;
            margin-right: 10px;
        }

        .level-group, .course-group, .year-group {
            margin-top: 30px;
        }

        .level-group h3, .course-group h3, .year-group h3 {
            color: #333;
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #28a745;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            color: white;
            cursor: none;
            border-radius: 5px;
        }

        .btn-edit {
            background-color: orange;
        }

        .btn-delete {
            background-color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'sidebar_academic.php'; ?>
        <main>
            <h2>View Timetable</h2>

            <form method="POST" class="filter-form">
                <select name="course_level">
                    <option value="">Select Level</option>
                    <?php while ($level = $levels->fetch_assoc()): ?>
                        <option value="<?php echo $level['course_level']; ?>" <?php echo ($selected_level == $level['course_level']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level['course_level']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select name="course_name">
                    <option value="">Select Course</option>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <option value="<?php echo $course['course_name']; ?>" <?php echo ($selected_course == $course['course_name']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select name="year_of_study">
                    <option value="">Select Year of Study</option>
                    <?php while ($year = $years->fetch_assoc()): ?>
                        <option value="<?php echo $year['year_of_study']; ?>" <?php echo ($selected_year == $year['year_of_study']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($year['year_of_study']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Filter</button>
            </form>

            <?php if (empty($timetable_groups)): ?>
                <p>No timetable entries found.</p>
            <?php else: ?>
                <?php foreach ($timetable_groups as $level => $courses): ?>
                    <div class="level-group">
                        <h3>Level: <?php echo htmlspecialchars($level); ?></h3>
                        <?php foreach ($courses as $course_name => $years): ?>
                            <div class="course-group">
                                <h3>Course: <?php echo htmlspecialchars($course_name); ?></h3>
                                <?php foreach ($years as $year_of_study => $entries): ?>
                                    <div class="year-group">
                                        <h3>Year of Study: <?php echo htmlspecialchars($year_of_study); ?></h3>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Course Name</th>
                                                    <th>Module Name</th>
                                                    <th>Day</th>
                                                    <th>Time</th>
                                                    <th>Classroom</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($entries as $entry): ?>
                                                    <tr>
                                                        <td><?php echo $entry['id']; ?></td>
                                                        <td><?php echo $entry['course_name']; ?></td>
                                                        <td><?php echo $entry['module_name']; ?></td>
                                                        <td><?php echo $entry['day']; ?></td>
                                                        <td><?php echo $entry['time']; ?></td>
                                                        <td><?php echo $entry['classroom_name']; ?></td>
                                                        <td class="action-buttons">
                                                            <a href="edit_timetable.php?id=<?php echo $entry['id']; ?>" class="btn btn-edit">Edit</a>
                                                            <form action="process_delete_timetable.php" method="POST" style="display:inline;">
                                                                <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                                                <button type="submit" class="btn btn-delete">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
