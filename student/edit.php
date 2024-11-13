<?php
// Include header for consistent session start and functions for this script
require_once '../header.php';
require_once '../functions.php';

// Guard to ensure only logged-in users can access this page
guard();

// Check if the student ID is provided in the URL, if not, redirect to registration page
$student_id = $_GET['id'] ?? null;

if (!$student_id) {
    header("Location: register.php");
    exit;
}

// Retrieve the student data using the provided student ID
$studentIndex = getSelectedStudentIndex($student_id);
$studentData = $studentIndex !== null ? getSelectedStudentData($studentIndex) : null;

if (!$studentData) {
    header("Location: register.php");
    exit;
}

$errors = [];
$successMessage = "";

// Handle form submission for updating student data
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect updated data from the form and sanitize input
    $updatedStudentData = [
        'student_id' => $student_id, // Student ID is not editable
        'first_name' => trim($_POST['first_name']),
        'last_name' => trim($_POST['last_name']),
    ];

    // Validate the updated data
    $errors = validateStudentData($updatedStudentData);

    // Update student data if no validation errors are found
    if (empty($errors)) {
        $_SESSION['students'][$studentIndex] = $updatedStudentData;  // Update session data
        $successMessage = "Student information updated successfully!";
        // Redirect to register.php to reflect updated changes
        header("Location: register.php");
        exit;
    }
}
?>

<div class="container my-5">
    <h3>Edit Student</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
        </ol>
    </nav>

    <!-- Display Success or Error Messages -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display Errors if Any -->
    <?php echo displayErrors($errors); ?>

    <!-- Edit Student Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="edit.php?id=<?php echo urlencode($student_id); ?>">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($studentData['student_id'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($studentData['first_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($studentData['last_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Student</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>
