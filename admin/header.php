<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_admin_logged_in = isset($_SESSION['adminId']);
$admin_uname = $_SESSION['adminUname'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SkyWings Admin Console | Operations Management</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

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
                        mono: ['"JetBrains Mono"', 'monospace'],
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
<body class="bg-slate-100 min-h-screen text-slate-800 antialiased">

<!-- Admin Navigation Bar (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<header class="bg-slate-950 text-white border-b border-slate-800 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Brand Logo -->
            <a href="index.php" class="flex items-center gap-3 text-decoration-none group">
                <div class="w-9 h-9 bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-sm border border-amber-400">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <span class="text-base font-extrabold text-white tracking-tight font-brand group-hover:text-amber-400 transition-colors">SkyWings</span>
                    <span class="block text-[9px] uppercase tracking-widest text-amber-400 font-bold">Admin Console</span>
                </div>
            </a>

            <?php if ($is_admin_logged_in): ?>
                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="index.php" class="px-3.5 py-2 text-xs font-bold uppercase tracking-wider text-slate-200 hover:text-white hover:bg-slate-900 border-b-2 border-transparent hover:border-amber-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-gauge-high mr-1.5 text-amber-400"></i>Dashboard
                    </a>
                    <a href="flight.php" class="px-3.5 py-2 text-xs font-bold uppercase tracking-wider text-slate-200 hover:text-white hover:bg-slate-900 border-b-2 border-transparent hover:border-amber-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-plus mr-1.5 text-amber-400"></i>Add Flight
                    </a>
                    <a href="all_flights.php" class="px-3.5 py-2 text-xs font-bold uppercase tracking-wider text-slate-200 hover:text-white hover:bg-slate-900 border-b-2 border-transparent hover:border-amber-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-plane-tail mr-1.5 text-amber-400"></i>All Flights
                    </a>
                    <a href="list_airlines.php" class="px-3.5 py-2 text-xs font-bold uppercase tracking-wider text-slate-200 hover:text-white hover:bg-slate-900 border-b-2 border-transparent hover:border-amber-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-building mr-1.5 text-amber-400"></i>Airlines Fleet
                    </a>
                    <a href="review.php" class="px-3.5 py-2 text-xs font-bold uppercase tracking-wider text-slate-200 hover:text-white hover:bg-slate-900 border-b-2 border-transparent hover:border-amber-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-comment-dots mr-1.5 text-amber-400"></i>Reviews
                    </a>
                </nav>

                <!-- Right Admin User Actions -->
                <div class="hidden md:flex items-center gap-3">
                    <a href="../index.php" target="_blank" class="px-3 py-1.5 text-xs font-bold bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-700 transition-colors text-decoration-none flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        <span>Passenger Portal</span>
                    </a>

                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-900 border border-slate-800 text-xs">
                        <i class="fa-solid fa-user-shield text-amber-400"></i>
                        <span class="font-bold text-white"><?php echo htmlspecialchars($admin_uname); ?></span>
                    </div>

                    <form action="../includes/logout.inc.php" method="POST" class="m-0">
                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold bg-rose-950/60 text-rose-300 hover:bg-rose-600 hover:text-white border border-rose-500/30 transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-right-from-bracket text-[11px]"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex md:hidden">
                    <button id="adminMobileMenuBtn" type="button" class="p-2 text-slate-400 hover:text-white hover:bg-slate-900 border border-slate-800 focus:outline-none">
                        <i id="adminMobileMenuIcon" class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php if ($is_admin_logged_in): ?>
        <!-- Mobile Drawer Navigation -->
        <div id="adminMobileMenu" class="hidden md:hidden bg-slate-950 border-t border-slate-800 px-4 pt-3 pb-4 space-y-2">
            <a href="index.php" class="block px-3 py-2 text-sm font-bold text-slate-200 hover:bg-slate-900 text-decoration-none">
                <i class="fa-solid fa-gauge-high mr-2 text-amber-400"></i>Dashboard
            </a>
            <a href="flight.php" class="block px-3 py-2 text-sm font-bold text-slate-200 hover:bg-slate-900 text-decoration-none">
                <i class="fa-solid fa-plus mr-2 text-amber-400"></i>Add Flight
            </a>
            <a href="all_flights.php" class="block px-3 py-2 text-sm font-bold text-slate-200 hover:bg-slate-900 text-decoration-none">
                <i class="fa-solid fa-plane-tail mr-2 text-amber-400"></i>All Flights
            </a>
            <a href="list_airlines.php" class="block px-3 py-2 text-sm font-bold text-slate-200 hover:bg-slate-900 text-decoration-none">
                <i class="fa-solid fa-building mr-2 text-amber-400"></i>Airlines Fleet
            </a>
            <a href="review.php" class="block px-3 py-2 text-sm font-bold text-slate-200 hover:bg-slate-900 text-decoration-none">
                <i class="fa-solid fa-comment-dots mr-2 text-amber-400"></i>Customer Reviews
            </a>
            <div class="pt-2 border-t border-slate-800 flex items-center justify-between">
                <a href="../index.php" target="_blank" class="text-xs font-bold text-sky-400 hover:underline">
                    View Passenger Site &rarr;
                </a>
                <form action="../includes/logout.inc.php" method="POST" class="m-0">
                    <button type="submit" class="text-xs font-bold text-rose-400 hover:text-rose-300">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('adminMobileMenuBtn');
        const menu = document.getElementById('adminMobileMenu');
        const icon = document.getElementById('adminMobileMenuIcon');
        if (btn && menu) {
            btn.addEventListener('click', function() {
                menu.classList.toggle('hidden');
                if (menu.classList.contains('hidden')) {
                    icon.classList.replace('fa-xmark', 'fa-bars');
                } else {
                    icon.classList.replace('fa-bars', 'fa-xmark');
                }
            });
        }
    });
</script>
