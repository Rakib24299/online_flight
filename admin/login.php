<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If admin is already logged in, redirect straight to admin dashboard
if (isset($_SESSION['adminId'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Administrator Portal Login | SkyWings</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        brand: ['"Montserrat"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link rel="icon" href="../assets/images/airtic.png" type="image/png">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen antialiased flex flex-col justify-between">

    <!-- Top Minimal Navigation -->
    <header class="border-b border-slate-800 bg-slate-950/80 px-4 sm:px-8 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="../index.php" class="flex items-center gap-3 text-decoration-none group">
                <div class="w-9 h-9 bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-sm border border-amber-400">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <span class="text-base font-extrabold text-white tracking-tight font-brand group-hover:text-amber-400 transition-colors">SkyWings</span>
                    <span class="block text-[9px] uppercase tracking-widest text-amber-400 font-bold">Admin Console</span>
                </div>
            </a>

            <a href="../index.php" class="px-3.5 py-1.5 text-xs font-bold text-slate-400 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-700 transition-colors text-decoration-none flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Passenger Site</span>
            </a>
        </div>
    </header>

    <!-- Main Login Card Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
    <main class="py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
        <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-12 border border-slate-700 bg-slate-950">

            <!-- Left Info Panel (5 cols) -->
            <div class="md:col-span-5 bg-gradient-to-b from-slate-950 to-slate-900 p-8 sm:p-10 flex flex-col justify-between space-y-8 border-b md:border-b-0 md:border-r border-slate-800">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/30 mb-4">
                        <i class="fa-solid fa-lock text-[10px]"></i>
                        <span>Restricted Access</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight mb-3">
                        Flight Operations Management
                    </h1>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Authorized administrative portal for SkyWings flight route scheduling, airline fleet management, passenger passenger manifests, and revenue analytics.
                    </p>

                    <!-- Core Admin Features List -->
                    <div class="mt-8 space-y-3.5 text-xs text-slate-300 pt-6 border-t border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20 flex-shrink-0">
                                <i class="fa-solid fa-plane-up text-xs"></i>
                            </div>
                            <span>Flight Scheduling & Status Control</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-sky-500/10 text-sky-400 flex items-center justify-center border border-sky-500/20 flex-shrink-0">
                                <i class="fa-solid fa-building text-xs"></i>
                            </div>
                            <span>Airline Fleet & Seat Capacity Management</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 flex-shrink-0">
                                <i class="fa-solid fa-users text-xs"></i>
                            </div>
                            <span>Passenger Manifest & Booking Logs</span>
                        </div>
                    </div>
                </div>

                <!-- Security Note -->
                <div class="pt-6 border-t border-slate-800 text-[11px] text-slate-500 flex items-center gap-2">
                    <i class="fa-solid fa-shield-check text-emerald-500 text-xs"></i>
                    <span>Authorized staff authentication only</span>
                </div>
            </div>

            <!-- Right Login Form Panel (7 cols) -->
            <div class="md:col-span-7 bg-white text-slate-900 p-8 sm:p-10 flex flex-col justify-between space-y-6">
                <div>
                    <!-- Form Header -->
                    <div class="border-b border-slate-200 pb-4 mb-6">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-300 mb-1">
                            <i class="fa-solid fa-key text-[10px]"></i>
                            <span>Admin Authentication</span>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                            Sign In as Administrator
                        </h2>
                    </div>

                    <!-- Alert Messages -->
                    <?php if (isset($_GET['error'])): ?>
                        <div class="p-3.5 mb-5 bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-2.5">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm flex-shrink-0"></i>
                            <span>
                                <?php
                                if ($_GET['error'] === 'invalidcred') {
                                    echo 'Invalid administrator username or email. Please verify.';
                                } elseif ($_GET['error'] === 'wrongpwd') {
                                    echo 'Incorrect administrator password. Please try again.';
                                } elseif ($_GET['error'] === 'sqlerror') {
                                    echo 'Database error. Please check server connection.';
                                } else {
                                    echo 'Authentication error. Please try again.';
                                }
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form action="../includes/admin/login.inc.php" method="POST" class="space-y-4">
                        
                        <!-- Username or Email -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                <i class="fa-solid fa-user-shield text-slate-700 mr-1"></i> Admin Username or Email
                            </label>
                            <input type="text" name="user_id" required autofocus placeholder="Enter admin username (e.g. admin)" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-slate-900 focus:outline-none transition-colors font-medium">
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                <i class="fa-solid fa-lock text-slate-700 mr-1"></i> Admin Password
                            </label>
                            <div class="relative">
                                <input type="password" id="adminPasswordInput" name="user_pass" required placeholder="Enter password" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-slate-900 focus:outline-none transition-colors pr-10 font-medium">
                                <button type="button" onclick="toggleAdminPassword()" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none">
                                    <i id="adminPasswordToggleIcon" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" name="login_but" class="w-full py-3.5 px-6 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm border border-slate-900 transition-colors flex items-center justify-center gap-2">
                                <i class="fa-solid fa-shield-halved text-amber-400 text-xs"></i>
                                <span>Authenticate & Access Console</span>
                            </button>
                        </div>

                    </form>
                </div>

                <!-- Footer / Return Links -->
                <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                    <span>Passenger booking portal?</span>
                    <a href="../login.php" class="font-bold text-sky-600 hover:text-sky-700 text-decoration-none">
                        Go to Passenger Login &rarr;
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Bottom Footer Strip -->
    <footer class="border-t border-slate-800 bg-slate-950 py-4 px-4 text-center text-xs text-slate-500">
        <p>&copy; <?php echo date('Y'); ?> SkyWings Aviation &bull; Operational Flight Administration System</p>
    </footer>

    <script>
        function toggleAdminPassword() {
            const input = document.getElementById('adminPasswordInput');
            const icon = document.getElementById('adminPasswordToggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>
</html>
