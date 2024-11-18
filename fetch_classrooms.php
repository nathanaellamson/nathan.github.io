
<?php
require 'connection1.php';

if (isset($_POST['classroom_id'], $_POST['day'], $_POST['time'])) {
    $classroom_id = $_POST['classroom_id'];
    $day = $_POST['day'];
    $time = $_POST['time'];

    // Calculate time range
    $time_start = date("H:i", strtotime($time) - 10800); // 3 hours before selected time
    $time_end = date("H:i", strtotime($time) + 10800); // 3 hours after selected time

    $query = "SELECT * FROM timetable WHERE classroom_id = ? AND day = ? AND (time BETWEEN ? AND ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $classroom_id, $day, $time_start, $time_end);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo 'Classroom is occupied during the selected time.';
    } else {
        echo 'Classroom is available.';
    }
}
?>
