<?php
// Include database connection
require 'connection1.php';
session_start();

// Check if user is logged in and registration number is set
if (!isset($_SESSION['registration_number'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch student's registration number from session
$registration_number = $_SESSION['registration_number'];

// Fetch student's course, level, and year of study based on registration number
$query = "SELECT course, level, year_of_study FROM students WHERE registration_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $registration_number);
$stmt->execute();
$student_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

$course = $student_info['course'];
$level = $student_info['level'];
$year_of_study = $student_info['year_of_study'];

// Include header
include 'header.php';

// Fetch timetable entries for the logged-in student, ordered by day and time
$timetable_entries = [];
$query = "SELECT tt.*, cr.name AS classroom_name, c.course_name AS course_name, m.module_name
FROM timetable tt
JOIN class_rooms cr ON tt.classroom_id = cr.id
JOIN courses c ON tt.course_name = c.course_name
JOIN modules m ON tt.module_id = m.module_code
WHERE tt.course_name = (SELECT course_name FROM courses WHERE course_name = ?)
AND tt.course_level = ?
AND tt.year_of_study = ?
ORDER BY tt.day, tt.time ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param('sis', $course, $level, $year_of_study);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $timetable_entries[$row['day']][] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Timetable</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        display: flex;
        height: 100vh; /* Full height of the viewport */
    }

    .container {
        display: flex;
        flex-direction: column;
        justify-content: flex-start; /* Align items at the start */
        align-items: stretch; /* Allow items to stretch to full width */
        width: 100%;
        height: 100%; /* Full height */
    }

    main {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 1200px; /* Set a maximum width for larger devices */
        margin: 0 auto; /* Center the main content */
        flex-grow: 1; /* Allow main to grow and fill available space */
        overflow-y: auto; /* Enable vertical scrolling if needed */
    }

    h2, h3 {
        color: #333;
        margin-bottom: 20px;
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

    tr:hover {
        background-color: #f1f1f1;
    }

    /* Responsive design adjustments */
    @media (max-width: 768px) {
        main {
            padding: 15px; /* Less padding on smaller screens */
        }

        th, td {
            font-size: 14px; /* Smaller font size */
        }

        h2, h3 {
            font-size: 1.5em; /* Responsive heading sizes */
        }
    }
</style>

</head>
<body>
    <div class="container">
       <?php include 'sidebar_student.php'; ?>
        <main>
            <h2>Your Timetable</h2>

            <?php
            $days_of_week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            foreach ($days_of_week as $day):
            ?>
                <h3 style="color: green; font-weight: 900;"><?php echo $day; ?></h3>
                <?php if (isset($timetable_entries[$day])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Module Name</th>
                                <th>Time</th>
                                <th>Classroom</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timetable_entries[$day] as $entry): ?>
                                <tr>
                                    <td style="color: blue; font-weight: bold;"><?php echo $entry['module_name']; ?></td>
                                    <td style="color: blue; font-weight: bold;"><?php echo date('H:i', strtotime($entry['time'])); ?></td>
                                    <td style="color: blue; font-weight: bold;"><?php echo $entry['classroom_name']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: red; font-weight: 900;">No sessions for this day</p>
                <?php endif; ?>
            <?php endforeach; ?>
        </main>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
