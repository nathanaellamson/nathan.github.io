
<?php
require 'connection1.php';

if (isset($_POST['module_id'])) {
    $module_id = mysqli_real_escape_string($conn, $_POST['module_id']);

    $query = "SELECT semester FROM modules WHERE id = '$module_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $module = mysqli_fetch_assoc($result);
        echo 'Semester ' . $module['semester'];
    } else {
        echo 'Semester information not available';
    }
}
?>
