<?php 
include('header.php');  
require 'connection1.php'; // Include your database connection if needed

// Fetch departments from the database
$departments = [];
$result = $conn->query("SELECT department_name FROM departments");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row['department_name'];
    }
}

// Fetch courses from the database
$courses = [];
$course_result = $conn->query("SELECT course_name FROM courses");

if ($course_result->num_rows > 0) {
    while ($row = $course_result->fetch_assoc()) {
        $courses[] = $row['course_name'];
    }
}

// Check for messages
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>
<link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->

<div class="container">
    <?php include('sidebar_admission.php'); ?>
    <h4>Registration Form</h4>
    
    <?php if ($message): ?>
        <div class="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form id="student-registration-form" method="POST" action="student_regreceiver.php">
        <div class="form-section">
            <h4>Study Program Information</h4>
        </div>
        <input type="text" id="year_of_admission" name="year_of_admission" placeholder="Year of Admission" required>
        <select id="campus" name="campus" required>
            <option value="">Select Campus</option>
            <option value="Dar es Salaam">Dar es Salaam</option>
            <option value="Other">Other</option>
        </select>
        <select id="department" name="department" required>
            <option value="">Select Department</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo htmlspecialchars($department); ?>"><?php echo htmlspecialchars($department); ?></option>
            <?php endforeach; ?>
        </select>
        <select id="program_registered" name="program_registered" required>
            <option value="">Select Program Registered</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?php echo htmlspecialchars($course); ?>"><?php echo htmlspecialchars($course); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" id="stream" name="stream" placeholder="Class Stream" required>
        <select id="level_of_study" name="level_of_study" required>
            <option value="">Select Level of Study</option>
            <option value="certificate">Certificate</option>
            <option value="diploma">Diploma</option>
            <option value="bachelor">Bachelor</option>
            <option value="masters">Masters</option>
        </select>

        <!-- Personal Information -->
        <div class="form-section">
            <h4>Personal Information</h4>
        </div>
        <input type="text" id="surname" name="surname" placeholder="Surname" required>
        <input type="text" id="middle_name" name="middle_name" placeholder="Middle Name">
        <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
        <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <input type="date" id="dob" name="dob" placeholder="Date of Birth" required>
        <input type="text" id="district_of_birth" name="district_of_birth" placeholder="District of Birth" required>
        <input type="text" id="region_of_birth" name="region_of_birth" placeholder="Region of Birth" required>
        <input type="text" id="national_id" name="national_id" placeholder="National ID (NIDA)">
        <input type="text" id="student_status" name="student_status" placeholder="Student Status">

        <!-- Contact Information -->
        <div class="form-section">
            <h4>Contact Information</h4>
        </div>
        <input type="text" id="permanent_address" name="permanent_address" placeholder="Permanent Address" required>
        <input type="text" id="current_address" name="current_address" placeholder="Current Address">
        <input type="text" id="phone" name="phone" placeholder="Phone" required>
        <input type="email" id="email" name="email" placeholder="Email" required>

        <!-- Next of Kin Information -->
        <div class="form-section">
            <h4>Next of Kin Information</h4>
        </div>
        <input type="text" id="next_of_kin_name" name="next_of_kin_name" placeholder="Name of Next of Kin" required>
        <input type="text" id="next_of_kin_phone" name="next_of_kin_phone" placeholder="Next of Kin Phone" required>
        <input type="text" id="next_of_kin_occupation" name="next_of_kin_occupation" placeholder="Next of Kin Occupation">

        <!-- Bank Information -->
        <div class="form-section">
            <h4>Bank Information</h4>
        </div>
        <input type="text" id="bank_name" name="bank_name" placeholder="Name of Bank">
        <input type="text" id="branch_name" name="branch_name" placeholder="Branch Name">
        <input type="text" id="account_number" name="account_number" placeholder="Account Number">
        <input type="text" id="nhif_card_number" name="nhif_card_number" placeholder="NHIF Card Number">

        <!-- Entry Qualifications Information -->
        <div class="form-section">
            <h4>Entry Qualifications Information</h4>
        </div>
        <input type="text" id="form_iv_school_name" name="form_iv_school_name" placeholder="Form IV School Name" required>
        <input type="text" id="form_iv_necta_number" name="form_iv_necta_number" placeholder="Form IV NECTA Number" required>
        <input type="text" id="form_vi_school_name" name="form_vi_school_name" placeholder="Form VI School Name">
        <input type="text" id="form_vi_necta_number" name="form_vi_necta_number" placeholder="Form VI NECTA Number">

        <!-- Submit Button -->
        <button type="submit">Submit Registration</button>
    </form>
</div>

<script>
    document.getElementById('student-registration-form').addEventListener('submit', function() {
        document.getElementById('loading-spinner').style.display = 'block';
    });
</script>


<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        display: flex; /* Use flexbox */
        flex-direction: column; /* Stack items vertically */
        min-height: 100vh; /* Ensure the body takes full height */
    }

    .container {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        width: 100%;
        height: 100%;
        flex-grow: 1; /* Allow this to grow and fill space */
    }

    main {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 1200px;
        margin: 20px auto; /* Center the main content with margin */
        flex-grow: 1; /* Allow main to grow and fill available space */
        overflow-y: auto; /* Enable vertical scrolling if needed */
    }


    h1 {
        text-align: center;
        color: #333;
    }
    h4 {
        color: blue;
    }

    form {
        display: grid;
        grid-template-columns: repeat(5, 1fr); /* Five columns for form fields */
        gap: 10px; /* Reduced gap for a tighter layout */
    }

    input, select {
        width: 100%;
        padding: 10px; /* Increased padding for better touch targets */
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box; /* Ensures padding doesn't increase total width */
        font-size: 1em; /* Standard font size for inputs */
    }

    input[type="text"], input[type="email"], input[type="date"] {
        text-transform: uppercase; /* Auto-capitalize input fields */
    }

    select#level_of_study {
        text-transform: lowercase; /* Keep level of study lowercase */
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        form {
            grid-template-columns: 1fr; /* Stack fields vertically on small screens */
        }
        
        input, select {
            padding: 12px; /* Increased padding for better usability on small screens */
            font-size: 1.1em; /* Slightly larger font size for better readability */
        }
    }

    /* Alert styles */
    .alert {
        background-color: #ffdddd; /* Light red background for alert */
        color: #d8000c; /* Dark red text color */
        padding: 10px; /* Padding around alert */
        margin: 15px 0; /* Margin around alert */
    }

    .form-section {
        margin-top: 20px; /* Space above sections */
        margin-bottom: 10px; /* Space below sections */
    }

    button {
        background-color: blue; /* Button background color */
        color: white; /* Button text color */
        border: none; /* Remove border */
        padding: 10px 15px; /* Button padding */
        border-radius: 4px; /* Rounded corners */
        cursor: pointer; /* Change cursor to pointer on hover */
        margin-top: 10px; /* Space above button */
    }

    button:hover {
        background-color: darkblue; /* Darker blue on hover */
    }
</style>

