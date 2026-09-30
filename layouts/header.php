<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_user_logged_in = isset($_SESSION['userId']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SkyWings | Online Flight Booking</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Montserrat:wght@500;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
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
                    },
                    colors: {
                        primary: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#b9dffe',
                            300: '#7cc4fd',
                            400: '#36a5fa',
                            500: '#0c87eb',
                            600: '#006ac9',
                            700: '#0154a3',
                            800: '#064786',
                            900: '#0b3c6f',
                            950: '#07264a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Bootstrap CSS for backward compatibility -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    
    <link rel="icon" href="assets/images/airtic.png" type="image/png">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

<!-- Flat Modern Navigation Bar (rounded-none, shadow-none, subtle border) -->
<header class="sticky top-0 z-50 bg-slate-900 border-b border-slate-800 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- Brand Logo -->
            <a href="<?php echo $is_user_logged_in ? 'dashboard.php' : 'index.php'; ?>" class="flex items-center gap-3 group text-decoration-none">
                <div class="w-10 h-10 rounded-none bg-sky-600 flex items-center justify-center border border-sky-500/30">
                    <i class="fa-solid fa-plane-departure text-white text-base"></i>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-white tracking-tight font-brand group-hover:text-sky-400 transition-colors">SkyWings</span>
                    <span class="block text-[10px] uppercase font-bold tracking-widest text-sky-400">
                        <?php echo $is_user_logged_in ? 'Passenger Portal' : 'Aero Booking'; ?>
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-1">
                <?php if($is_user_logged_in): ?>
                    <!-- Logged-in Passenger Navigation -->
                    <a href="dashboard.php" class="px-4 py-2 text-sm font-semibold rounded-none text-slate-200 hover:text-white hover:bg-slate-800 border-b-2 border-transparent hover:border-sky-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-gauge-high mr-1.5 text-xs text-sky-400"></i>Dashboard
                    </a>
                    <a href="booking_history.php" class="px-4 py-2 text-sm font-semibold rounded-none text-slate-200 hover:text-white hover:bg-slate-800 border-b-2 border-transparent hover:border-sky-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-clock-rotate-left mr-1.5 text-xs text-sky-400"></i>Booking History
                    </a>
                    <a href="ticket.php" class="px-4 py-2 text-sm font-semibold rounded-none text-slate-200 hover:text-white hover:bg-slate-800 border-b-2 border-transparent hover:border-sky-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-ticket mr-1.5 text-xs text-sky-400"></i>Tickets
                    </a>
                <?php else: ?>
                    <!-- Public Guest Navigation -->
                    <a href="index.php" class="px-4 py-2 text-sm font-semibold rounded-none text-slate-200 hover:text-white hover:bg-slate-800 border-b-2 border-transparent hover:border-sky-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-house mr-1.5 text-xs text-sky-400"></i>Home
                    </a>
                    <a href="book.php" class="px-4 py-2 text-sm font-semibold rounded-none text-slate-200 hover:text-white hover:bg-slate-800 border-b-2 border-transparent hover:border-sky-500 transition-colors text-decoration-none">
                        <i class="fa-solid fa-magnifying-glass mr-1.5 text-xs text-sky-400"></i>Search Flights
                    </a>
                <?php endif; ?>

                <a href="feedback.php" class="px-4 py-2 text-sm font-semibold rounded-none text-slate-200 hover:text-white hover:bg-slate-800 border-b-2 border-transparent hover:border-sky-500 transition-colors text-decoration-none">
                    <i class="fa-solid fa-comment-dots mr-1.5 text-xs text-sky-400"></i>Feedback
                </a>
            </nav>

            <!-- User Auth Action Buttons -->
            <div class="hidden md:flex items-center gap-3">
                <?php if($is_user_logged_in): ?>
                    <a href="dashboard.php" class="flex items-center gap-2 px-3.5 py-2 rounded-none bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-decoration-none">
                        <div class="w-6 h-6 rounded-none bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <span class="text-sm font-semibold text-slate-200"><?php echo htmlspecialchars($_SESSION['userUid'] ?? 'Passenger'); ?></span>
                    </a>

                    <form action="includes/logout.inc.php" method="POST" class="m-0">
                        <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-none bg-rose-950/40 text-rose-300 hover:bg-rose-600 hover:text-white border border-rose-500/30 transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                <?php else: ?>
                    <!-- Login Dropdown Button -->
                    <div class="relative" id="loginDropdownContainer">
                        <button id="loginDropdownBtn" type="button" class="px-4 py-2 text-sm font-semibold rounded-none bg-sky-600 hover:bg-sky-500 text-white border border-sky-400/40 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-right-to-bracket text-xs"></i>
                            <span>Login</span>
                            <i class="fa-solid fa-chevron-down text-[10px] ml-0.5"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="loginDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-slate-900 border border-slate-700 rounded-none p-1.5 z-50">
                            <a href="login.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none text-sm font-medium text-slate-200 hover:text-white hover:bg-slate-800 border-b border-slate-800 text-decoration-none">
                                <div class="w-6 h-6 rounded-none bg-sky-500/20 text-sky-400 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <span class="block">Passenger Login</span>
                                    <span class="block text-[11px] text-slate-400">User Dashboard & Trips</span>
                                </div>
                            </a>
                            <a href="admin/login.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-none text-sm font-medium text-slate-200 hover:text-white hover:bg-slate-800 text-decoration-none mt-1">
                                <div class="w-6 h-6 rounded-none bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div>
                                    <span class="block">Administrator</span>
                                    <span class="block text-[11px] text-slate-400">Admin Portal & Control</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="register.php" class="px-4 py-2 text-sm font-semibold rounded-none bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 transition-all text-decoration-none">
                        Register
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex md:hidden">
                <button id="mobileMenuBtn" type="button" class="p-2 rounded-none text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-700 focus:outline-none">
                    <i id="mobileMenuIcon" class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Drawer Navigation -->
    <div id="mobileMenu" class="hidden md:hidden bg-slate-900 border-t border-slate-800 px-4 pt-3 pb-5 space-y-2">
        <?php if($is_user_logged_in): ?>
            <a href="dashboard.php" class="block px-3 py-2.5 rounded-none text-base font-medium text-slate-200 hover:bg-slate-800 text-decoration-none">
                <i class="fa-solid fa-gauge-high mr-2 text-sky-400"></i>Passenger Dashboard
            </a>
            <a href="booking_history.php" class="block px-3 py-2.5 rounded-none text-base font-medium text-slate-200 hover:bg-slate-800 text-decoration-none">
                <i class="fa-solid fa-clock-rotate-left mr-2 text-sky-400"></i>Booking History
            </a>
            <a href="ticket.php" class="block px-3 py-2.5 rounded-none text-base font-medium text-slate-200 hover:bg-slate-800 text-decoration-none">
                <i class="fa-solid fa-ticket mr-2 text-sky-400"></i>Tickets
            </a>
        <?php else: ?>
            <a href="index.php" class="block px-3 py-2.5 rounded-none text-base font-medium text-slate-200 hover:bg-slate-800 text-decoration-none">
                <i class="fa-solid fa-house mr-2 text-sky-400"></i>Home
            </a>
            <a href="book.php" class="block px-3 py-2.5 rounded-none text-base font-medium text-slate-200 hover:bg-slate-800 text-decoration-none">
                <i class="fa-solid fa-magnifying-glass mr-2 text-sky-400"></i>Search Flights
            </a>
        <?php endif; ?>
        
        <a href="feedback.php" class="block px-3 py-2.5 rounded-none text-base font-medium text-slate-200 hover:bg-slate-800 text-decoration-none">
            <i class="fa-solid fa-comment-dots mr-2 text-sky-400"></i>Feedback
        </a>
        
        <div class="pt-4 border-t border-slate-800 space-y-2">
            <?php if($is_user_logged_in): ?>
                <div class="px-3 py-2 text-sm text-slate-300">
                    Logged in as: <strong class="text-white"><?php echo htmlspecialchars($_SESSION['userUid'] ?? 'Passenger'); ?></strong>
                </div>
                <form action="includes/logout.inc.php" method="POST" class="m-0">
                    <button type="submit" class="w-full text-left px-3 py-2.5 rounded-none text-base font-semibold text-rose-300 bg-rose-950/40 hover:bg-rose-600 hover:text-white border border-rose-500/30 transition-all">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Logout
                    </button>
                </form>
            <?php else: ?>
                <a href="login.php" class="block text-center px-4 py-2.5 rounded-none bg-sky-600 text-white font-semibold text-decoration-none border border-sky-400/40">
                    Passenger Login
                </a>
                <a href="admin/login.php" class="block text-center px-4 py-2.5 rounded-none bg-slate-800 text-slate-200 font-semibold text-decoration-none border border-slate-700">
                    Admin Portal
                </a>
                <a href="register.php" class="block text-center px-4 py-2 text-sm text-sky-400 hover:underline">
                    Create New Account
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    // Login dropdown toggle
    const loginDropdownBtn = document.getElementById('loginDropdownBtn');
    const loginDropdownMenu = document.getElementById('loginDropdownMenu');
    if (loginDropdownBtn && loginDropdownMenu) {
        loginDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            loginDropdownMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', () => {
            if (!loginDropdownMenu.classList.contains('hidden')) {
                loginDropdownMenu.classList.add('hidden');
            }
        });
    }

    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuIcon = document.getElementById('mobileMenuIcon');
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenuIcon.classList.replace('fa-xmark', 'fa-bars');
            } else {
                mobileMenuIcon.classList.replace('fa-bars', 'fa-xmark');
            }
        });
    }
</script>