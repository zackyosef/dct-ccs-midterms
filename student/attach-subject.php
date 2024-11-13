<?php
// Include the header if needed
$includeHeader = true;
$includeFooter = true;

if ($includeHeader) {
    require_once '../header.php';
}
require_once '../functions.php';


// Guard to ensure only logged-in users can access this page
guard();

// Get student ID from the URL or redirect if not available
$student_id = $_GET['student_id'] ?? null;
if (!$student_id) {
    header("Location: register.php");
    exit;
}

// Get student data from the session
$studentIndex = getSelectedStudentIndex($student_id);
$studentData = getSelectedStudentData($studentIndex);

// Redirect to the register page if the student is not found
if ($studentData === null) {
    header("Location: register.php");
    exit;
}

$errors = [];
$successMessage = "";

// Handle form submission for attaching subjects
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedSubjects = $_POST['subjects'] ?? [];

    // Validate that at least one subject is selected
    $errors = validateAttachedSubject($selectedSubjects);

    if (empty($errors)) {
        // Attach selected subjects to the student
        if (!isset($studentData['attached_subjects'])) {
            $studentData['attached_subjects'] = [];
        }

        foreach ($selectedSubjects as $subject_code) {
            // Avoid attaching the same subject more than once
            if (!in_array($subject_code, $studentData['attached_subjects'])) {
                $studentData['attached_subjects'][] = $subject_code;
            }
        }

        // Update student data in session
        $_SESSION['students'][$studentIndex] = $studentData;
        $successMessage = "Subjects successfully attached to the student!";
        
        // Stay on this page after submission
        header("Location: attach-subject.php?student_id=" . urlencode($student_id));
        exit;
    }
}

// Get available subjects (subjects not yet attached to the student)
$availableSubjects = $_SESSION['subjects'] ?? [];
$attachedSubjects = $studentData['attached_subjects'] ?? [];

$subjectsToAttach = array_filter($availableSubjects, function ($subject) use ($attachedSubjects) {
    return !in_array($subject['subject_code'], $attachedSubjects);
});
?>

<div class="container my-5">
    <h3>Attach Subject to Student</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attach Subject to Student</li>
        </ol>
    </nav>

    <!-- Display success or error messages -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>System Errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Student Information -->
    <div>
        <strong>Selected Student Information:</strong>
        <ul>
            <li>Student ID: <?php echo htmlspecialchars($studentData['student_id'], ENT_QUOTES, 'UTF-8'); ?></li>
            <li>Name: <?php echo htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ul>
    </div>

    <!-- Attach Subjects Form -->
    <form method="POST" action="attach-subject.php?student_id=<?php echo urlencode($student_id); ?>">
        <?php if (!empty($subjectsToAttach)): ?>
            <?php foreach ($subjectsToAttach as $subject): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="subjects[]" value="<?php echo htmlspecialchars($subject['subject_code'], ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="form-check-label">
                        <?php echo htmlspecialchars($subject['subject_code'] . " - " . $subject['subject_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary mt-3">Attach Subjects</button>
        <?php else: ?>
            <p>No subjects available to attach.</p>
        <?php endif; ?>
    </form>

    <!-- Attached Subjects List -->
    <div class="mt-5">
        <h5>Subject List</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($studentData['attached_subjects'])): ?>
                    <tr>
                        <td colspan="3" class="text-center">No subject found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($studentData['attached_subjects'] as $subject_code): ?>
                        <?php
                        $subjectIndex = getSelectedSubjectIndex($subject_code);
                        $subject = getSelectedSubjectData($subjectIndex);
                        ?>
                        <?php if ($subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="detach-subject.php?student_id=<?php echo urlencode($student_id); ?>&subject_code=<?php echo urlencode($subject['subject_code']); ?>" class="btn btn-sm btn-danger">Detach Subject</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php // Include the footer if needed
if ($includeFooter) {
    require_once '../footer.php';
} ?>
