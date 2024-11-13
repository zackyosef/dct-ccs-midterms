<?php
$includeHeader = true;
$includeFooter = true;

$includeHeader ? require_once '../header.php' : null;
require_once '../functions.php';
guard();

// Initialize error and success message variables
$errors = [];
$successMessage = "";

// Check if the student ID is provided in the URL, otherwise redirect to register page
$student_id = $_GET['id'] ?? null;
if (!$student_id) {
    header("Location: register.php");
    exit;
}

// Retrieve student data using the provided ID
$studentIndex = getSelectedStudentIndex($student_id);
$studentData = $studentIndex !== null ? getSelectedStudentData($studentIndex) : null;

if (!$studentData) {
    header("Location: register.php");
    exit;
}

// Handle form submission for deleting the student
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Delete student from session
    unset($_SESSION['students'][$studentIndex]);

    // Re-index the session array after deletion
    $_SESSION['students'] = array_values($_SESSION['students']);

    // Set success message and redirect
    $_SESSION['success_message'] = "Student record deleted successfully!";
    header("Location: register.php"); // Redirect to the student registration page
    exit;
}
?>

<div class="container my-5">
    <h3>Delete a Student</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
        </ol>
    </nav>

    <!-- Display success or error messages -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display student details for deletion confirmation -->
    <div class="card mb-4">
        <div class="card-body">
            <p>Are you sure you want to delete the following student record?</p>
            <ul>
                <li><strong>Student ID:</strong> <?php echo htmlspecialchars($studentData['student_id'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li><strong>First Name:</strong> <?php echo htmlspecialchars($studentData['first_name'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li><strong>Last Name:</strong> <?php echo htmlspecialchars($studentData['last_name'], ENT_QUOTES, 'UTF-8'); ?></li>
            </ul>

            <!-- Confirmation form for deletion -->
            <form method="POST" action="delete.php?id=<?php echo urlencode($student_id); ?>">
                <!-- Cancel Button: Redirect back to the previous page -->
                <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancel</button>
                <!-- Delete Button: Submit to delete the student -->
                <button type="submit" class="btn btn-danger">Delete Student Record</button>
            </form>
        </div>
    </div>
</div>

<?php
$includeFooter ? require_once '../footer.php' : null;
?>
