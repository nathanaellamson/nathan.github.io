<?php
session_start();
require 'connection1.php';

$registration_number = $_POST['registration_number'] ?? '';
$grouped_results = [];

if (!empty($registration_number)) {
    $query = $conn->prepare("
        SELECT academic_year, semester, id, registration_number, module_code, module_name, score AS score, remarks
        FROM sup_results
        WHERE registration_number = ?
        ORDER BY academic_year, semester
    ");
    $query->bind_param("s", $registration_number);
    $query->execute();
    $results = $query->get_result();

    // Group the results by academic_year and semester
    while ($row = $results->fetch_assoc()) {
        $grouped_results[$row['academic_year']][$row['semester']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SUP Results</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
    <?php include 'sidebar_hod.php'; ?>
    <main>
        <h2>SUP Results</h2>
        <form method="POST">
            <label for="registration_number">Registration Number:</label>
            <input type="text" name="registration_number" id="registration_number" required>
            <button type="submit">Filter</button>
        </form>

        <?php if (!empty($grouped_results)): ?>
            <p>Registration Number: <strong><?php echo htmlspecialchars($registration_number); ?></strong></p>
            <?php foreach ($grouped_results as $academic_year => $semesters): ?>
                <h3>Academic Year: <?php echo htmlspecialchars($academic_year); ?></h3>
                <?php foreach ($semesters as $semester => $result_rows): ?>
                    <h4>Semester: <?php echo htmlspecialchars($semester); ?></h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Module Code</th>
                                <th>Module Name</th>
                                <th>SUP Score</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result_rows as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['module_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['module_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                    <td>
                                        <a href="edit_result.php?id=<?php echo $row['id']; ?>&type=sup">Edit</a> |
                                        <a href="delete_result.php?id=<?php echo $row['id']; ?>&type=sup" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No results found for the provided registration number.</p>
        <?php endif; ?>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
