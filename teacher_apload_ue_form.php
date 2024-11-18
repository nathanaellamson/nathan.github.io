<?php 
session_start(); // Start the session
include('header.php'); 
require 'connection1.php'; // Include the database connection

// Check for messages in the session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Clear the messages after displaying them
unset($_SESSION['message']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>H.O.D Coursework Results Entry</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Set up a flexbox container */
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Flex-grow the main container to take up remaining space */
        .container {
            flex: 1;
            overflow-x: auto;
            padding-bottom: 20px; /* Add some padding if needed */
        }

        /* Footer styles */
        footer {
           
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        /* Additional styles */
        h4 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        #responseMessage {
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
        }

        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-left: 250px;
        }

        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            margin-left: 250px;
        }

        #loadingSpinner {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #28a745;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
     
    <!-- Display success or error messages -->
    <?php if ($message): ?>
        <div class="success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
<div class="container">
    
<h4>Upload UE Results</h4>
    <!-- Include the Sidebar -->
    <?php include 'sidebar_teacher.php'; ?>

    <form id="filterForm" method="POST" action="teacher_apload_ue.php">
        <label for="course">Course:</label>
        <select id="course" name="course" required>
            <option value="">Select Course</option>
        </select>

        <label for="level">Level:</label>
        <select id="level" name="level" required>
            <option value="">Select Level</option>
        </select>

        <label for="academic_year">Academic Year:</label>
        <select id="academic_year" name="academic_year" required>
            <option value="">Select Academic Year</option>
        </select>

        <label for="year_of_study">Year of Study:</label>
        <select id="year_of_study" name="year_of_study" required>
            <option value="">Select Year of Study</option>
        </select>

        <label for="semester">Semester:</label>
        <select id="semester" name="semester" required>
            <option value="">Select Semester</option>
        </select>

        <label for="module">Module:</label>
        <select id="module" name="module" required>
            <option value="">Select Module</option>
        </select>

        <!-- Student list will be populated here -->
        <div id="studentsList"></div>

        <!-- Loading spinner -->
        <div id="loadingSpinner">
            <div class="spinner"></div>
            <p>Processing data, please wait...</p>
        </div>
        
        <!-- Submit button to process the form with standard form submission -->
        <button type="submit">Upload UE Results</button>
    </form>
</div>

<script>
    $(document).ready(function () {
        // Fetch courses on page load
        $.get("fetch_courses.php", function (data) {
            $('#course').html(data);
        });

        // Fetch levels based on selected course
        $('#course').change(function () {
            var course = $(this).val();
            $.get("fetch_levels.php", {course: course}, function (data) {
                $('#level').html(data);
            });
        });

        // Fetch academic years based on selected level
        $('#level').change(function () {
            var course = $('#course').val();
            var level = $(this).val();
            $.get("fetch_academic_years.php", {course: course, level: level}, function (data) {
                $('#academic_year').html(data);
            });
        });

        // Fetch years of study based on selected academic year
        $('#academic_year').change(function () {
            var course = $('#course').val();
            var level = $('#level').val();
            var academic_year = $(this).val();
            $.get("fetch_year_of_study.php", {course: course, level: level, academic_year: academic_year}, function (data) {
                $('#year_of_study').html(data);
            });
        });

        // Fetch semesters based on selected year of study
        $('#year_of_study').change(function () {
            var course = $('#course').val();
            var level = $('#level').val();
            var academic_year = $('#academic_year').val();
            var year_of_study = $(this).val();
            $.get("fetch_semesters.php", {course: course, level: level, year_of_study: year_of_study}, function (data) {
                $('#semester').html(data);
            });
        });

        // Fetch modules based on selected semester
        $('#semester').change(function () {
            var course = $('#course').val();
            var level = $('#level').val();
            var year_of_study = $('#year_of_study').val();
            var semester = $(this).val();
            $.get("fetch_modules.php", {course: course, level: level, year_of_study: year_of_study, semester: semester}, function (data) {
                $('#module').html(data);
            });
        });

        // Fetch student list based on selected course, level, academic year, year of study, and module
        $('#module').change(function () {
            var course = $('#course').val();
            var level = $('#level').val();
            var academic_year = $('#academic_year').val();
            var year_of_study = $('#year_of_study').val();
            var module = $(this).val();

            $.get("fetch_students.php", {
                course: course,
                level: level,
                academic_year: academic_year,
                year_of_study: year_of_study,
                module: module
            }, function (data) {
                $('#studentsList').html(data);
            });
        });

        // Show loading spinner on form submission
        $('#filterForm').on('submit', function () {
            $('#loadingSpinner').show();
        });
    });
</script>
<?php include 'footer.php'; ?>
</body>
</html>
