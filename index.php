<?php
require_once 'header.php';
isset($includeHeader) && $includeHeader ? require_once 'header.php' : null;


// Initialize an array to store any errors
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate login credentials
    $errors = validateLoginCredentials($email, $password);

    // If no validation errors, proceed to check login credentials
    if (empty($errors)) {
        $users = getUsers(); // Fetch users from storage

        if (!checkLoginCredentials($email, $password, $users)) {
            $errors[] = "Invalid email or password";
        } else {
            // Successful login: set session and redirect to the dashboard
            $_SESSION['email'] = $email;
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!-- HTML structure for the login page -->
<div class="container d-flex flex-column justify-content-center align-items-center vh-100">
    <!-- Display any error messages above the login card -->
    <?php echo displayErrors($errors); ?>

    <!-- Login card container -->
    <div class="card shadow-sm" style="width: 500px;">
        <div class="card-body">
            <h5 class="card-title mb-5">Login</h5>

            <!-- Login form -->
            <form action="index.php" method="POST" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

<?php
isset($includeFooter) && $includeFooter ? require_once 'footer.php' : null;
?>
