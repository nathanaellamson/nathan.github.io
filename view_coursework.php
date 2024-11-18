<?php
session_start();
require 'connection1.php';

$reg_number = $_SESSION['registration_number'] ?? ''; 
$current_year = date('Y');
$current_academic_year = "$current_year/" . ($current_year + 1);

$years_query = $conn->prepare("
    SELECT DISTINCT academic_year 
    FROM coursework_results 
    WHERE registration_number = ?
    ORDER BY academic_year DESC
");
$years_query->bind_param("s", $reg_number);
$years_query->execute();
$years_result = $years_query->get_result();
$academic_years = $years_result->fetch_all(MYSQLI_ASSOC);

$selected_year = $_GET['year'] ?? $current_academic_year;

$query = $conn->prepare("
    SELECT cwr.module_code, cwr.module_name, m.credit, cwr.score, cwr.remarks, cwr.semester 
    FROM coursework_results cwr
    JOIN modules m ON cwr.module_code = m.module_code
    WHERE cwr.registration_number = ? AND cwr.academic_year = ?
    ORDER BY cwr.semester, cwr.module_code
");
$query->bind_param("ss", $reg_number, $selected_year);
$query->execute();
$results = $query->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coursework Scores</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
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
        <?php include 'sidebar_student.php'; ?> <!-- Including the sidebar -->

        <main>
            <h2>Coursework Scores</h2>
            <p style="color: blue">Current Academic Year: <?php echo htmlspecialchars($selected_year); ?></p>
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
            // Display results
            if ($results->num_rows > 0) {
                $current_semester = '';
                while ($row = $results->fetch_assoc()):
                    if ($current_semester != $row['semester']):
                        if ($current_semester): ?>
                            </table>
                        <?php endif;
                        $current_semester = $row['semester'];
            ?>
                    <h4 style="color: blue">Semester <?php echo htmlspecialchars($current_semester); ?></h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Module Code</th>
                                <th>Module Name</th>
                                <th>Credits</th>
                                <th>Score</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
            <?php endif; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['module_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['module_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['credit']); ?></td>
                            <td><?php echo htmlspecialchars($row['score']); ?></td>
                            <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                        </tr>
            <?php endwhile; ?>
                        </tbody>
                    </table>
            <?php } else { ?>
                <p>No coursework results found for the selected academic year.</p>
            <?php } ?>
        </main>
    </div>
    
    <?php include 'footer.php'; ?>

</body>
</html>

<?php
$years_query->close();
$conn->close();
?>
