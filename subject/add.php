<?php
$includeHeader = true;
$includeFooter = true;

$includeHeader ? require_once '../header.php' : null;
require_once '../functions.php';

// Guard to restrict access to logged-in users only
guard();

// Initialize error and success messages
$errors = [];
$successMessage = "";

// Display delete success message if it exists in the session and clear it afterward
if (!empty($_SESSION['delete_success'])) {
    $successMessage = $_SESSION['delete_success'];
    unset($_SESSION['delete_success']);
}

// Initialize subjects in session if not set
if (empty($_SESSION['subjects'])) {
    $_SESSION['subjects'] = [];
}

// Handle form submission for adding a subject
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_data = [
        'subject_code' => trim($_POST['subject_code'] ?? ''),
        'subject_name' => trim($_POST['subject_name'] ?? '')
    ];

    // Validate the subject data
    $errors = validateSubjectData($subject_data);

    // Check for duplicate subjects
    if (empty($errors)) {
        $duplicateError = checkDuplicateSubjectData($subject_data);
        if ($duplicateError) {
            $errors[] = $duplicateError;
        }
    }

    // If no errors, save the subject and reset form fields
    if (empty($errors)) {
        $_SESSION['subjects'][] = $subject_data;
        $successMessage = "Subject added successfully!";
        $subject_data = ['subject_code' => '', 'subject_name' => '']; // Reset form fields
    }
}

// Handle subject deletion if triggered from query parameters
if (isset($_GET['action'], $_GET['code']) && $_GET['action'] === 'delete') {
    $subject_code = $_GET['code'];
    foreach ($_SESSION['subjects'] as $index => $subject) {
        if ($subject['subject_code'] === $subject_code) {
            unset($_SESSION['subjects'][$index]);
            $_SESSION['subjects'] = array_values($_SESSION['subjects']); // Re-index array
            $_SESSION['delete_success'] = "Subject deleted successfully!";
            header("Location: add.php"); // Redirect to avoid multiple deletion on refresh
            exit;
        }
    }
}
?>

<div class="container my-5">
    <h3>Add a New Subject</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
        </ol>
    </nav>

    <!-- Display Success Message if Subject Added or Deleted -->
    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display Error Messages -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>System Errors</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Add Subject Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="add.php" method="POST">
                <div class="mb-3">
                    <label for="subject_code" class="form-label">Subject Code</label>
                    <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="Enter Subject Code" value="<?php echo htmlspecialchars($subject_data['subject_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="mb-3">
                    <label for="subject_name" class="form-label">Subject Name</label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="Enter Subject Name" value="<?php echo htmlspecialchars($subject_data['subject_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Add Subject</button>
            </form>
        </div>
    </div>

    <!-- Subject List Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Subject List</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($_SESSION['subjects'])): ?>
                        <tr>
                            <td colspan="3" class="text-center">No subject found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($_SESSION['subjects'] as $subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="edit.php?code=<?php echo urlencode($subject['subject_code']); ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="add.php?action=delete&code=<?php echo urlencode($subject['subject_code']); ?>" class="btn btn-sm btn-danger">Delete</a>
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


