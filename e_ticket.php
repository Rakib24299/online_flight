<?php
include_once 'helpers/helper.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config/db.php';

// Auth check
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}

$user_id = (int)$_SESSION['userId'];
$ticket = null;

if (isset($_POST['print_but']) && isset($_POST['ticket_id'])) {
    $ticket_id = (int)$_POST['ticket_id'];

    $sql = "SELECT t.ticket_id, t.seat_no, t.cost, t.class,
                   p.f_name, p.m_name, p.l_name, p.mobile, p.dob,
                   f.flight_id, f.airline, f.source, f.Destination, f.departure, f.arrivale, f.duration
            FROM ticket t
            LEFT JOIN passenger_profile p ON t.passenger_id = p.passenger_id
            LEFT JOIN flight f ON t.flight_id = f.flight_id
            WHERE t.ticket_id = ? AND t.user_id = ?";

    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 'ii', $ticket_id, $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $ticket = mysqli_fetch_assoc($res);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Ticket #<?php echo $ticket ? 'TK-' . $ticket['ticket_id'] : 'Print'; ?> | SkyWings</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

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
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <link rel="icon" href="assets/images/airtic.png" type="image/png">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        /* Strict single-page print rules */
        @media print {
            html, body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                font-size: 13px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print { 
                display: none !important; 
            }
            .print-container {
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .print-ticket {
                border: 1.5px solid #0f172a !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .print-grid {
                display: grid !important;
                grid-template-columns: repeat(12, minmax(0, 1fr)) !important;
            }
            .print-col-8 {
                grid-column: span 8 / span 8 !important;
            }
            .print-col-4 {
                grid-column: span 4 / span 4 !important;
                border-left: 2px dashed #94a3b8 !important;
                border-top: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-200 min-h-screen antialiased">

<?php if (!$ticket): ?>
    <!-- No ticket data state -->
    <div class="max-w-lg mx-auto mt-20 bg-white border border-slate-300 p-8 rounded-none text-center">
        <div class="w-12 h-12 bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 border border-slate-200">
            <i class="fa-solid fa-ticket-simple text-xl"></i>
        </div>
        <h2 class="text-lg font-bold text-slate-900 mb-1">No Ticket Data</h2>
        <p class="text-xs text-slate-500 mb-5">Please select a valid ticket from your ticket dashboard to generate a boarding pass.</p>
        <a href="ticket.php" class="inline-block px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold border border-sky-700 text-decoration-none transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Return to Tickets
        </a>
    </div>

<?php else:
    $dep_time = strtotime($ticket['departure']);
    $arr_time = strtotime($ticket['arrivale']);
    $class_name = ($ticket['class'] === 'B') ? 'BUSINESS CLASS' : 'ECONOMY CLASS';
    $full_name = strtoupper(trim($ticket['f_name'] . ' ' . $ticket['m_name'] . ' ' . $ticket['l_name']));
    if (empty(trim($full_name))) $full_name = 'PASSENGER';
    $ticket_code = 'TK' . str_pad($ticket['ticket_id'], 8, '0', STR_PAD_LEFT);
    $board_time = $dep_time - (30 * 60);
?>

    <!-- Top Action Bar (hidden during printing) -->
    <div class="no-print bg-slate-900 border-b border-slate-800 text-white">
        <div class="max-w-4xl mx-auto px-4 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-sky-600 flex items-center justify-center border border-sky-500/30">
                    <i class="fa-solid fa-plane-departure text-white text-xs"></i>
                </div>
                <div>
                    <span class="text-sm font-extrabold tracking-tight">SkyWings</span>
                    <span class="block text-[10px] text-sky-400 uppercase tracking-wider font-bold">Single-Page E-Ticket</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="ticket.php" class="px-3.5 py-2 text-xs font-bold bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 transition-colors text-decoration-none">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back
                </a>
                <button onclick="window.print();" class="px-5 py-2 text-xs font-bold bg-sky-600 hover:bg-sky-500 border border-sky-400/40 text-white transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i>
                    <span>Print / Save as PDF (1 Page)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Printable E-Ticket Boarding Pass (Engineered to strictly fit 1 page) -->
    <div class="print-container max-w-4xl mx-auto my-6 px-4 sm:px-0">
        <div class="print-ticket bg-white border border-slate-300">

            <!-- Ticket Header Strip -->
            <div class="bg-slate-900 text-white px-5 sm:px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-sky-600 flex items-center justify-center border border-sky-500/40">
                        <i class="fa-solid fa-plane-departure text-white text-[11px]"></i>
                    </div>
                    <div>
                        <span class="text-base font-extrabold tracking-tight leading-none block">SkyWings</span>
                        <span class="block text-[8px] uppercase tracking-widest text-sky-400 font-bold mt-0.5">Boarding Pass</span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="block text-xs font-bold text-sky-400 uppercase tracking-wider"><?php echo $class_name; ?></span>
                    <span class="block text-[10px] text-slate-400 font-semibold font-mono">#<?php echo $ticket_code; ?></span>
                </div>
            </div>

            <!-- Main Ticket Body -->
            <div class="print-grid grid grid-cols-1 lg:grid-cols-12">
                
                <!-- Left: Flight & Passenger Info (8 cols) -->
                <div class="print-col-8 lg:col-span-8 p-5 sm:p-6 space-y-4">

                    <!-- Route Display: Departure → Arrival -->
                    <div class="grid grid-cols-5 items-center">
                        <!-- Departure -->
                        <div class="col-span-2">
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Departure</span>
                            <span class="block text-2xl sm:text-3xl font-black text-slate-900 tracking-tighter leading-tight">
                                <?php echo htmlspecialchars($ticket['source']); ?>
                            </span>
                            <span class="block text-[11px] font-semibold text-slate-600 mt-1">
                                <?php echo date('M d, Y', $dep_time); ?>
                            </span>
                            <span class="block text-lg font-extrabold text-slate-900">
                                <?php echo date('h:i A', $dep_time); ?>
                            </span>
                        </div>

                        <!-- Plane Trail -->
                        <div class="col-span-1 text-center flex flex-col items-center gap-0.5 py-1">
                            <div class="w-[1px] h-3 bg-slate-200"></div>
                            <div class="w-8 h-8 bg-sky-50 border border-sky-200 flex items-center justify-center">
                                <i class="fa-solid fa-plane text-sky-600 text-xs"></i>
                            </div>
                            <span class="text-[8px] font-bold text-slate-400 uppercase mt-0.5">
                                <?php echo !empty($ticket['duration']) ? htmlspecialchars($ticket['duration']) : 'Direct'; ?>
                            </span>
                            <div class="w-[1px] h-3 bg-slate-200"></div>
                        </div>

                        <!-- Arrival -->
                        <div class="col-span-2 text-right">
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Destination</span>
                            <span class="block text-2xl sm:text-3xl font-black text-slate-900 tracking-tighter leading-tight">
                                <?php echo htmlspecialchars($ticket['Destination']); ?>
                            </span>
                            <span class="block text-[11px] font-semibold text-slate-600 mt-1">
                                <?php echo date('M d, Y', $arr_time); ?>
                            </span>
                            <span class="block text-lg font-extrabold text-slate-900">
                                <?php echo date('h:i A', $arr_time); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-dashed border-slate-200"></div>

                    <!-- Passenger Info Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-2.5 text-xs">
                        <div>
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400">Passenger Name</span>
                            <span class="block text-xs sm:text-sm font-extrabold text-slate-900 uppercase truncate"><?php echo htmlspecialchars($full_name); ?></span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400">Airline</span>
                            <span class="block text-xs sm:text-sm font-extrabold text-slate-900 truncate"><?php echo htmlspecialchars($ticket['airline']); ?></span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400">Flight No.</span>
                            <span class="block text-xs sm:text-sm font-extrabold text-slate-900">FL-<?php echo $ticket['flight_id']; ?></span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400">Contact</span>
                            <span class="block text-xs font-bold text-slate-700 truncate"><?php echo htmlspecialchars($ticket['mobile'] ?? 'N/A'); ?></span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400">Total Fare</span>
                            <span class="block text-xs font-extrabold text-slate-900">$<?php echo htmlspecialchars($ticket['cost']); ?></span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400">Class</span>
                            <span class="block text-xs font-extrabold text-slate-900"><?php echo $class_name; ?></span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-dashed border-slate-200"></div>

                    <!-- Boarding Details Highlighted Row -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-slate-50 border border-slate-200 p-2.5 text-center">
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Gate</span>
                            <span class="block text-xl font-black text-slate-900 leading-none">A-12</span>
                        </div>
                        <div class="bg-sky-50 border border-sky-200 p-2.5 text-center">
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-sky-600 mb-0.5">Seat No.</span>
                            <span class="block text-xl font-black text-sky-700 leading-none"><?php echo htmlspecialchars($ticket['seat_no']); ?></span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-2.5 text-center">
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Boarding</span>
                            <span class="block text-xl font-black text-slate-900 leading-none">
                                <?php echo date('H:i', $board_time); ?>
                            </span>
                        </div>
                    </div>

                </div>

                <!-- Right: Stub & Barcode Section (4 cols, dashed border separator) -->
                <div class="print-col-4 lg:col-span-4 border-t lg:border-t-0 lg:border-l-2 border-dashed border-slate-300 bg-slate-50/70 p-5 sm:p-6 flex flex-col justify-between">
                    
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Boarding Stub</span>
                            <span class="text-[11px] font-black text-slate-800 font-mono">#<?php echo $ticket_code; ?></span>
                        </div>

                        <!-- Mini Route -->
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-200">
                            <div>
                                <span class="block text-base font-black text-slate-900 leading-none"><?php echo htmlspecialchars($ticket['source']); ?></span>
                                <span class="block text-[9px] text-slate-500 font-semibold mt-0.5"><?php echo date('h:i A', $dep_time); ?></span>
                            </div>
                            <i class="fa-solid fa-plane text-slate-400 text-xs mx-1"></i>
                            <div class="text-right">
                                <span class="block text-base font-black text-slate-900 leading-none"><?php echo htmlspecialchars($ticket['Destination']); ?></span>
                                <span class="block text-[9px] text-slate-500 font-semibold mt-0.5"><?php echo date('h:i A', $arr_time); ?></span>
                            </div>
                        </div>

                        <!-- Stub Details -->
                        <div class="space-y-1.5 text-[11px] mb-3">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Passenger:</span>
                                <span class="font-bold text-slate-800 uppercase truncate max-w-[110px]"><?php echo htmlspecialchars($full_name); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Flight:</span>
                                <span class="font-bold text-slate-800">FL-<?php echo $ticket['flight_id']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Date:</span>
                                <span class="font-bold text-slate-800"><?php echo date('d M Y', $dep_time); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Gate / Seat:</span>
                                <span class="font-extrabold text-sky-700">A-12 / <?php echo htmlspecialchars($ticket['seat_no']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Barcode Section -->
                    <div>
                        <div class="bg-white border border-slate-200 p-2.5 text-center">
                            <!-- Barcode bars -->
                            <div class="h-8 flex items-center justify-center gap-[2px] overflow-hidden mb-1">
                                <?php
                                $bars = [3,1,4,2,1,5,2,3,1,4,1,2,5,3,1,2,4,1,3,2,1,4,2,3,1,5,2,1,3,4,1,2,3,1,2,4,5,1,3,2,1,4];
                                foreach ($bars as $b): ?>
                                    <div class="h-full bg-slate-900" style="width: <?php echo $b; ?>px;"></div>
                                <?php endforeach; ?>
                            </div>
                            <span class="block text-[9px] font-mono tracking-[0.25em] text-slate-500 font-bold"><?php echo $ticket_code; ?></span>
                        </div>

                        <!-- Notice -->
                        <p class="text-[9px] text-slate-400 text-center mt-2 leading-tight">
                            Please be at boarding gate <strong class="text-slate-600">30 min</strong> before departure.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Footer Strip -->
            <div class="bg-slate-100 border-t border-slate-300 px-5 sm:px-6 py-2.5 flex flex-col sm:flex-row items-center justify-between text-[9px] text-slate-400 gap-1">
                <span>&copy; <?php echo date('Y'); ?> SkyWings Aviation &mdash; Electronically generated boarding pass</span>
                <span class="font-mono font-bold"><?php echo $ticket_code; ?> &bull; <?php echo date('d/m/Y H:i', time()); ?></span>
            </div>

        </div>
    </div>

    <!-- Auto-trigger print dialog -->
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>

<?php endif; ?>

</body>
</html>
