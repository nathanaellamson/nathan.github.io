
<?php
// fetch_modules.php
require 'connection1.php';

if (isset($_POST['course_name'], $_POST['year_of_study'], $_POST['course_level'])) {
    $course = $_POST['course_name'];
    $year = $_POST['year_of_study'];
    $level = $_POST['course_level'];

    // Prepare the query
    $query = "SELECT module_code, module_name FROM modules WHERE course = ? AND level = ? AND year_of_study = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sss", $course, $level, $year);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<h3>Select Details for Each Module</h3>';
            while ($row = $result->fetch_assoc()) {
                $module_code = htmlspecialchars($row['module_code']);
                $module_name = htmlspecialchars($row['module_name']);
                ?>
                <div class="module-details">
                      <h4><?php echo $module_name; ?> (<?php echo $module_code; ?>)</h4>
    <input type="hidden" name="modules_code[]" value="<?php echo $module_code; ?>">

    <!-- Select Day -->
    <label for="day_<?php echo $module_code; ?>">Day:</label>
    <select name="day[<?php echo $module_code; ?>]" id="day_<?php echo $module_code; ?>" >
        <option value="">Select Day</option>
        <option value="Monday">Monday</option>
        <option value="Tuesday">Tuesday</option>
        <option value="Wednesday">Wednesday</option>
        <option value="Thursday">Thursday</option>
        <option value="Friday">Friday</option>
    </select>

    <!-- Select Time -->
    <label for="time_<?php echo $module_code; ?>">Time:</label>
    <input type="time" name="time[<?php echo $module_code; ?>]" id="time_<?php echo $module_code; ?>" >

    <!-- Select Classroom -->
    <label for="classroom_<?php echo $module_code; ?>">Classroom:</label>
    <select name="classroom[<?php echo $module_code; ?>]" id="classroom_<?php echo $module_code; ?>" class="classroom-select" >
        <option value="">Select Classroom</option>
        <?php
        // Fetch classrooms from database
        $classroom_query = "SELECT id, name FROM class_rooms";
        $classroom_result = $conn->query($classroom_query);
        if ($classroom_result && $classroom_result->num_rows > 0) {
            while ($classroom = $classroom_result->fetch_assoc()) {
                echo "<option value='" . $classroom['id'] . "'>" . htmlspecialchars($classroom['name']) . "</option>";
            }
        }
        ?>
    </select>
</div>

                <?php
            }
        } else {
            echo "No modules found for the selected course, year of study, and level.";
        }
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
    }
}

$conn->close();
?>
