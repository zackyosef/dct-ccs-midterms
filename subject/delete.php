<?php
// Include the header if needed
$includeHeader = true;
$includeFooter = true;

if ($includeHeader) {
    require_once '../header.php';
}

// Guard function to ensure only logged-in users can access the page
guard();

// Retrieve subject code from URL; if not present, redirect to add subject page
$subjectCode = $_GET['code'] ?? null;
if (!$subjectCode) {
    header("Location: add.php");
    exit;
}

// Locate the subject in the session data
$subjectData = null;
$subjectIndex = null;

foreach ($_SESSION['subjects'] as $index => $subject) {
    if ($subject['subject_code'] === $subjectCode) {
        $subjectData = $subject;
        $subjectIndex = $index;
        break;
    }
}

// If the subject is not found, redirect to add subject page
if ($subjectData === null) {
    header("Location: add.php");
    exit;
}

// Handle delete confirmation when form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_delete'])) {
    unset($_SESSION['subjects'][$subjectIndex]);
    $_SESSION['subjects'] = array_values($_SESSION['subjects']); // Re-index the array

    // Set delete success message and redirect
    $_SESSION['delete_success'] = "Subject deleted successfully!";
    header("Location: add.php");
    exit;
}
?>

<div class="container my-5">
    <h3>Delete Subject</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="add.php">Add Subject</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
        </ol>
    </nav>

    <!-- Confirmation Message -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <p class="lead">Are you sure you want to delete the following subject record?</p>
            <ul class="list-unstyled">
                <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($subjectData['subject_code'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($subjectData['subject_name'], ENT_QUOTES, 'UTF-8'); ?></li>
            </ul>

            <!-- Delete Confirmation Form -->
            <form action="delete.php?code=<?php echo urlencode($subjectCode); ?>" method="POST" class="mt-4">
                <input type="hidden" name="confirm_delete" value="1">
                <a href="add.php" class="btn btn-secondary me-3">Cancel</a>
                <button type="submit" class="btn btn-danger">Delete Subject Record</button>
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
