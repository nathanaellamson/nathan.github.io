
<?php
// Include database connection
// add_timetable.php
session_start();
require 'connection1.php';
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Timetable Info</title>
     <link rel="stylesheet" href="styles.css">
    <style>
        /* Styling similar to original */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 50px auto; background-color: #fff; padding: 20px; border-radius: 8px;  }
        h2 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; gap: 15px; }
        select, input { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 1em; }
        button { padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .module-details { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
</head>
<body>

<div class="container">
<?php
    // Check if there are any messages and display them
    if (isset($_SESSION['timetable_messages'])) {
        foreach ($_SESSION['timetable_messages'] as $message) {
          
        }
        // Clear messages after displaying
        unset($_SESSION['timetable_messages']);
    }
    ?>
<?php include 'sidebar_academic.php'; ?>
    <h2>Add Timetable Information</h2>
    <form action="process_timetable.php" method="POST">

        <!-- Course Level Dropdown -->
        <label for="course_level">Course Level:</label>
        <select name="course_level" id="course_level" required>
            <option value="">Select Course Level</option>
            <?php
            $query = "SELECT DISTINCT course_level FROM courses";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='".$row['course_level']."'>".$row['course_level']."</option>";
            }
            ?>
        </select>

        <!-- Course Name Dropdown -->
        <label for="course_name">Course Name:</label>
        <select name="course_name" id="course_name" required>
            <option value="">Select Course Name</option>
        </select>

        <!-- Year of Study Dropdown -->
        <label for="year_of_study">Year of Study:</label>
        <select name="year_of_study" id="year_of_study" required>
            <option value="">Select Year of Study</option>
            <option value="1">1 Year </option>
            <option value="2">2 Year </option>
            <option value="3">3 Year </option>
            <option value="4">4 Year </option>
        </select>

        <!-- Modules with day, time, and classroom selection -->
        <div id="module_details"></div>

        <button type="submit">Add Timetable Info</button>
    </form>


<script>
$(document).ready(function() {
    $('#course_level').change(function() {
        var courseLevel = $(this).val();
        if (courseLevel) {
            $.ajax({
                type: 'POST',
                url: 'fetch_course.php',
                data: { course_level: courseLevel },
                success: function(html) {
                    $('#course_name').html(html);
                    $('#module_details').html('');
                }
            });
        } else {
            $('#course_name').html('<option value="">Select Course Name</option>');
            $('#module_details').html('');
        }
    });

    $('#course_name').change(function() {
        var courseName = $(this).val();
        var level = $('#course_level').val();
        var yearOfStudy = $('#year_of_study').val();
        
        if (courseName && level && yearOfStudy) {
            $.ajax({
                type: 'POST',
                url: 'fetch_module.php',
                data: { course_name: courseName, year_of_study: yearOfStudy, course_level: level },
                success: function(html) {
                    $('#module_details').html(html);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", error);
                }
            });
        } else {
            $('#module_details').html('');
        }
    });

    $('#year_of_study').change(function() {
        var courseName = $('#course_name').val();
        var level = $('#course_level').val();
        var yearOfStudy = $(this).val();

        if (courseName && level && yearOfStudy) {
            $.ajax({
                type: 'POST',
                url: 'fetch_module.php',
                data: { course_name: courseName, year_of_study: yearOfStudy, course_level: level },
                success: function(html) {
                    $('#module_details').html(html);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", error);
                }
            });
        } else {
            $('#module_details').html('');
        }
    });

    $(document).on('change', '.classroom-select', function() {
        var moduleElement = $(this).closest('.module-details');
        var classroomId = $(this).val();
        var day = moduleElement.find('select[name^="day_"]').val();
        var time = moduleElement.find('input[name^="time_"]').val();

        if (classroomId && day && time) {
            $.ajax({
                type: 'POST',
                url: 'fetch_classrooms.php',
                data: { classroom_id: classroomId, day: day, time: time },
                success: function(response) {
                    if (response.trim() === 'Classroom is occupied during the selected time.') {
                        alert('Classroom is occupied during the selected time. Please choose another time or classroom.');
                        moduleElement.find('.classroom-select').val('');
                    }
                }
            });
        }
    });
});

</script>
</div>
<!-- Include the Footer -->
<?php //include 'footer.php'; ?>
</body>
</html>
