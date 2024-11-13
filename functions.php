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


?>