<?php
include_once 'helpers/helper.php';
subview('header.php');
?>

<!-- Reset Password Request Page (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-16 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-md w-full bg-white border border-slate-300 p-8 sm:p-10 space-y-6">

        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Forgot Password?
            </h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                Enter your registered passenger email address. We will generate a secure reset link for your account.
            </p>
        </div>

        <!-- Alert Notifications -->
        <?php if (isset($_GET['mail']) && $_GET['mail'] === 'success'): ?>
            <div class="p-3.5 bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base flex-shrink-0"></i>
                <span>Password reset instructions have been generated! Please check your email inbox.</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['err'])): ?>
            <div class="p-3.5 bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base flex-shrink-0"></i>
                <span>
                    <?php
                    if ($_GET['err'] === 'invalidemail') {
                        echo 'Please enter a valid email address.';
                    } elseif ($_GET['err'] === 'sqlerr' || $_GET['err'] === 'mailerr') {
                        echo 'System error processing request. Please try again.';
                    } else {
                        echo 'An error occurred. Please try again.';
                    }
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="includes/reset-request.inc.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    <i class="fa-solid fa-envelope text-sky-600 mr-1"></i> Registered Email Address
                </label>
                <input type="email" name="user_email" required autofocus placeholder="you@example.com" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors">
            </div>

            <div class="pt-2">
                <button type="submit" name="reset-req-submit" class="w-full py-3.5 px-6 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm uppercase tracking-wider border border-sky-700 transition-colors flex items-center justify-center gap-2">
                    <span>Send Reset Request</span>
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </div>
        </form>

        <!-- Back to Login -->
        <div class="pt-4 border-t border-slate-200 text-center text-xs">
            <a href="login.php" class="font-bold text-slate-600 hover:text-sky-600 text-decoration-none">
                &larr; Remember your password? Sign In
            </a>
        </div>

    </div>
</main>

<?php subview('footer.php'); ?>
