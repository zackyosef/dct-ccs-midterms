<?php 
function checkUserSessionIsActive() {
    // Check if the session is already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Start the session if not already started
    }

    // If the user is already logged in (i.e., session variable 'email' is set and not empty)
    if (!empty($_SESSION['email'])) {
        // Redirect to the dashboard
        header("Location: dashboard.php");
        exit; // Exit to prevent further code execution
    }
}

function validateLoginCredentials($email, $password) {
    // Initialize an empty array to store error messages
    $errors = [];

    // Validate the email field
    if (empty(trim($email))) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Validate the password field
    if (empty(trim($password))) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long"; 
    }

    // Return the array of errors (empty if no errors found)
    return $errors;
}

function getUsers() {
    // Define an associative array of users with their credentials
    $users = [
        [
            'email' => 'user1@email.com',
            'password' => password_hash('password1', PASSWORD_DEFAULT) // Hashed password for security
        ],
        [
            'email' => 'user2@email.com',
            'password' => password_hash('password2', PASSWORD_DEFAULT)
        ],
        [
            'email' => 'user3@email.com',
            'password' => password_hash('password3', PASSWORD_DEFAULT)
        ],
        [
            'email' => 'user4@email.com',
            'password' => password_hash('password4', PASSWORD_DEFAULT)
        ],
        [
            'email' => 'user5@email.com',
            'password' => password_hash('password5', PASSWORD_DEFAULT)
        ],
    ];

    // Return the list of users
    return $users;
}

function checkLoginCredentials($email, $password, $users) {
    // Iterate through the list of users to find a match by email
    foreach ($users as $user) {
        // Check if the email matches
        if ($user['email'] === $email) {
            // Use password_verify to check the given password against the hashed password
            if (password_verify($password, $user['password'])) {
                return true; // Return true if both email and password match
            } else {
                return false; // Return false if the password does not match
            }
        }
    }
    
    // Return false if no matching email is found
    return false;
}

function displayErrors($errors) {
    // Return an empty string if there are no errors
    if (empty($errors)) {
        return '';
    }

    // Initialize the HTML string with an alert container
    $html = '<div class="alert alert-danger"><strong>System Errors:</strong><ul>';

    // Loop through each error and append it to the HTML string
    foreach ($errors as $error) {
        $html .= '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>'; // Escape error message for security
    }

    // Close the unordered list and alert div
    $html .= '</ul></div>';

    // Return the constructed HTML string
    return $html;
}

function renderErrorsToView($error) {
    // Return an empty string if there's no error to display
    if (empty($error)) {
        return '';
    }

    // Construct HTML string for a dismissible alert
    $html = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    $html .= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); // Escape the error message
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    $html .= '</div>';

    // Return the constructed HTML string
    return $html;
}

function guard() {
    // Ensure the session is started before accessing session variables
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user is not logged in (i.e., 'email' is not set or is empty)
    if (empty($_SESSION['email'])) {
        // Redirect to the login page (index.php)
        header("Location: index.php");
        exit; // Terminate the script to ensure no further code is executed
    }
}

function validateStudentData(array $student_data): array {
    // Initialize an empty array to store error messages
    $errors = [];

    // Validation checks for each required field
    if (empty($student_data['student_id'])) {
        $errors[] = "Student ID is required.";
    } elseif (!preg_match('/^\d{5,10}$/', $student_data['student_id'])) {
        $errors[] = "Student ID must be between 5 and 10 digits.";
    }

    if (empty($student_data['first_name'])) {
        $errors[] = "First Name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s-]+$/', $student_data['first_name'])) {
        $errors[] = "First Name must contain only letters, spaces, or hyphens.";
    }

    if (empty($student_data['last_name'])) {
        $errors[] = "Last Name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s-]+$/', $student_data['last_name'])) {
        $errors[] = "Last Name must contain only letters, spaces, or hyphens.";
    }

    return $errors;
}

function checkDuplicateStudentData(array $student_data): string {
    // Check for duplicate student ID in the current session students array
    foreach ($_SESSION['students'] as $student) {
        if (strcasecmp($student['student_id'], $student_data['student_id']) === 0) {
            return "Duplicate Student ID found.";
        }
    }
    return "";
}

function getSelectedStudentIndex(string $student_id): ?int {
    // Iterate through students to find the index of the student with the given ID
    foreach ($_SESSION['students'] as $index => $student) {
        if (isset($student['student_id']) && $student['student_id'] === $student_id) {
            return $index; // Return the index if the student is found
        }
    }

    return null; // Return null if the student is not found
}

function getSelectedStudentData(int $index): ?array {
    // Check if the student exists at the given index and return the data if it does
    return $_SESSION['students'][$index] ?? null;
}

function validateSubjectData(array $subject_data): array {
    // Initialize an empty array to store error messages
    $errors = [];

    // Validate subject code
    if (empty($subject_data['subject_code'])) {
        $errors[] = "Subject Code is required.";
    } elseif (!preg_match('/^[A-Z0-9]{3,10}$/', $subject_data['subject_code'])) {
        $errors[] = "Subject Code must be between 3 and 10 uppercase alphanumeric characters.";
    }

    // Validate subject name
    if (empty($subject_data['subject_name'])) {
        $errors[] = "Subject Name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $subject_data['subject_name'])) {
        $errors[] = "Subject Name must contain only letters and spaces.";
    }

    return $errors;
}

function checkDuplicateSubjectData(array $subject_data): ?string {
    // Check if the subject already exists in session
    if (!isset($_SESSION['subjects'])) {
        return null; // If no subjects exist, return null
    }

    foreach ($_SESSION['subjects'] as $subject) {
        // Check if the subject code or name already exists (case-insensitive comparison)
        if (strcasecmp($subject['subject_code'], $subject_data['subject_code']) === 0) {
            return "Duplicate Subject Code: " . htmlspecialchars($subject_data['subject_code'], ENT_QUOTES, 'UTF-8') . " already exists.";
        }
        if (strcasecmp($subject['subject_name'], $subject_data['subject_name']) === 0) {
            return "Duplicate Subject Name: " . htmlspecialchars($subject_data['subject_name'], ENT_QUOTES, 'UTF-8') . " already exists.";
        }
    }

    return null; // No duplicates found
}

?>