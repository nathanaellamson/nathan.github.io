<?php
// Include database connection
require 'connection1.php';

// Include header
include 'header.php';

// Fetch all classrooms
$classrooms = [];
$result = $conn->query("SELECT * FROM class_rooms");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classrooms[] = $row;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Classrooms</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin: 20px;
        }


        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #28a745;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
   <?php include 'sidebar_academic.php'; ?>
        <main>
            <h2>View Classrooms</h2>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Capacity</th>
                        <th>Building</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classrooms)): ?>
                        <tr>
                            <td colspan="4">No classrooms found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classrooms as $classroom): ?>
                            <tr>
                                <td><?php echo $classroom['id']; ?></td>
                                <td><?php echo $classroom['name']; ?></td>
                                <td><?php echo $classroom['capacity']; ?></td>
                                <td><?php echo $classroom['building']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
