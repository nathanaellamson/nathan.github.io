<?php
ob_start(); // Start output buffering

// Include the TCPDF library
require_once('tcpdf/tcpdf.php');
require 'connection1.php';

// Check if registration number is set
if (!isset($_GET['registration_number'])) {
    die('Registration number is required.');
}

$registration_number = $_GET['registration_number'];

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student data
$studentQuery = $conn->prepare("SELECT * FROM students WHERE registration_number = ?");
$studentQuery->bind_param("s", $registration_number);
$studentQuery->execute();
$student = $studentQuery->get_result()->fetch_assoc();

// Debugging: Check if student data is retrieved
if (!$student) {
    die('Student not found. Please check the registration number.');
}

// Fetch academic results grouped by academic year and semester
function fetchResultsGrouped($conn, $registration_number, $course, $level) {
    $results = [];
    $query = "
        SELECT cr.academic_year, m.module_name, 
               cr.score AS coursework_score, 
               ur.score AS ue_score, 
               sr.score AS sup_score, 
               COALESCE(cr.remarks, ur.remarks, sr.remarks) AS remarks,
               m.semester
        FROM modules m
        LEFT JOIN coursework_results cr ON cr.module_code = m.module_code AND cr.registration_number = ?
        LEFT JOIN ue_results ur ON ur.module_code = m.module_code AND ur.registration_number = ?
        LEFT JOIN sup_results sr ON sr.module_code = m.module_code AND sr.registration_number = ?
        WHERE m.course = ? AND m.level = ?
        ORDER BY cr.academic_year, m.semester, m.module_name";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $registration_number, $registration_number, $registration_number, 
                                  $course, $level);
    $stmt->execute();

    // Check for errors in the query execution
    if ($stmt->error) {
        die('Query error: ' . $stmt->error);
    }

    $resultSet = $stmt->get_result();

    while ($row = $resultSet->fetch_assoc()) {
        $year = $row['academic_year'];
        $semester = $row['semester'];

        if (!isset($results[$year][$semester])) {
            $results[$year][$semester] = [];
        }

        $results[$year][$semester][] = [
            'module_name' => $row['module_name'],
            'coursework_score' => $row['coursework_score'],
            'ue_score' => $row['ue_score'],
            'sup_score' => $row['sup_score'],
            'remarks' => $row['remarks']
        ];
    }
    return $results;
}

// Fetch results data
$results = fetchResultsGrouped($conn, $registration_number, $student['course'], $student['level']);

// Debugging: Check if results were fetched successfully
if (empty($results)) {
    die('No results found for this student.');
}

// Create new PDF document
$pdf = new TCPDF();
$pdf->AddPage();

// College Information
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Cardinary Rugambwa Memorial College', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Address: 123 College Lane, Bukoba, Tanzania', 0, 1, 'C');
$pdf->Ln(10);

// Student Passport Photo (Ensure the path is correct)
$image_path = 'images/' . $student['profile_image']; // Update with actual path
$pdf->Image($image_path, 15, 40, 40, 40, 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
$pdf->Ln(5);

// Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Student Transcript', 0, 1, 'C');

// Student Information
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Name: ' . $student['surname'] . ' ' . $student['first_name'] . ' ' . $student['middle_name'], 0, 1);
$pdf->Cell(0, 10, 'Roll No: ' . $student['registration_number'], 0, 1);
$pdf->Cell(0, 10, 'Department: ' . $student['department'], 0, 1);
$pdf->Ln(10);

// Additional Information
$pdf->Cell(0, 10, 'Study course: ' . $student['course'], 0, 1);
$pdf->Cell(0, 10, 'Level of study: ' . $student['level'], 0, 1);
$pdf->Cell(0, 10, 'Gender: ' . $student['gender'], 0, 1);
$pdf->Cell(0, 10, 'Date of birth: ' . $student['dob'], 0, 1);
$pdf->Cell(0, 10, 'Students Email address: ' . $student['email'], 0, 1);
$pdf->Ln(10);

// Set table headers background color
$pdf->SetFillColor(200, 220, 255); // Light blue color for header
$pdf->SetFont('helvetica', 'B', 10);

// Table Headers (with colspan for grouping)
$pdf->Cell(0, 10, 'Academic Year and Semester', 1, 1, 'C', 1); // Grouped header

// Inner headers
$pdf->Cell(60, 10, 'Module', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'C/W', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'U/E', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'SUP', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'Remarks', 1, 1, 'C', 1); // Move to next line

// Fill table with data
$pdf->SetFont('helvetica', '', 10);
foreach ($results as $year => $semesters) {
    foreach ($semesters as $semester => $modules) {
        // Grouping by academic year and semester
        $pdf->Cell(0, 10, "$year - Semester $semester", 1, 1, 'C'); // Year and Semester row

        foreach ($modules as $module) {
            $pdf->Cell(60, 10, $module['module_name'], 1);
            $pdf->Cell(20, 10, $module['coursework_score'], 1);
            $pdf->Cell(20, 10, $module['ue_score'], 1);
            $pdf->Cell(20, 10, $module['sup_score'], 1);
            $pdf->Cell(20, 10, $module['remarks'], 1);
            $pdf->Ln();
        }
    }
}

// Clean output buffer before generating PDF
ob_end_clean(); // Clean the output buffer
$pdf->Output('transcript.pdf', 'I');

// Function to add rotated text
function RotatedText($pdf, $x, $y, $txt, $angle) {
    // Save the current transformation matrix
    $pdf->StartTransform();
    $pdf->Rotate($angle, $x, $y);
    $pdf->Text($x, $y, $txt);
    // Restore the transformation matrix
    $pdf->StopTransform();
}
