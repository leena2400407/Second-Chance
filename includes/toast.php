<?php
/**
 * Toast Notification & Email Modal Component
 * Second Chance University Portal
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$toast = $_SESSION['toast'] ?? null;
unset($_SESSION['toast']);

$lastEmail = $_SESSION['last_email'] ?? null;
?>
<?php if ($toast): ?>
<div class="toast-container" id="toastContainer">
    <div class="toast <?php echo ($toast['type'] === 'email_sent') ? 'toast-email' : 'toast-welcome'; ?>" id="mainToast">
        <div class="toast-icon">
            <?php echo ($toast['type'] === 'email_sent') ? '📧' : '👋'; ?>
        </div>
        <div class="toast-content">
            <div class="toast-title">
                <?php echo htmlspecialchars($toast['title'] ?? 'Notification'); ?>
            </div>
            <div class="toast-message">
                <?php echo htmlspecialchars($toast['message'] ?? ''); ?>
            </div>
            
            <?php if (!empty($toast['show_email_preview']) && $lastEmail): ?>
                <div class="toast-actions">
                    <button type="button" class="toast-btn" onclick="openEmailModal()">
                        Inspect Confirmation Email ↗
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <button type="button" class="toast-close" onclick="dismissToast()" aria-label="Close">
            &times;
        </button>
    </div>
</div>
<?php endif; ?>

<?php if ($lastEmail): ?>
<!-- Email Preview Modal -->
<div class="email-modal-overlay" id="emailModalOverlay">
    <div class="email-modal-card">
        <div class="email-modal-header">
            <div style="font-weight: 700; font-size: 0.95rem;">
                📧 Dispatched Email Preview
            </div>
            <button type="button" onclick="closeEmailModal()" style="background: transparent; border: none; color: var(--cream-bg); font-size: 1.4rem; cursor: pointer; line-height: 1;">
                &times;
            </button>
        </div>
        <div style="background: #EFE9E6; padding: 0.75rem 1.5rem; font-size: 0.8rem; border-bottom: 1px solid var(--border-light); color: var(--primary-slate);">
            <div><b>To:</b> <?php echo htmlspecialchars($lastEmail['to']); ?> (<?php echo htmlspecialchars($lastEmail['recipient']); ?>)</div>
            <div><b>Subject:</b> <?php echo htmlspecialchars($lastEmail['subject']); ?></div>
            <div><b>Status:</b> <span style="color: var(--success-green); font-weight: bold;">Sent & Confirmed</span> • <?php echo htmlspecialchars($lastEmail['sent_at']); ?></div>
        </div>
        <div class="email-modal-body">
            <?php echo $lastEmail['body']; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function dismissToast() {
    const el = document.getElementById('toastContainer');
    if (el) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-10px)';
        el.style.transition = 'all 0.3s ease';
        setTimeout(() => el.remove(), 300);
    }
}

function openEmailModal() {
    const modal = document.getElementById('emailModalOverlay');
    if (modal) modal.classList.add('active');
}

function closeEmailModal() {
    const modal = document.getElementById('emailModalOverlay');
    if (modal) modal.classList.remove('active');
}

// Auto-dismiss toast after 6 seconds
document.addEventListener("DOMContentLoaded", function() {
    const toast = document.getElementById('mainToast');
    if (toast) {
        setTimeout(function() {
            dismissToast();
        }, 6000);
    }
});
</script>

