<?php
$includeHeader = true;
$includeFooter = true;

$includeHeader ? require_once '../header.php' : null;

require_once '../functions.php';

// Ensure the user is logged in
guard();

// Initialize default values for errors and success messages
$errors = [];
$successMessage = "";

// Initialize students in session if not set
if (empty($_SESSION['students'])) {
    $_SESSION['students'] = [];
}

// Handle form submission for adding a student
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize student data
    $studentData = array_map('trim', [
        'student_id' => $_POST['student_id'] ?? '',
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? ''
    ]);

    // Validate the required fields
    $errors = validateStudentData($studentData);

    // Check for duplicate student ID if no validation errors
    if (empty($errors)) {
        $duplicateCheck = checkDuplicateStudentData($studentData);
        if ($duplicateCheck) {
            $errors[] = $duplicateCheck;
        }
    }

    // Save student if no errors
    if (empty($errors)) {
        $_SESSION['students'][] = $studentData;
        $successMessage = "Student added successfully!";
    }
}
?>

<div class="container my-5">
    <!-- Page Title and Breadcrumb Navigation -->
    <h3>Register a New Student</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Register Student</li>
        </ol>
    </nav>

    <!-- Display Success Message if Student is Added Successfully -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display Error Messages -->
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <strong>System Errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Add Student Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Enter Student ID" value="<?php echo htmlspecialchars($studentData['student_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" value="<?php echo htmlspecialchars($studentData['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" value="<?php echo htmlspecialchars($studentData['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Add Student</button>
            </form>
        </div>
    </div>

    <!-- Student List Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Student List</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($_SESSION['students'])): ?>
                        <tr>
                            <td colspan="4" class="text-center">No student records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($_SESSION['students'] as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($student['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="delete.php?id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-sm btn-danger">Delete</a>
                                    <a href="attach-subject.php?student_id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-sm btn-warning">Attach Subject</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$includeFooter ? require_once '../footer.php' : null;
?>
