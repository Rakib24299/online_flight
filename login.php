<?php
include_once 'helpers/helper.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect straight to dashboard
if (isset($_SESSION['userId'])) {
    header('Location: dashboard.php');
    exit();
}

subview('header.php');
?>

<!-- Login Page Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-12 border border-slate-300 bg-white">

        <!-- Left Branding & Info Column (5 cols) -->
        <div class="md:col-span-5 bg-slate-900 text-white p-8 sm:p-10 flex flex-col justify-between space-y-8 border-b md:border-b-0 md:border-r border-slate-800">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-sky-600 flex items-center justify-center border border-sky-500/30">
                        <i class="fa-solid fa-plane-departure text-white text-base"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight font-brand block leading-none">SkyWings</span>
                        <span class="text-[9px] uppercase tracking-widest text-sky-400 font-bold">Passenger Portal</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <h2 class="text-2xl font-extrabold text-white tracking-tight leading-tight">
                        Welcome Back to Your Journey
                    </h2>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Sign in to manage your flight reservations, download verified digital e-tickets, and access exclusive travel fare rates.
                    </p>
                </div>

                <!-- Features List -->
                <div class="mt-8 space-y-4 text-xs text-slate-300 pt-6 border-t border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-sky-600/20 text-sky-400 flex items-center justify-center border border-sky-500/30 flex-shrink-0">
                            <i class="fa-solid fa-ticket text-xs"></i>
                        </div>
                        <span>Instant 1-Click Boarding Pass Print</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-emerald-600/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 flex-shrink-0">
                            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                        </div>
                        <span>Real-Time Flight Status & History</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-amber-600/20 text-amber-400 flex items-center justify-center border border-amber-500/30 flex-shrink-0">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                        </div>
                        <span>Secure 256-Bit Encrypted Authentication</span>
                    </div>
                </div>
            </div>

            <!-- Admin Shortcut -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>Are you an administrator?</span>
                <a href="admin/login.php" class="text-sky-400 hover:text-sky-300 font-bold text-decoration-none">
                    Admin Portal &rarr;
                </a>
            </div>
        </div>

        <!-- Right Login Form Column (7 cols) -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-between space-y-6">
            
            <div>
                <!-- Form Header -->
                <div class="border-b border-slate-200 pb-4 mb-6">
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-sky-100 text-sky-800 border border-sky-300 mb-1">
                        <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
                        <span>Passenger Sign In</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                        Login to Your Account
                    </h1>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_GET['pwd']) && $_GET['pwd'] === 'updated'): ?>
                    <div class="p-3.5 mb-5 bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                        <span>Your password has been successfully updated! You can now log in.</span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="p-3.5 mb-5 bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
                        <span>
                            <?php
                            if ($_GET['error'] === 'invalidcred') {
                                echo 'Invalid username or password. Please verify and try again.';
                            } elseif ($_GET['error'] === 'wrongpwd') {
                                echo 'Incorrect password. Please try again or reset your password.';
                            } elseif ($_GET['error'] === 'sqlerror') {
                                echo 'Database connection error. Please try again shortly.';
                            } else {
                                echo 'An error occurred. Please try again.';
                            }
                            ?>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form action="includes/login.inc.php" method="POST" class="space-y-4">
                    
                    <!-- Username or Email -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            <i class="fa-solid fa-user text-sky-600 mr-1"></i> Username or Email Address
                        </label>
                        <input type="text" name="user_id" required autofocus placeholder="Enter your username or email" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors">
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                <i class="fa-solid fa-lock text-sky-600 mr-1"></i> Password
                            </label>
                            <a href="reset-pwd.php" class="text-xs font-bold text-sky-600 hover:text-sky-700 text-decoration-none">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <input type="password" id="loginPasswordInput" name="user_pass" required placeholder="Enter your password" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors pr-10">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <i id="passwordToggleIcon" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" name="login_but" class="w-full py-3.5 px-6 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm border border-sky-700 transition-colors flex items-center justify-center gap-2">
                            <span>Sign In to Dashboard</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>

                </form>
            </div>

            <!-- Footer: Register Link -->
            <div class="pt-4 border-t border-slate-200 text-center text-xs text-slate-600">
                <span>Don't have a passenger account?</span>
                <a href="register.php" class="ml-1 font-bold text-sky-600 hover:text-sky-700 text-decoration-none">
                    Create New Account &rarr;
                </a>
            </div>

        </div>

    </div>
</main>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('loginPasswordInput');
        const icon = document.getElementById('passwordToggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>

<?php subview('footer.php'); ?>