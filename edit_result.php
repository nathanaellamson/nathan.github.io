
<?php
session_start();
require 'connection1.php';

if (isset($_GET['id']) && isset($_GET['type'])) {
    $result_id = $_GET['id'];
    $type = $_GET['type'];

    // Determine the correct table based on type
    $table = '';
    switch ($type) {
        case 'coursework':
            $table = 'coursework_results';
            break;
        case 'sup':
            $table = 'sup_results';
            break;
        case 'ue':
            $table = 'ue_results';
            break;
        default:
            die('Invalid type.');
    }

    // Fetch existing data
    $query = $conn->prepare("SELECT score, remarks FROM $table WHERE id = ?");
    $query->bind_param("i", $result_id);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $score = $_POST['score'];
        $remarks = $_POST['remarks'];

        // Update the score and remarks
        $updateQuery = $conn->prepare("UPDATE $table SET score = ?, remarks = ? WHERE id = ?");
        $updateQuery->bind_param("dsi", $score, $remarks, $result_id);
        
        if ($updateQuery->execute()) {
            echo "<p>Result updated successfully.</p>";
            header("Location: view_all_students_{$type}_results.php");
            exit;
        } else {
            echo "<p>Error updating result.</p>";
        }
    }
} else {
    die('Missing parameters.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar_hod.php'; ?>
    <main>
        <h2>Edit Result</h2>
        <form method="POST">
            <label for="score">Score:</label>
            <input type="number" id="score" name="score" step="0.01" value="<?php echo htmlspecialchars($result['score']); ?>" required>
            <label for="remarks">Remarks:</label>
            <input type="text" id="remarks" name="remarks" value="<?php echo htmlspecialchars($result['remarks']); ?>">
            <button type="submit">Update</button>
        </form>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
