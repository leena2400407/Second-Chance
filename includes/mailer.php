<?php
/**
 * Email Notification & Confirmation Service
 * Second Chance University Portal
 * Author: Karim
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Send an HTML email and record it for immediate preview & logging.
 */
function sendPortalEmail($toEmail, $recipientName, $subject, $htmlBody) {
    $fromEmail = "no-reply@secondchance.com";
    $fromName  = "Second Chance University Portal";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $fromName <$fromEmail>\r\n";
    $headers .= "Reply-To: support@secondchance.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 1. Attempt PHP native mail if sendmail / SMTP is configured
    $hasSendmail = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') || file_exists('/usr/sbin/sendmail') || file_exists('/usr/lib/sendmail');
    if ($hasSendmail) {
        @mail($toEmail, $subject, $htmlBody, $headers);
    }

    // 2. Store in session history for immediate UI preview in modal / toast
    if (!isset($_SESSION['sent_emails'])) {
        $_SESSION['sent_emails'] = [];
    }

    $emailRecord = [
        'id'        => uniqid('email_'),
        'to'        => $toEmail,
        'recipient' => $recipientName,
        'subject'   => $subject,
        'body'      => $htmlBody,
        'sent_at'   => date('Y-m-d H:i:s'),
        'status'    => 'Sent'
    ];

    $_SESSION['sent_emails'][] = $emailRecord;
    $_SESSION['last_email'] = $emailRecord;

    // 3. Optional log file in includes/
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logFile = $logDir . '/emails.log';
    $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: $toEmail | SUBJECT: $subject\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND);

    return true;
}

/**
 * Send registration confirmations to both User and Admin.
 */
function sendRegistrationConfirmations($userName, $userEmail, $userPhone) {
    $adminEmail = "admin@secondchance.com";

    // -------------------------------------------------------------
    // 1. Confirmation Email to the USER
    // -------------------------------------------------------------
    $userSubject = "Welcome to Second Chance - Account Confirmation";
    $userHtml = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #F2E9E4; margin: 0; padding: 20px; color: #1F1F2E; }
            .email-container { max-width: 560px; margin: 0 auto; background: #FFFFFF; border-radius: 14px; overflow: hidden; border: 1px solid #E2DCD8; box-shadow: 0 4px 12px rgba(34, 34, 59, 0.08); }
            .header { background: #22223B; padding: 28px; text-align: center; color: #F2E9E4; }
            .header-badge { display: inline-block; background: #C9ADA7; color: #22223B; padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; }
            .content { padding: 30px 28px; font-size: 15px; line-height: 1.6; }
            .details-box { background: #FAF8F6; border: 1px solid #E2DCD8; border-radius: 10px; padding: 16px 20px; margin: 20px 0; }
            .detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #EDE8E5; font-size: 14px; }
            .detail-row:last-child { border-bottom: none; }
            .detail-label { color: #5C5470; font-weight: 600; }
            .detail-value { color: #22223B; font-weight: 700; }
            .btn { display: inline-block; background: #22223B; color: #F2E9E4; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; text-align: center; margin-top: 15px; }
            .footer { background: #FAF8F6; padding: 18px; text-align: center; font-size: 12px; color: #5C5470; border-top: 1px solid #E2DCD8; }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <span class="header-badge">Second Chance Portal</span>
                <h1 style="margin: 0; font-size: 22px; color: #F2E9E4;">Account Confirmation</h1>
            </div>
            <div class="content">
                <p>Hello <b>' . htmlspecialchars($userName) . '</b>,</p>
                <p>Thank you for registering on <b>Second Chance University Portal</b>. Your account has been verified and registered successfully.</p>
                
                <div class="details-box">
                    <div class="detail-row">
                        <span class="detail-label">Full Name:</span>
                        <span class="detail-value">' . htmlspecialchars($userName) . '</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Registered Email:</span>
                        <span class="detail-value">' . htmlspecialchars($userEmail) . '</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone Number:</span>
                        <span class="detail-value">' . htmlspecialchars($userPhone) . '</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value" style="color: #276749;">✓ Active Student Account</span>
                    </div>
                </div>

                <p>You can now log in using your university email address and access your campus profile.</p>
                <p style="text-align: center;">
                    <a href="login.php" class="btn">Log In to Your Account</a>
                </p>
            </div>
            <div class="footer">
                © ' . date('Y') . ' Second Chance University Portal. This is an automated confirmation email.
            </div>
        </div>
    </body>
    </html>';

    sendPortalEmail($userEmail, $userName, $userSubject, $userHtml);

    // -------------------------------------------------------------
    // 2. Notification Email to the ADMIN
    // -------------------------------------------------------------
    $adminSubject = "[Admin Alert] New Student Registration: " . $userName;
    $adminHtml = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #F2E9E4; margin: 0; padding: 20px; color: #1F1F2E; }
            .email-container { max-width: 560px; margin: 0 auto; background: #FFFFFF; border-radius: 14px; overflow: hidden; border: 1px solid #E2DCD8; box-shadow: 0 4px 12px rgba(34, 34, 59, 0.08); }
            .header { background: #22223B; border-top: 4px solid #E2A65E; padding: 24px; text-align: center; color: #F2E9E4; }
            .admin-tag { display: inline-block; background: #E2A65E; color: #22223B; padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; }
            .content { padding: 30px 28px; font-size: 15px; line-height: 1.6; }
            .details-box { background: #FAF8F6; border: 1px solid #E2DCD8; border-radius: 10px; padding: 16px 20px; margin: 20px 0; }
            .detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #EDE8E5; font-size: 14px; }
            .detail-row:last-child { border-bottom: none; }
            .detail-label { color: #5C5470; font-weight: 600; }
            .detail-value { color: #22223B; font-weight: 700; }
            .footer { background: #FAF8F6; padding: 18px; text-align: center; font-size: 12px; color: #5C5470; border-top: 1px solid #E2DCD8; }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <span class="admin-tag">Administrator Alert</span>
                <h1 style="margin: 0; font-size: 20px; color: #F2E9E4;">New User Registration</h1>
            </div>
            <div class="content">
                <p>Hello <b>Administrator</b>,</p>
                <p>A new student has registered on the <b>Second Chance University Portal</b>. The registration details are recorded below:</p>
                
                <div class="details-box">
                    <div class="detail-row">
                        <span class="detail-label">Student Name:</span>
                        <span class="detail-value">' . htmlspecialchars($userName) . '</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">' . htmlspecialchars($userEmail) . '</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">' . htmlspecialchars($userPhone) . '</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Registration Time:</span>
                        <span class="detail-value">' . date('Y-m-d H:i:s') . '</span>
                    </div>
                </div>

                <p>This account has been initialized with the default Student role (role = 0).</p>
            </div>
            <div class="footer">
                Second Chance Administration System Notification
            </div>
        </div>
    </body>
    </html>';

    sendPortalEmail($adminEmail, "Administrator", $adminSubject, $adminHtml);

    return true;
}
?>
