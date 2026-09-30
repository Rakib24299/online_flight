<?php
include_once 'helpers/helper.php';
subview('header.php');

$selector = $_GET['selector'] ?? '';
$validator = $_GET['validator'] ?? '';
$is_valid_token = !empty($selector) && !empty($validator) && (ctype_xdigit($selector) !== false) && (ctype_xdigit($validator) !== false);
?>

<!-- Set New Password Page (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-16 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-md w-full bg-white border border-slate-300 p-8 sm:p-10 space-y-6">

        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-lock-open"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Create New Password
            </h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                Enter and confirm your new secure account password.
            </p>
        </div>

        <!-- Alert Notifications -->
        <?php if (isset($_GET['err'])): ?>
            <div class="p-3.5 bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base flex-shrink-0"></i>
                <span>
                    <?php
                    if ($_GET['err'] === 'pwdnotmatch') {
                        echo 'Passwords do not match. Please re-enter.';
                    } elseif ($_GET['err'] === 'sqlerr') {
                        echo 'Database error. Please try again.';
                    } else {
                        echo 'Invalid or expired token request.';
                    }
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if (!$is_valid_token): ?>
            <!-- Invalid Token State -->
            <div class="bg-rose-50 border border-rose-200 p-5 text-center space-y-3">
                <p class="text-xs text-rose-800 font-semibold">
                    Could not validate your password reset token. The link may be expired or invalid.
                </p>
                <a href="reset-pwd.php" class="inline-block px-4 py-2 bg-rose-700 text-white font-bold text-xs border border-rose-800 text-decoration-none">
                    Request New Reset Link
                </a>
            </div>
        <?php else: ?>
            <!-- Form -->
            <form action="includes/reset-password.inc.php" method="POST" class="space-y-4">
                <input type="hidden" name="selector" value="<?php echo htmlspecialchars($selector); ?>">
                <input type="hidden" name="validator" value="<?php echo htmlspecialchars($validator); ?>">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        <i class="fa-solid fa-lock text-sky-600 mr-1"></i> New Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least 8 characters, with at least one number, one uppercase and one lowercase letter" placeholder="Min 8 characters" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        <i class="fa-solid fa-lock text-sky-600 mr-1"></i> Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_repeat" required placeholder="Re-type new password" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors">
                </div>

                <p class="text-[11px] text-slate-400 leading-tight">
                    Must be at least 8 characters with upper & lower case letters and numbers.
                </p>

                <div class="pt-2">
                    <button type="submit" name="new-pwd-submit" class="w-full py-3.5 px-6 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm uppercase tracking-wider border border-sky-700 transition-colors flex items-center justify-center gap-2">
                        <span>Save & Update Password</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Back to Login -->
        <div class="pt-4 border-t border-slate-200 text-center text-xs">
            <a href="login.php" class="font-bold text-slate-600 hover:text-sky-600 text-decoration-none">
                &larr; Back to Passenger Login
            </a>
        </div>

    </div>
</main>

<?php subview('footer.php'); ?>
