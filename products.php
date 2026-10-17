<?php
/**
 * Products Catalog - Second Chance University Marketplace
 * Assigned to: Walid
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Catalog - Second Chance</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="main-wrapper">
        <div style="margin-bottom: 2rem;">
            <h2>University Marketplace Catalog 🎒</h2>
            <p style="color: var(--text-muted);">Explore secondhand academic items from verified university students</p>
        </div>

        <div class="category-grid">
            <div class="category-card">
                <div class="category-icon">📐</div>
                <div class="category-name">Engineering Tools</div>
                <div class="category-desc">Calculators, drawing kits, rulers, and breadboards.</div>
            </div>
            <div class="category-card">
                <div class="category-icon">🩺</div>
                <div class="category-name">Medical & Pharmacy</div>
                <div class="category-desc">Lab coats, stethoscopes, lab goggles, and dental tools.</div>
            </div>
            <div class="category-card">
                <div class="category-icon">📚</div>
                <div class="category-name">Academic Books</div>
                <div class="category-desc">Calculus, Physics, Organic Chemistry, and summaries.</div>
            </div>
        </div>

        <div class="spec-box" style="text-align: center; background: var(--white); padding: 2rem;">
            <h3>Products Module (In Progress by Walid)</h3>
            <p style="color: var(--text-muted); margin: 0.5rem 0 1.5rem 0;">
                Product listing, filtering by category, search, and pricing details will be integrated here.
            </p>
            <a href="index.php" class="btn btn-primary">Return to Home</a>
        </div>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>
