<?php
//student_dashboard.php
session_start();
require 'connection1.php'; // Include your database connection

// Check if the student is logged in
if (!isset($_SESSION['registration_number'])) {
    header("Location: index.php");
    exit();
}

// Fetch student details from the database using the session username/registration number
$student_username = $_SESSION['registration_number'];
$query = "SELECT * FROM students WHERE registration_number = '$student_username'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $student = mysqli_fetch_assoc($result);
} else {
    echo "No student data found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f9f9f9;
        }

        .container {
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        main {
            flex: 1;
            margin-left: 160px; /* Sidebar width */
            padding: 20px;
        }

        .dashboard-content {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1, h3 {
            color: blue;
        }

        .student-info-box {
            display: flex;
            justify-content: space-between;
            background-color: #e9f5ff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .student-info-box div {
            width: 48%;
        }

        .student-info-box p {
            font-size: 16px;
            margin: 8px 0;
        }

        .student-timetable {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .student-timetable th, .student-timetable td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .student-timetable th {
            background-color: #0073e6;
            color: #ffffff;
        }

        .student-timetable td {
            color: #333;
        }

        @media (max-width: 768px) {
            .student-info-box {
                flex-direction: column;
                padding: 10px;
            }

            .student-info-box div {
                width: 100%;
                margin-bottom: 10px;
            }

            main {
                margin-left: 0;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Include the Header -->
    <?php include 'header.php'; ?>

    <div class="container">
        <!-- Include the Student Sidebar -->
        <?php include 'sidebar_student.php'; ?>

        <main>
            <section class="dashboard-content">
                <h1>Student Dashboard</h1>
                <p >Welcome, <p style="color: purple; font-weight: 900;"> <?php echo htmlspecialchars($student['first_name']) . " " . htmlspecialchars($student['surname']); ?>!</p>

                <div class="student-info-box">
                    <div>
                        <h3 style="color: orange; font-weight: 900;">Student Information</h3>
                        <p><strong>Registration Number:</strong><p style="color: blue; font-weight: 900;"> <?php echo htmlspecialchars($student['registration_number']); ?></p>
                        <p><strong>Year of Study:</strong><p style="color: blue; font-weight: 900;"> <?php echo htmlspecialchars($student['year_of_study']); ?> Year</p>
                        <p><strong>Stream:</strong><p style="color: blue; font-weight: 900;"> <?php echo htmlspecialchars($student['stream']); ?></p>
                        <p><strong>Status:</strong><p style="color: blue; font-weight: 900;"> <?php echo htmlspecialchars($student['student_status']); ?></p>
                    </div>
                    <div>
                        
                        <p><strong>Entry Year:</strong><p style="color: blue; font-weight: 900;"> <?php echo htmlspecialchars($student['year_of_admission']); ?></p>
                        <p><strong>Level:</strong><p style="color: blue; font-weight: 900;"> <?php echo htmlspecialchars($student['level']); ?></p>
                        <p><strong>Current Academic Year:</strong><p style="color: blue; font-weight: 900;"> <?php echo htmlspecialchars($student['academic_year']); ?></p>
                        <p><strong>Program:</strong><p style="color: blue; font-weight: 900;"> <?php echo htmlspecialchars($student['course']); ?></p>
                        <p><strong>Department:</strong><p style="color: blue; font-weight: 900;"> <?php echo htmlspecialchars($student['department']); ?></p>
                    </div>
                </div>

               
            </section>
        </main>
    </div>

    <!-- Include the Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
