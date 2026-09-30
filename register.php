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

<!-- Register Page Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-12 border border-slate-300 bg-white">

        <!-- Left Branding & Info Column (5 cols) -->
        <div class="md:col-span-5 bg-slate-900 text-white p-8 sm:p-10 flex flex-col justify-between space-y-8 border-b md:border-b-0 md:border-r border-slate-800">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-sky-600 flex items-center justify-center border border-sky-500/30">
                        <i class="fa-solid fa-user-plus text-white text-base"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight font-brand block leading-none">SkyWings</span>
                        <span class="text-[9px] uppercase tracking-widest text-sky-400 font-bold">New Membership</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <h2 class="text-2xl font-extrabold text-white tracking-tight leading-tight">
                        Create Your Passenger Account
                    </h2>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Join SkyWings to manage your flight itineraries, generate boarding passes instantly, and track flight statuses in real-time.
                    </p>
                </div>

                <!-- Features List -->
                <div class="mt-8 space-y-4 text-xs text-slate-300 pt-6 border-t border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-sky-600/20 text-sky-400 flex items-center justify-center border border-sky-500/30 flex-shrink-0">
                            <i class="fa-solid fa-bolt text-xs"></i>
                        </div>
                        <span>Fast & Hassle-Free Flight Booking</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-emerald-600/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 flex-shrink-0">
                            <i class="fa-solid fa-qrcode text-xs"></i>
                        </div>
                        <span>Instant E-Tickets with Barcode</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-amber-600/20 text-amber-400 flex items-center justify-center border border-amber-500/30 flex-shrink-0">
                            <i class="fa-solid fa-headset text-xs"></i>
                        </div>
                        <span>24/7 Dedicated Passenger Support</span>
                    </div>
                </div>
            </div>

            <!-- Login Prompt -->
            <div class="pt-6 border-t border-slate-800 text-xs text-slate-400 flex items-center justify-between">
                <span>Already registered?</span>
                <a href="login.php" class="text-sky-400 hover:text-sky-300 font-bold text-decoration-none">
                    Log In Here &rarr;
                </a>
            </div>
        </div>

        <!-- Right Registration Form Column (7 cols) -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-between space-y-6">
            
            <div>
                <!-- Form Header -->
                <div class="border-b border-slate-200 pb-4 mb-6">
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-sky-100 text-sky-800 border border-sky-300 mb-1">
                        <i class="fa-solid fa-address-card text-[10px]"></i>
                        <span>Passenger Sign Up</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                        Register Account
                    </h1>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="p-3.5 mb-5 bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm flex-shrink-0"></i>
                        <span>
                            <?php
                            if ($_GET['error'] === 'invalidemail') {
                                echo 'Please provide a valid email address format.';
                            } elseif ($_GET['error'] === 'pwdnotmatch') {
                                echo 'Passwords do not match. Please re-enter your password.';
                            } elseif ($_GET['error'] === 'usernameexists') {
                                echo 'This username is already taken. Please choose a different username.';
                            } elseif ($_GET['error'] === 'emailexists') {
                                echo 'An account with this email address already exists. Please log in.';
                            } elseif ($_GET['error'] === 'sqlerror') {
                                echo 'Database error occurred. Please try again shortly.';
                            } else {
                                echo 'Registration failed. Please check form inputs.';
                            }
                            ?>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form action="includes/register.inc.php" method="POST" class="space-y-4">
                    
                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            <i class="fa-solid fa-user text-sky-600 mr-1"></i> Username <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="username" required placeholder="Choose a username" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors">
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            <i class="fa-solid fa-envelope text-sky-600 mr-1"></i> Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email_id" required placeholder="you@example.com" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors">
                    </div>

                    <!-- Password & Confirm Password Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                <i class="fa-solid fa-lock text-sky-600 mr-1"></i> Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="regPasswordInput" name="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least 8 characters, with at least one number, one uppercase and one lowercase letter" placeholder="Min 8 characters" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors pr-9">
                                <button type="button" onclick="toggleRegPassword('regPasswordInput', 'regPasswordToggleIcon')" class="absolute inset-y-0 right-0 px-2.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i id="regPasswordToggleIcon" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                <i class="fa-solid fa-lock text-sky-600 mr-1"></i> Confirm Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="regConfirmPasswordInput" name="password_repeat" required placeholder="Re-type password" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors pr-9">
                                <button type="button" onclick="toggleRegPassword('regConfirmPasswordInput', 'regConfirmToggleIcon')" class="absolute inset-y-0 right-0 px-2.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i id="regConfirmToggleIcon" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Password Hint -->
                    <p class="text-[11px] text-slate-400 leading-tight">
                        Must be at least 8 characters with upper & lower case letters and numbers.
                    </p>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" name="signup_submit" class="w-full py-3.5 px-6 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm uppercase tracking-wider border border-sky-700 transition-colors flex items-center justify-center gap-2">
                            <span>Create Account & Continue</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>

                </form>
            </div>

            <!-- Footer: Sign In Link -->
            <div class="pt-4 border-t border-slate-200 text-center text-xs text-slate-600">
                <span>Already have a SkyWings account?</span>
                <a href="login.php" class="ml-1 font-bold text-sky-600 hover:text-sky-700 text-decoration-none">
                    Log In &rarr;
                </a>
            </div>

        </div>

    </div>
</main>

<script>
    function toggleRegPassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
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