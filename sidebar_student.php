<?php
//session_start();
//sidebar_student.php
require 'connection1.php'; // Include your database connection

// Check if the user is logged in
if (isset($_SESSION['email']) || isset($_SESSION['registration_number'])) {
    $userEmail = $_SESSION['email'] ?? null; // Use email if set
    $registrationNumber = $_SESSION['registration_number'] ?? null; // Use registration number if set

    // Query to get the profile image for staff if email is set
    if ($userEmail) {
        $query = "SELECT profile_image FROM staff WHERE email = '$userEmail'";
        $result = mysqli_query($conn, $query);
        $profileImage = '';

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $profileImage = $user['profile_image'];
        }
    }

    // If no profile image found and userEmail is not set, check for registration number
    if (empty($profileImage) && $registrationNumber) {
        $query = "SELECT profile_image FROM students WHERE registration_number = '$registrationNumber'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $profileImage = $user['profile_image'];
        }
    }

    // Set default image if no profile image found
    if (empty($profileImage)) {
        $profileImage = 'images/profile.jpg'; // Default image
    }
} else {
    // Redirect to login if not logged in
    header("Location: index.php");
    exit();
}
?>


<aside class="sidebar">
    <div class="user-profile">
        <img src="<?= $profileImage ?>" alt="User Profile Picture" class="profile-picture">
        <h3><?= isset($user) ? ($user['role'] ?? 'Student') : 'Student' ?></h3> <!-- Default to Student -->
    </div>
    <ul class="sidebar" id="sidebar">
        <li>
        <a href="student_dashboard.php"><i class="fas fa-home"></i> Home</a><br>
            <a href="#"><i class="fas fa-user"></i> Profile</a>
            <ul class="dropdown">
                <li><a href=""><i class="fas fa-eye"></i> View Profile</a></li>
                <li><a href="view_student.php"><i class="fas fa-edit"></i> Edit Profile</a></li>
                <li><a href="#"><i class="fas fa-history"></i> View Activity Logs</a></li>
            </ul>
        </li>
        <li>
            <a href="#"><i class="fas fa-calendar-alt"></i> Timetable</a>
            <ul class="dropdown">
                <li><a href="view_student_timetable.php"><i class="fas fa-eye"></i> View Timetable</a></li>
                <li><a href="#"><i class="fas fa-download"></i> Download Timetable</a></li>
                <li><a href="#"><i class="fas fa-bell"></i> Class Reminders</a></li>
                <li><a href="#"><i class="fas fa-clock"></i> Time Adjustment Notifications</a></li>
            </ul>
        </li>
        <li>
            <a href="#"><i class="fas fa-graduation-cap"></i> Academic Info</a>
            <ul class="dropdown">
                <li><a href="view_student_modules.php"><i class="fas fa-book"></i> Registered Modules</a></li>
                <li><a href="view_coursework.php"><i class="fas fa-file-alt"></i> Coursework Results</a></li>
                <li><a href="view_ue_total.php"><i class="fas fa-file"></i> Final Year Results</a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i> Progress Report</a></li>
            </ul>
        </li>
        <li>
            <a href="#"><i class="fas fa-money-bill-wave"></i> Financial</a>
            <ul class="dropdown">
                <li><a href="#"><i class="fas fa-check-circle"></i> Application Fee</a></li>
                <li><a href="#"><i class="fas fa-check-circle"></i> School Fees</a></li>
                <li><a href="#"><i class="fas fa-check-circle"></i> Other Payments</a></li>
                <li><a href="#"><i class="fas fa-history"></i> Payment History</a></li>
            </ul>
        </li>
        <li>
            <a href="#"><i class="fas fa-paper-plane"></i> Requests</a>
            <ul class="dropdown">
                <li><a href="#"><i class="fas fa-file-download"></i> Request Transcript</a></li>
                <li><a href="#"><i class="fas fa-plane"></i> Request Leave</a></li>
                <li><a href="#"><i class="fas fa-exchange-alt"></i> Request Course Change</a></li>
                <li><a href="#"><i class="fas fa-exclamation-circle"></i> Request Module Exemption</a></li>
            </ul>
        </li>
        <li>
            <a href="#"><i class="fas fa-lock"></i> Security</a>
            <ul class="dropdown">
                <li><a href="#"><i class="fas fa-eye"></i> View Logs</a></li>
                <li><a href="s_changepass.php"><i class="fas fa-key"></i> Change Password</a></li>
                <li><a href="#"><i class="fas fa-user-shield"></i> Enable 2FA</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Security Settings</a></li>
            </ul>
        </li>
    </ul>
</aside>
