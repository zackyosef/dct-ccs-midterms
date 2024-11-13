<?php
// Include the header if needed
$includeHeader = true;
$includeFooter = true;

if ($includeHeader) {
    require_once '../header.php';
}


// Guard to restrict access to logged-in users only
guard();

// Retrieve the subject code from the URL or redirect if it's not provided
$subjectCode = $_GET['code'] ?? null;
if (!$subjectCode) {
    header("Location: add.php");
    exit;
}

// Locate the subject based on the provided code in session
$subjectData = null;
$subjectIndex = null;

foreach ($_SESSION['subjects'] as $index => $subject) {
    if ($subject['subject_code'] === $subjectCode) {
        $subjectData = $subject;
        $subjectIndex = $index;
        break;
    }
}

// If the subject is not found, redirect to the add subject page
if ($subjectData === null) {
    header("Location: add.php");
    exit;
}

// Initialize error and success messages
$errors = [];
$successMessage = "";

// Handle form submission for editing the subject
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subjectName = trim($_POST['subject_name']);

    // Validation: Check if subject name is empty
    if (empty($subjectName)) {
        $errors[] = "Subject Name is required.";
    }

    // Update the subject in session if there are no errors
    if (empty($errors)) {
        $_SESSION['subjects'][$subjectIndex]['subject_name'] = $subjectName;
        $successMessage = "Subject updated successfully!";

        // Redirect back to the add subject page after update
        $_SESSION['edit_success'] = $successMessage;
        header("Location: add.php");
        exit;
    }
}
?>

<div class="container my-5">
    <h3>Edit Subject</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
        </ol>
    </nav>

    <!-- Display success message if the update was successful -->
    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display error messages -->
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

    <!-- Edit Subject Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="edit.php?code=<?php echo urlencode($subjectCode); ?>" method="POST">
                <div class="mb-3">
                    <label for="subject_code" class="form-label">Subject Code</label>
                    <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo htmlspecialchars($subjectData['subject_code'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="subject_name" class="form-label">Subject Name</label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Enter Subject Name" value="<?php echo htmlspecialchars($subjectData['subject_name'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update Subject</button>
            </form>
        </div>
    </div>
</div>

<?php
// Include the footer if needed
if ($includeFooter) {
    require_once '../footer.php';
}
?>
