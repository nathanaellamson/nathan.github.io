
<?php
require 'connection1.php';
include 'header.php';

// Get the timetable entry ID
$id = $_GET['id'];
$timetable_entry = [];
$result = $conn->query("SELECT * FROM timetable WHERE id = $id");

if ($result) {
    $timetable_entry = $result->fetch_assoc();
}

// Check if course_level and year_of_study are set correctly
if (!isset($timetable_entry['course_level']) || !isset($timetable_entry['year_of_study'])) {
    echo "<script>alert('Error: Course level or year of study not found for this timetable entry.');</script>";
    exit();
}

// Fetch classroom name based on classroom_id
$classroom_id = $timetable_entry['id'];
$classroom_result = $conn->query("SELECT name FROM class_rooms WHERE id = $classroom_id");
$classroom_name = $classroom_result->fetch_assoc()['name'] ?? '';

// Fetch all classrooms for the dropdown
$classrooms = $conn->query("SELECT id, name FROM class_rooms");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $module_id = $_POST['module_id'];
    $course_level = $_POST['course_level'];
    $year = $_POST['year_of_study'];
    $day = $_POST['day'];
    $time = date('H:i:s', strtotime($_POST['time']));
    $classroom_id = $_POST['id'];

    // Proceed with checks and update (same as before)
    // ... your collision checks and update logic here ...
    // Check for overlapping sessions within the same classroom
    $checkQuery = "SELECT * FROM timetable 
                   WHERE classroom_id = ? 
                     AND day = ? 
                     AND id != ? 
                     AND (
                         (time <= ? AND ADDTIME(time, '03:00:00') > ?) OR
                         (time < ? AND ? < ADDTIME(time, '03:00:00'))
                     )";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('isiisss', $classroom_id, $day, $id, $time, $time, $time, $time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Classroom $classroom_id is occupied on $day at $time or overlapping with another session.');</script>";
    } else {
        // Check for overlapping sessions for the same course, course name, and year of study
        $checkClassQuery = "SELECT * FROM timetable 
                            WHERE course_name = ? 
                              AND course_level = ? 
                              AND year_of_study = ? 
                              AND day = ? 
                              AND id != ? 
                              AND (
                                  (time <= ? AND ADDTIME(time, '03:00:00') > ?) OR
                                  (time < ? AND ? < ADDTIME(time, '03:00:00'))
                              )";
        $stmt = $conn->prepare($checkClassQuery);
        $stmt->bind_param('siissssss', $course_name, $course_level, $year, $day, $id, $time, $time, $time, $time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Overlap detected with another session for course $course_name on $day at $time. Ensure a 3-hour gap.');</script>";
        } else {
            // Proceed with update if no collisions are found
            $updateQuery = "UPDATE timetable SET course_name = ?, module_id = ?, day = ?, time = ?, classroom_id = ?, course_level = ?, year_of_study = ? WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('sssiiiii', $course_name, $module_id, $day, $time, $classroom_id, $course_level, $year, $id);

            if ($stmt->execute()) {
                echo "<script>alert('Timetable updated successfully.');</script>";
                header('Location: view_timetable.php');
                exit();
            } else {
                echo "<script>alert('Error updating timetable: " . $conn->error . "');</script>";
            }
        }
    }
    $stmt->close();
}
?>

<!-- HTML form for editing the timetable -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Timetable</title>
</head>
<body>
<?php include 'sidebar_academic.php'; ?>
<div class="container">
    <h2>Edit Timetable Entry</h2>
    <form action="" method="POST">
        <label for="course_name">Course Name:</label>
        <input type="text" name="course_name" value="<?php echo $timetable_entry['course_name']; ?>" readonly>

        <label for="module_id">Module ID:</label>
        <input type="text" name="module_id" value="<?php echo $timetable_entry['module_id']; ?>" readonly>

        <label for="course_level">Course Level:</label>
        <input type="number" name="course_level" value="<?php echo $timetable_entry['course_level']; ?>" readonly>

        <label for="year_of_study">Year of Study:</label>
        <input type="number" name="year_of_study" value="<?php echo $timetable_entry['year_of_study']; ?>" readonly>

        <label for="day">Day:</label>
        <select name="day" required>
            <option value="Monday" <?php echo ($timetable_entry['day'] === 'Monday') ? 'selected' : ''; ?>>Monday</option>
            <option value="Tuesday" <?php echo ($timetable_entry['day'] === 'Tuesday') ? 'selected' : ''; ?>>Tuesday</option>
            <option value="Wednesday" <?php echo ($timetable_entry['day'] === 'Wednesday') ? 'selected' : ''; ?>>Wednesday</option>
            <option value="Thursday" <?php echo ($timetable_entry['day'] === 'Thursday') ? 'selected' : ''; ?>>Thursday</option>
            <option value="Friday" <?php echo ($timetable_entry['day'] === 'Friday') ? 'selected' : ''; ?>>Friday</option>
        </select>

        <label for="time">Time:</label>
        <input type="time" name="time" value="<?php echo $timetable_entry['time']; ?>" required>

        <label for="classroom_id">Classroom:</label>
        <select name="id" required>
            <?php while ($classroom = $classrooms->fetch_assoc()): ?>
                <option value="<?php echo $classroom['id']; ?>" <?php echo ($classroom['id'] == $timetable_entry['classroom_id']) ? 'selected' : ''; ?>>
                    <?php echo $classroom['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Update</button>
    </form>
</div>
<a href="view_timetable.php">Cancel</a>
<?php include 'footer.php'; ?>
</body>
</html>
