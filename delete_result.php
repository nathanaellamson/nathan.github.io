
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

    // Delete the record
    $deleteQuery = $conn->prepare("DELETE FROM $table WHERE result_id = ?");
    $deleteQuery->bind_param("i", $result_id);
    
    if ($deleteQuery->execute()) {
        echo "Result deleted successfully.";
    } else {
        echo "Error deleting result.";
    }

    header("Location: view_all_students_{$type}_results.php");
    exit;
} else {
    die('Missing parameters.');
}
?>
