
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>H.o.d Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Include the Header -->
    <?php include 'header.php'; ?>

    <div class="container">
        <!-- Include the  Sidebar -->
        <?php include 'sidebar_hod.php'; ?>

        <main>
            <section class="dashboard-content">
                <h1>H.o.d Dashboard</h1>
                <p>Welcome, [H.o.d Name]</p>
                <h2>Your Latest Information</h2>
                <!-- h.o.d-specific content goes here -->
            </section>
        </main>
    </div>

    <!-- Include the Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>