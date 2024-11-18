<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Student Transcript</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container">
<?php include 'sidebar_hod.php'; ?>
    <h1>Student Transcript Generator</h1>
    <main>
    <section class="dashboard-content">
        <form action="transcripts.php" method="get">
            <label for="registration_number">Enter Registration Number:</label><br>
            <input type="text" id="registration_number" name="registration_number" required><br>
            <input type="submit" value="Generate Transcript">
        </form>
        <section>
</main>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
