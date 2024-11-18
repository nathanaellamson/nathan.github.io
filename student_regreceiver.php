<?php 
include('header.php');  
require 'connection1.php'; // Include your database connection if needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $year_of_admission = $_POST['year_of_admission'];
    $campus = $_POST['campus'];
    $program_registered = $_POST['program_registered'];
    $stream = $_POST['stream'];
    $department = $_POST['department'];
    $level_of_study = $_POST['level_of_study'];
    $surname = $_POST['surname'];
    $middle_name = $_POST['middle_name'];
    $first_name = $_POST['first_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $district_of_birth = $_POST['district_of_birth'];
    $region_of_birth = $_POST['region_of_birth'];
    $national_id = $_POST['national_id'];
    $student_status = $_POST['student_status'];
    $permanent_address = $_POST['permanent_address'];
    $current_address = $_POST['current_address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $next_of_kin_name = $_POST['next_of_kin_name'];
    $next_of_kin_phone = $_POST['next_of_kin_phone'];
    $next_of_kin_occupation = $_POST['next_of_kin_occupation'];
    $bank_name = $_POST['bank_name'];
    $branch_name = $_POST['branch_name'];
    $account_number = $_POST['account_number'];
    $nhif_card_number = $_POST['nhif_card_number'];
    $form_iv_school_name = $_POST['form_iv_school_name'];
    $form_iv_necta_number = $_POST['form_iv_necta_number'];
    $form_vi_school_name = $_POST['form_vi_school_name'];
    $form_vi_necta_number = $_POST['form_vi_necta_number'];

    // Generate the registration number
    $prefix = "CARUMCO/BTCL";
    $current_year = date('Y'); // Get the current year
    $current_month = strtoupper(date('M')); // Get the current month in uppercase

    // Ensure the registration number is unique
    $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE registration_number LIKE ?");
    $registration_number_prefix = "$prefix%"; // Prefix for querying
    $stmt->bind_param("s", $registration_number_prefix);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    // Generate unique registration number
    do {
        $suffix = str_pad($count + 1, 3, '0', STR_PAD_LEFT); // Zero pad to 3 digits
        $registration_number = "$prefix$suffix/$current_month$current_year"; // Concatenate to form final registration number
        
        // Check if this registration number already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE registration_number = ?");
        $stmt->bind_param("s", $registration_number);
        $stmt->execute();
        $stmt->bind_result($exists);
        $stmt->fetch();
        $stmt->close();

        $count++;
    } while ($exists > 0); // Continue until a unique number is found

    // Set the initial password as the username
    $username = $registration_number; // The generated username
    $password = $username; // Same as the username

    // Set the academic year dynamically
    $academic_year = date('Y') . '/' . (date('Y') + 1);

    // Insert data into the database
    $insert_stmt = $conn->prepare("INSERT INTO students (registration_number, year_of_admission, campus, course, department, stream, level, surname, middle_name, first_name, gender, dob, district_of_birth, region_of_birth, national_id, student_status, permanent_address, current_address, phone, email, next_of_kin_name, next_of_kin_phone, next_of_kin_occupation, bank_name, branch_name, account_number, nhif_card_number, form_iv_school_name, form_iv_necta_number, form_vi_school_name, form_vi_necta_number, academic_year, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $insert_stmt->bind_param("sssssssssssssssssssssssssssssssss", $registration_number, $year_of_admission, $campus, $program_registered, $department, $stream, $level_of_study, $surname, $middle_name, $first_name, $gender, $dob, $district_of_birth, $region_of_birth, $national_id, $student_status, $permanent_address, $current_address, $phone, $email, $next_of_kin_name, $next_of_kin_phone, $next_of_kin_occupation, $bank_name, $branch_name, $account_number, $nhif_card_number, $form_iv_school_name, $form_iv_necta_number, $form_vi_school_name, $form_vi_necta_number, $academic_year, $password);
    
    if ($insert_stmt->execute()) {
        echo "Student registered successfully!";
        // After successful form processing
header('Location: student_regform.php?message=Registration successful');
exit();

        // Optionally, you can redirect to another page or send a success message
    } else {
        if ($insert_stmt->errno === 1062) { // Duplicate entry error code
            echo "Error: Duplicate registration number. Please try again.";
            
// If there is an error
header('Location: student_regform.php?message=Error: Registration failed');
exit();
        } else {
            echo "Error: " . $insert_stmt->error;
        }
    }

    $insert_stmt->close();
    $conn->close();
}
?>
