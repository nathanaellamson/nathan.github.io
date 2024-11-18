
<?php
// process_timetable.php
// Include database connection
require 'connection1.php';

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch form data
    $course_level = $_POST['course_level'];
    $course_name = $_POST['course_name'];
    $year_of_study = $_POST['year_of_study'];

    // Ensure module details are set
    if (!isset($_POST['day']) || !isset($_POST['time']) || !isset($_POST['classroom']) || !isset($_POST['modules_code'])) {
        echo "Error: Module details not set.";
        exit;
    }

    // Fetch module selections
    $modules = $_POST['modules_code'];
    $days = $_POST['day'];
    $times = $_POST['time'];
    $classrooms = $_POST['classroom'];

    foreach ($modules as $module_code) {
        $day = $days[$module_code];
        $time = date('H:i:s', strtotime($times[$module_code]));
        $classroom_id = $classrooms[$module_code];

        // Verify that the classroom ID exists in the class_rooms table
        $verifyClassroomQuery = "SELECT id FROM class_rooms WHERE id = ?";
        $stmt = $conn->prepare($verifyClassroomQuery);
        $stmt->bind_param('i', $classroom_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo "Error: Classroom ID $classroom_id does not exist in the class_rooms table.<br>";
            continue; // Skip to the next module if classroom ID does not exist
        }

        // Check for overlapping sessions in the same classroom
        $checkClassroomQuery = "SELECT * FROM timetable 
                                WHERE classroom_id = ? 
                                  AND day = ? 
                                  AND (
                                      (time <= ? AND ADDTIME(time, '03:00:00') > ?) OR
                                      (time < ? AND ? < ADDTIME(time, '03:00:00'))
                                  )";
        $stmt = $conn->prepare($checkClassroomQuery);
        $stmt->bind_param('isssss', $classroom_id, $day, $time, $time, $time, $time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Classroom ID $classroom_id is occupied on $day at $time or overlapping with another session.<br>";
            continue;
        }

        // Check for collisions for the same course level, course name, and year of study across all classrooms
        $checkClassQuery = "SELECT * FROM timetable 
                            WHERE course_level = ? 
                              AND course_name = ? 
                              AND year_of_study = ? 
                              AND day = ? 
                              AND (
                                  (time <= ? AND ADDTIME(time, '03:00:00') > ?) OR
                                  (time < ? AND ? < ADDTIME(time, '03:00:00'))
                              )";
        $stmt = $conn->prepare($checkClassQuery);
        $stmt->bind_param('ssisssss', $course_level, $course_name, $year_of_study, $day, $time, $time, $time, $time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Session for course level $course_level, course $course_name, year $year_of_study is overlapping with another session on $day at $time. Please ensure there is a 3-hour gap.<br>";
            continue;
        }

        // Insert timetable information if all checks pass
        $insertQuery = "INSERT INTO timetable (course_level, course_name, year_of_study, module_id, day, time, classroom_id)
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param('ssisssi', $course_level, $course_name, $year_of_study, $module_code, $day, $time, $classroom_id);

        if ($stmt->execute()) {
            echo "Timetable information for module $module_code on $day at $time has been added successfully.<br>";
        } else {
            echo "Error adding timetable information: " . $conn->error . "<br>";
        }
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo "Error: No form data submitted.";
}

// Close the database connection
$conn->close();
?>
