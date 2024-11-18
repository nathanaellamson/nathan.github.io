<?php
session_start();
require 'connection1.php';

$reg_number = $_SESSION['registration_number'] ?? ''; 
$current_year = date('Y');
$current_academic_year = "$current_year/" . ($current_year + 1);

// Fetch academic years
$years_query = $conn->prepare("
    SELECT DISTINCT academic_year 
    FROM ue_results 
    WHERE registration_number = ? 
    ORDER BY academic_year DESC
");
$years_query->bind_param("s", $reg_number);
$years_query->execute();
$years_result = $years_query->get_result();
$academic_years = $years_result->fetch_all(MYSQLI_ASSOC);

$selected_year = $_GET['year'] ?? $current_academic_year;

// Fetch CW, UE results, and SUP scores for the selected academic year
$query = $conn->prepare("
    SELECT cr.module_code, cr.module_name, m.module_type, m.credit, cr.score AS coursework_score, 
           COALESCE(uer.score, 0) AS ue_score, COALESCE(sr.score, NULL) AS sup, uer.remarks, uer.semester, m.level
    FROM coursework_results cr
    JOIN ue_results uer ON cr.module_code = uer.module_code AND cr.registration_number = uer.registration_number
    JOIN modules m ON cr.module_code = m.module_code
    LEFT JOIN sup_results sr ON cr.module_code = sr.module_code AND cr.registration_number = sr.registration_number
    WHERE cr.registration_number = ? AND cr.academic_year = ?
    ORDER BY cr.semester, cr.module_code
");
$query->bind_param("ss", $reg_number, $selected_year);
$query->execute();
$results = $query->get_result();

// Grade calculation function
function calculateGrade($total_score, $course_level) {
    if ($course_level === 'bachelor') {
        if ($total_score >= 70) return ['A', 5];
        if ($total_score >= 60) return ['B+', 4];
        if ($total_score >= 50) return ['B', 3];
        if ($total_score >= 40) return ['C', 2];
        return ['F', 0];
    } elseif (in_array($course_level, ['diploma', 'certificate'])) {
        if ($total_score >= 80) return ['A', 5];
        if ($total_score >= 70) return ['B+', 4];
        if ($total_score >= 60) return ['B', 3];
        if ($total_score >= 50) return ['C', 2];
        if ($total_score >= 40) return ['D', 1];
        return ['F', 0];
    }
    return ['F', 0];
}

$total_credits = 0;
$total_points = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UE Results</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            margin: 0; /* Remove default margin */
        }
        .container {
            display: flex;
            flex: 1; /* Allow the container to grow and fill available space */
        }
        main {
            flex: 1;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        footer {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
            border-top: 1px solid #ddd;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            margin: 8px 0;
        }
        ul li a {
            color: #007bff;
            text-decoration: none;
        }
        ul li a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <?php include 'sidebar_student.php'; ?>
        <main>
            <h2>Examination Results</h2>
            <p style="color: orange">Current Year: <?php echo htmlspecialchars($selected_year); ?></p>
            <div class="year-links">
                <h3>Year of Study</h3>
                <ul>
                    <?php foreach ($academic_years as $year): ?>
                        <li><a href="?year=<?php echo urlencode($year['academic_year']); ?>"><?php echo htmlspecialchars($year['academic_year']); ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="#">View All</a></li>
                </ul>
            </div>
            <?php
            $current_semester = '';
            while ($row = $results->fetch_assoc()):
                $ue_score_60 = $row['ue_score'] * 0.6; // 60% of UE score
                $sup_score = $row['sup'];
                $course_level = $row['level'];
                $remarks = '';
                $grade = '';
                $points = 0;

                if ($sup_score !== null) {
                    // If SUP score is present
                    if ($sup_score < 40) {
                        $grade = 'F';
                        $remarks = 'carry';
                        $points = 0; // No points for failing grade
                    } else {
                        $grade = 'C*'; // Special grade for SUP >= 40
                        $remarks = 'pass';
                        $points = 2; // Assuming C* corresponds to 2 points for GPA calculation
                    }
                } else {
                    // Calculate total score and grade if no SUP score
                    $total_score = $row['coursework_score'] + $ue_score_60;

                    if (($course_level === 'bachelor' && $row['coursework_score'] < 16) || 
                        (in_array($course_level, ['diploma', 'certificate']) && $row['coursework_score'] < 20)) {
                        $remarks = 'technical sup';
                    } elseif ($row['ue_score'] < 40) {
                        $grade = 'F';
                        $points = 0; // Assuming C* corresponds to 2 points for GPA calculation
                        $remarks = 'fail';
                    } else {
                        $remarks = 'pass';
                    }

                    // Calculate grade based on total score
                    list($grade, $points) = calculateGrade($total_score, $course_level);
                }

                // Add credits and points to totals for GPA calculation
                $total_credits += $row['credit'];
                $total_points += $row['credit'] * $points;

                if ($current_semester != $row['semester']):
                    if ($current_semester): ?>
                        </tbody></table>
                    <?php endif;
                    $current_semester = $row['semester'];
            ?>
                    <h4 style="color: blue">Semester <?php echo htmlspecialchars($current_semester); ?></h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Module Name</th>
                                <th>Type</th>
                                <th>CW</th>
                                <th>UE (60%)</th>
                                <th>SUP</th>
                                <th>Total Score</th>
                                <th>Credits</th>
                                <th>Grade</th>
                                <th>Points</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php endif; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['module_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['module_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['module_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['coursework_score']); ?></td>
                                <td><?php echo htmlspecialchars($ue_score_60); ?></td>
                                <td><?php echo htmlspecialchars($sup_score); ?></td>
                                <td><?php echo htmlspecialchars($total_score); ?></td>
                                <td><?php echo htmlspecialchars($row['credit']); ?></td>
                                <td><?php echo htmlspecialchars($grade); ?></td>
                                <td><?php echo htmlspecialchars($points); ?></td>
                                <td><?php echo htmlspecialchars($remarks); ?></td>
                            </tr>
            <?php endwhile; ?>
                        </tbody>
                    </table>
            <h5 style="color: darkblue; font-weight: 900;">GPA: <?php echo $total_credits ? number_format($total_points / $total_credits, 1) : '<p style="color:red"> [No results found for selected academic year]</p>'; ?></h5>
        </main>
    </div>
    <footer>
        <p>&copy; 2024 Cardinary Rugambwa Memorial College. All Rights Reserved.</p>
    </footer>
</body>
</html>
