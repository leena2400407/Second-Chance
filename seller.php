<?php
/**
 * Seller Dashboard - Second Chance University Marketplace
 * Assigned to: Mohab / Authentication & Session Guard by Karim
 */

session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

// Protect seller page - only sellers (or admin) can access
requireSeller();

$user = currentUser();
$flashSuccess = getFlashMessage('success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Hub - Second Chance</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="main-wrapper">
        <?php if ($flashSuccess): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($flashSuccess); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['welcome'])): ?>
            <div class="alert alert-success">
                🎉 <b>Congratulations!</b> Your seller account is active. You can now start listing university textbooks, engineering tools, lab coats, and materials.
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2>Seller Hub 📦</h2>
                <p style="color: var(--text-muted);">Welcome back, <b><?php echo htmlspecialchars($user['name']); ?></b>! Manage your university product listings.</p>
            </div>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <span class="user-badge badge-seller" style="font-size: 0.9rem; padding: 0.4rem 0.85rem;">
                    University Seller (Role: 0, Seller)
                </span>
                <a href="product-form.php" class="btn btn-primary" style="background-color: var(--primary-slate);">+ Add New Listing</a>
            </div>
        </div>

        <!-- Seller Overview Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
            <div class="spec-box" style="margin: 0; background: var(--white);">
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Registered Seller Phone</div>
                <div style="font-size: 1.4rem; font-weight: 800; color: var(--primary-dark); margin-top: 0.25rem;">
                    <?php echo htmlspecialchars($user['phone']); ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">Used for InstaPay transfers & COD handovers</div>
            </div>

            <div class="spec-box" style="margin: 0; background: var(--white); border-left-color: var(--muted-lavender);">
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Account Email</div>
                <div style="font-size: 1.15rem; font-weight: 700; color: var(--primary-slate); margin-top: 0.25rem; word-break: break-all;">
                    <?php echo htmlspecialchars($user['email']); ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">Verified university credentials</div>
            </div>

            <div class="spec-box" style="margin: 0; background: var(--white); border-left-color: var(--soft-rose);">
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Security Deposit Policy</div>
                <div style="font-size: 1.4rem; font-weight: 800; color: var(--text-dark); margin-top: 0.25rem;">
                    5% Online
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">Protects both buyer & seller reservations</div>
            </div>
        </div>

        <!-- Quick Action / Guidance -->
        <div style="background: var(--white); border-radius: var(--radius-md); padding: 2rem; border: 1px solid var(--border-color); text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">📚</div>
            <h3 style="margin-bottom: 0.5rem; color: var(--primary-dark);">Ready to sell your used university items?</h3>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 1.5rem auto; font-size: 0.95rem;">
                Help fellow students save money while clearing out your old textbooks, lab tools, engineering equipment, or study summaries.
            </p>
            <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <a href="product-form.php" class="btn btn-primary">+ Post Product Listing</a>
                <a href="products.php" class="btn btn-outline">View All Campus Listings</a>
            </div>
        </div>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>
