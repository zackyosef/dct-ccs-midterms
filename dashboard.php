<?php
$includeHeader = true;
$includeFooter = true;

$includeHeader ? require_once 'header.php' : null;

require_once 'functions.php'; 

// Ensure the user is logged in
guard();
?>

<!-- Main Content Container -->
<div class="container">

    <!-- Welcome Message -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Welcome to the System: <?php echo htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <!-- Logout Form -->
        <form method="POST" action="logout.php" class="mb-0">
            <button type="submit" name="logout" class="btn btn-danger">Logout</button>
        </form>
    </div>

    <!-- Card Container Row -->
    <div class="row">
        <!-- Add a Subject Card -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">Add a Subject</div>
                <div class="card-body">
                    <p>Add a new subject to the system by clicking the button below.</p>
                    <a href="subject/add.php" class="btn btn-primary">Add Subject</a>
                </div>
            </div>
        </div>

        <!-- Register a Student Card -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">Register a Student</div>
                <div class="card-body">
                    <p>Register a new student in the system by clicking the button below.</p>
                    <a href="student/register.php" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </div>

</div> <!-- End of Main Content Container -->

<?php
$includeFooter ? require_once 'footer.php' : null;
?>
