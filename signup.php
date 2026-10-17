<?php
/**
 * Sign Up Page
 * Second Chance University Portal
 * 
 * Matches phpMyAdmin `users` schema:
 * id, fname, lname, email, password, phone, role ('user' | 'admin'), created_at
 */

session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/mailer.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$errors = [];
$fname  = '';
$lname  = '';
$email  = '';
$phone  = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fname            = trim($_POST['fname'] ?? '');
    $lname            = trim($_POST['lname'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // --- 1. Full Server-Side Validations ---
    // First Name
    if (empty($fname)) {
        $errors['fname'] = "First name is required.";
    } elseif (mb_strlen($fname) < 2) {
        $errors['fname'] = "First name must be at least 2 characters.";
    } elseif (mb_strlen($fname) > 50) {
        $errors['fname'] = "First name cannot exceed 50 characters.";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $fname)) {
        $errors['fname'] = "First name can only contain letters.";
    }

    // Last Name
    if (empty($lname)) {
        $errors['lname'] = "Last name is required.";
    } elseif (mb_strlen($lname) < 2) {
        $errors['lname'] = "Last name must be at least 2 characters.";
    } elseif (mb_strlen($lname) > 50) {
        $errors['lname'] = "Last name cannot exceed 50 characters.";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $lname)) {
        $errors['lname'] = "Last name can only contain letters.";
    }

    // Email Address
    if (empty($email)) {
        $errors['email'] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid university or personal email address.";
    } else {
        $escapedEmail = mysqli_real_escape_string($conn, $email);
        $checkEmailQuery = "SELECT id FROM users WHERE email = '$escapedEmail' LIMIT 1";
        $emailResult = mysqli_query($conn, $checkEmailQuery);
        if ($emailResult && mysqli_num_rows($emailResult) > 0) {
            $errors['email'] = "This email is already registered. Please log in.";
        }
    }

    // Phone Number
    $cleanPhone = preg_replace('/\s+/', '', $phone);
    if (empty($phone)) {
        $errors['phone'] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10,20}$/', $cleanPhone)) {
        $errors['phone'] = "Please enter a valid phone number (10 to 20 digits).";
    } else {
        $escapedPhone = mysqli_real_escape_string($conn, $cleanPhone);
        $checkPhoneQuery = "SELECT id FROM users WHERE phone = '$escapedPhone' LIMIT 1";
        $phoneResult = mysqli_query($conn, $checkPhoneQuery);
        if ($phoneResult && mysqli_num_rows($phoneResult) > 0) {
            $errors['phone'] = "This phone number is already registered.";
        }
    }

    // Password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Password must contain at least one letter and one number.";
    }

    // Confirm Password
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // --- 2. Database Insertion (role = 'user') ---
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $escapedFname    = mysqli_real_escape_string($conn, $fname);
        $escapedLname    = mysqli_real_escape_string($conn, $lname);
        $escapedEmail    = mysqli_real_escape_string($conn, $email);
        $escapedPhone    = mysqli_real_escape_string($conn, $cleanPhone);

        // Insert into table matching phpMyAdmin columns
        $insertSql = "INSERT INTO users (fname, lname, email, password, phone, role) 
                      VALUES ('$escapedFname', '$escapedLname', '$escapedEmail', '$hashed_password', '$escapedPhone', 'user')";

        if (mysqli_query($conn, $insertSql)) {
            $newUserId = mysqli_insert_id($conn);
            $fullName  = $fname . ' ' . $lname;

            // Establish secure session
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['fname']   = $fname;
            $_SESSION['lname']   = $lname;
            $_SESSION['name']    = $fullName;
            $_SESSION['email']   = $email;
            $_SESSION['phone']   = $cleanPhone;
            $_SESSION['role']    = 'user';

            // Send confirmation email to user & admin
            sendRegistrationConfirmations($fullName, $email, $cleanPhone);

            // Pop-up Notification
            $_SESSION['toast'] = [
                'type'               => 'email_sent',
                'title'              => 'Confirmation Email Sent',
                'message'            => 'Registration complete! A confirmation email was sent to ' . htmlspecialchars($email) . ' and the administrator.',
                'show_email_preview' => true
            ];

            header("Location: index.php");
            exit();
        } else {
            $errors['db'] = "Registration failed: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Second Chance</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="main-wrapper">
        <div class="auth-wrapper" style="max-width: 520px;">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-header-icon">🎓</div>
                    <h2>Create an Account</h2>
                    <p>Register with your university credentials</p>
                </div>

                <?php if (!empty($errors['db'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($errors['db']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="signup.php" id="signupForm" novalidate>
                    
                    <!-- First Name & Last Name (2 columns) -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                        <div class="form-group">
                            <label class="form-label" for="fname">
                                First Name <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   name="fname" 
                                   id="fname" 
                                   class="form-control <?php echo isset($errors['fname']) ? 'is-invalid' : ''; ?>" 
                                   placeholder="e.g. Karim" 
                                   value="<?php echo htmlspecialchars($fname); ?>" 
                                   required>
                            <div class="invalid-feedback" id="fname-error">
                                <?php echo $errors['fname'] ?? ''; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="lname">
                                Last Name <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   name="lname" 
                                   id="lname" 
                                   class="form-control <?php echo isset($errors['lname']) ? 'is-invalid' : ''; ?>" 
                                   placeholder="e.g. Ahmed" 
                                   value="<?php echo htmlspecialchars($lname); ?>" 
                                   required>
                            <div class="invalid-feedback" id="lname-error">
                                <?php echo $errors['lname'] ?? ''; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="form-group">
                        <label class="form-label" for="email">
                            University / Personal Email <span class="required">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                               placeholder="e.g. karim@miuegypt.edu.eg" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               required>
                        <div class="invalid-feedback" id="email-error">
                            <?php echo $errors['email'] ?? ''; ?>
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label class="form-label" for="phone">
                            Phone Number <span class="required">*</span>
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                               placeholder="e.g. 01012345678" 
                               value="<?php echo htmlspecialchars($phone); ?>" 
                               required>
                        <div class="invalid-feedback" id="phone-error">
                            <?php echo $errors['phone'] ?? ''; ?>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">
                            Password <span class="required">*</span>
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                   placeholder="At least 8 characters" 
                                   required>
                            <button type="button" class="password-toggle-btn" data-target="password">Show</button>
                        </div>
                        <div class="invalid-feedback" id="password-error">
                            <?php echo $errors['password'] ?? ''; ?>
                        </div>

                        <!-- Real-Time Password Checklist -->
                        <div class="password-checklist">
                            <ul>
                                <li id="check-len" class="invalid">• At least 8 characters long</li>
                                <li id="check-letter" class="invalid">• Contains at least one letter</li>
                                <li id="check-number" class="invalid">• Contains at least one number</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">
                            Confirm Password <span class="required">*</span>
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   name="confirm_password" 
                                   id="confirm_password" 
                                   class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                   placeholder="Re-type your password" 
                                   required>
                            <button type="button" class="password-toggle-btn" data-target="confirm_password">Show</button>
                        </div>
                        <div class="invalid-feedback" id="confirm-password-error">
                            <?php echo $errors['confirm_password'] ?? ''; ?>
                        </div>
                        <div class="password-checklist" style="margin-top: 0.35rem;">
                            <ul>
                                <li id="check-match" class="invalid">• Passwords match</li>
                            </ul>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                        Sign Up
                    </button>
                </form>

                <div class="auth-footer">
                    Already have an account? <a href="login.php">Log In here</a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>
