<?php
/**
 * View Profile - Second Chance University Portal
 * Matches phpMyAdmin schema: id, fname, lname, email, phone, role, created_at
 */

session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = " . (int)$user_id . " LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header("Location: logout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Second Chance</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php include __DIR__ . '/includes/toast.php'; ?>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="main-wrapper">
        <div class="auth-wrapper" style="max-width: 580px;">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-header-icon">🎓</div>
                    <h2>User Profile</h2>
                    <p>Second Chance Campus Account</p>
                </div>

                <div style="background: var(--input-bg); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid var(--border-light);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.9rem; border-bottom: 1px solid var(--border-light); padding-bottom: 0.75rem;">
                        <span style="font-weight: 600; color: var(--text-muted); font-size: 0.88rem;">First Name:</span>
                        <span style="font-weight: 700; color: var(--primary-dark);"><?php echo htmlspecialchars($user['fname']); ?></span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.9rem; border-bottom: 1px solid var(--border-light); padding-bottom: 0.75rem;">
                        <span style="font-weight: 600; color: var(--text-muted); font-size: 0.88rem;">Last Name:</span>
                        <span style="font-weight: 700; color: var(--primary-dark);"><?php echo htmlspecialchars($user['lname']); ?></span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.9rem; border-bottom: 1px solid var(--border-light); padding-bottom: 0.75rem;">
                        <span style="font-weight: 600; color: var(--text-muted); font-size: 0.88rem;">Email Address:</span>
                        <span style="font-weight: 700; color: var(--primary-dark);"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.9rem; border-bottom: 1px solid var(--border-light); padding-bottom: 0.75rem;">
                        <span style="font-weight: 600; color: var(--text-muted); font-size: 0.88rem;">Phone Number:</span>
                        <span style="font-weight: 700; color: var(--primary-dark);"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-weight: 600; color: var(--text-muted); font-size: 0.88rem;">Role:</span>
                        <span>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="user-badge badge-admin">Administrator</span>
                            <?php else: ?>
                                <span class="user-badge badge-student">Student</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem;">
                    <a href="index.php" class="btn btn-outline" style="flex: 1;">Back to Portal</a>
                    <a href="logout.php" class="btn btn-primary" style="flex: 1; background-color: var(--primary-slate);">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>
