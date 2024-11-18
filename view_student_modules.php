<?php
session_start();
require 'connection1.php'; // Include your database connection file

// Assuming you have stored the registration number in the session upon login
$registration_number = $_SESSION['registration_number'] ?? null;

if (!$registration_number) {
    die("You need to log in to view your registered modules.");
}

// Fetch the student's details
$query = $conn->prepare("
    SELECT course, level, year_of_study
    FROM students
    WHERE registration_number = ?
");
$query->bind_param("s", $registration_number);
$query->execute();
$student_result = $query->get_result();

if ($student_result->num_rows === 0) {
    die("Student not found.");
}

$student = $student_result->fetch_assoc();
$course = $student['course'];
$level = $student['level'];
$year_of_study = $student['year_of_study'];

// Fetch registered modules for the student
$modules_query = $conn->prepare("
    SELECT module_code, module_name, semester
    FROM modules
    WHERE course = ? AND level = ? AND year_of_study = ?
");
$modules_query->bind_param("ssi", $course, $level, $year_of_study);
$modules_query->execute();
$modules_result = $modules_query->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Modules</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar_student.php'; ?>
    <main>
        <h2>Registered Modules for <?php echo htmlspecialchars($registration_number); ?></h2>

        <table>
            <thead>
                <tr>
                    <th>Module Code</th>
                    <th>Module Name</th>
                    <th>Semester</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($modules_result->num_rows > 0): ?>
                    <?php while ($module = $modules_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($module['module_code']); ?></td>
                            <td><?php echo htmlspecialchars($module['module_name']); ?></td>
                            <td><?php echo htmlspecialchars($module['semester']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No registered modules found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
