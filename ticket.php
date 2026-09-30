<?php
include_once 'helpers/helper.php';
subview('header.php');
require 'config/db.php';

// Check user login
if (!isset($_SESSION['userId'])) {
    echo '
    <main class="min-h-screen bg-slate-100 py-16 px-4">
        <div class="max-w-md mx-auto bg-white border border-slate-300 p-8 rounded-none text-center">
            <div class="w-12 h-12 bg-sky-100 text-sky-700 flex items-center justify-center mx-auto mb-4 border border-sky-200">
                <i class="fa-solid fa-lock text-lg"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-900 mb-2">Sign In Required</h2>
            <p class="text-xs text-slate-500 mb-6">Please log in to your account to view your booked e-tickets and boarding passes.</p>
            <a href="login.php" class="block w-full py-3 px-4 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm border border-sky-700 text-decoration-none transition-colors">
                Go to Passenger Login
            </a>
        </div>
    </main>';
    subview('footer.php');
    exit();
}

$user_id = (int)$_SESSION['userId'];
$alert_msg = '';

// Handle Ticket Cancellation
if (isset($_POST['cancel_but'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    
    // Ensure ticket belongs to the authenticated user
    $chk_sql = "SELECT passenger_id FROM ticket WHERE ticket_id = ? AND user_id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $chk_sql)) {
        mysqli_stmt_bind_param($stmt, 'ii', $ticket_id, $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            $passenger_id = (int)$row['passenger_id'];
            
            // Delete ticket and passenger profile
            mysqli_query($conn, "DELETE FROM ticket WHERE ticket_id = $ticket_id");
            mysqli_query($conn, "DELETE FROM passenger_profile WHERE passenger_id = $passenger_id");
            
            $alert_msg = "Ticket #TK-$ticket_id has been successfully cancelled.";
        }
    }
}

// Fetch all tickets for logged-in user
$tickets = [];
$t_sql = "SELECT t.ticket_id, t.seat_no, t.cost, t.class,
                 p.f_name, p.m_name, p.l_name, p.mobile,
                 f.flight_id, f.airline, f.source, f.Destination, f.departure, f.arrivale, f.duration
          FROM ticket t
          LEFT JOIN passenger_profile p ON t.passenger_id = p.passenger_id
          LEFT JOIN flight f ON t.flight_id = f.flight_id
          WHERE t.user_id = ?
          ORDER BY t.ticket_id DESC";

$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $t_sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $tickets[] = $row;
    }
}
?>

<!-- Ticket Page Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-6">
        
        <!-- Header Section -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300 mb-1">
                    <i class="fa-solid fa-ticket text-xs"></i>
                    <span>Digital Boarding Passes</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Your Confirmed E-Tickets
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Present your digital e-ticket at the boarding gate or print a physical copy for check-in.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="dashboard.php" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold border border-slate-900 flex items-center gap-1.5 transition-colors text-decoration-none">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Back to Dashboard</span>
                </a>
                <a href="dashboard.php" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs sm:text-sm font-bold border border-sky-700 flex items-center gap-1.5 transition-colors text-decoration-none">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Book Another</span>
                </a>
            </div>
        </div>

        <!-- Cancellation Alert Message -->
        <?php if (!empty($alert_msg)): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center gap-3">
                <div class="w-7 h-7 bg-emerald-100 text-emerald-700 flex items-center justify-center border border-emerald-300 flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
                <div><?php echo htmlspecialchars($alert_msg); ?></div>
            </div>
        <?php endif; ?>

        <!-- Empty State -->
        <?php if (empty($tickets)): ?>
            <div class="bg-white border border-slate-300 p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 border border-slate-200">
                    <i class="fa-solid fa-ticket-simple text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">No Tickets Issued Yet</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                    You currently have no active or previous flight tickets. Search flights now and reserve your journey in seconds.
                </p>
                <a href="index.php" class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold border border-sky-700 text-decoration-none transition-colors">
                    <i class="fa-solid fa-plane-departure text-xs"></i>
                    <span>Search Available Flights</span>
                </a>
            </div>
        <?php else: ?>

            <!-- Tickets List -->
            <div class="space-y-6">
                <?php foreach ($tickets as $t): 
                    $dep_time = strtotime($t['departure']);
                    $arr_time = strtotime($t['arrivale']);
                    $is_upcoming = $dep_time >= time();
                    $class_name = ($t['class'] === 'B') ? 'Business Class' : 'Economy Class';
                    $full_name = trim($t['f_name'] . ' ' . $t['m_name'] . ' ' . $t['l_name']);
                    if (empty($full_name)) $full_name = 'Passenger';
                ?>
                    <!-- Individual Boarding Pass Card (Sharp, Flat Modernist) -->
                    <div class="bg-white border border-slate-300 grid grid-cols-1 lg:grid-cols-12 transition-colors hover:border-slate-400">
                        
                        <!-- Main Boarding Pass Section (8 Columns on desktop) -->
                        <div class="lg:col-span-8 p-6 sm:p-7 flex flex-col justify-between space-y-6">
                            
                            <!-- Top Flight Header Bar -->
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-sky-600 text-white flex items-center justify-center font-bold text-sm border border-sky-700">
                                        <i class="fa-solid fa-plane-departure"></i>
                                    </div>
                                    <div>
                                        <span class="block text-base font-extrabold text-slate-900"><?php echo htmlspecialchars($t['airline']); ?></span>
                                        <span class="block text-[11px] font-semibold text-slate-500">Flight #FL-<?php echo $t['flight_id']; ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                        <?php echo $class_name; ?>
                                    </span>
                                    <?php if ($is_upcoming): ?>
                                        <span class="px-2.5 py-1 text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Confirmed
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-500 border border-slate-300">
                                            Completed
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Route & Schedule Display -->
                            <div class="grid grid-cols-3 items-center py-2">
                                <!-- Departure City -->
                                <div>
                                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Departure</span>
                                    <span class="block text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                        <?php echo htmlspecialchars($t['source']); ?>
                                    </span>
                                    <span class="block text-xs font-semibold text-sky-700 mt-1">
                                        <?php echo date('M d, Y', $dep_time); ?>
                                    </span>
                                    <span class="block text-sm font-bold text-slate-800">
                                        <?php echo date('h:i A', $dep_time); ?>
                                    </span>
                                </div>

                                <!-- Plane Route Trail -->
                                <div class="text-center px-2">
                                    <div class="flex items-center justify-center gap-2 text-slate-300">
                                        <div class="h-[1px] flex-1 bg-slate-300"></div>
                                        <i class="fa-solid fa-plane text-sky-600 text-sm"></i>
                                        <div class="h-[1px] flex-1 bg-slate-300"></div>
                                    </div>
                                    <span class="block text-[11px] font-semibold text-slate-500 mt-1">
                                        <?php echo !empty($t['duration']) ? htmlspecialchars($t['duration']) : 'Direct'; ?>
                                    </span>
                                </div>

                                <!-- Arrival City -->
                                <div class="text-right">
                                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Destination</span>
                                    <span class="block text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                        <?php echo htmlspecialchars($t['Destination']); ?>
                                    </span>
                                    <span class="block text-xs font-semibold text-emerald-700 mt-1">
                                        <?php echo date('M d, Y', $arr_time); ?>
                                    </span>
                                    <span class="block text-sm font-bold text-slate-800">
                                        <?php echo date('h:i A', $arr_time); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Passenger & Boarding Meta Info Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-200 text-xs">
                                <div>
                                    <span class="block font-bold uppercase text-slate-400 text-[10px]">Passenger</span>
                                    <span class="block font-extrabold text-slate-900 text-sm uppercase truncate">
                                        <?php echo htmlspecialchars($full_name); ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="block font-bold uppercase text-slate-400 text-[10px]">Seat Number</span>
                                    <span class="block font-extrabold text-sky-700 text-sm">
                                        <?php echo htmlspecialchars($t['seat_no']); ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="block font-bold uppercase text-slate-400 text-[10px]">Boarding Gate</span>
                                    <span class="block font-extrabold text-slate-900 text-sm">Gate A-12</span>
                                </div>
                                <div>
                                    <span class="block font-bold uppercase text-slate-400 text-[10px]">Total Paid</span>
                                    <span class="block font-extrabold text-slate-900 text-sm">
                                        $<?php echo htmlspecialchars($t['cost']); ?>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <!-- Perforated Stub Section (4 Columns on desktop, separated by dashed line) -->
                        <div class="lg:col-span-4 bg-slate-50 border-t lg:border-t-0 lg:border-l-2 border-dashed border-slate-300 p-6 sm:p-7 flex flex-col justify-between space-y-6">
                            
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Boarding Pass Stub</span>
                                    <span class="text-xs font-black text-slate-900">#TK-<?php echo $t['ticket_id']; ?></span>
                                </div>

                                <!-- Stub Details -->
                                <div class="space-y-2 text-xs border-b border-slate-200 pb-4 mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Passenger:</span>
                                        <span class="font-bold text-slate-800 uppercase truncate max-w-[140px]"><?php echo htmlspecialchars($full_name); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Flight:</span>
                                        <span class="font-bold text-slate-800"><?php echo htmlspecialchars($t['airline']); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Seat:</span>
                                        <span class="font-extrabold text-sky-700"><?php echo htmlspecialchars($t['seat_no']); ?> (<?php echo $t['class']; ?>)</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Date:</span>
                                        <span class="font-bold text-slate-800"><?php echo date('d M Y', $dep_time); ?></span>
                                    </div>
                                </div>

                                <!-- Flat Geometric Barcode Simulation -->
                                <div class="bg-white border border-slate-200 p-3 text-center mb-4">
                                    <div class="h-8 flex items-center justify-center gap-[2px] overflow-hidden opacity-85">
                                        <?php 
                                        $bars = [3,1,4,2,1,5,2,3,1,4,1,2,5,3,1,2,4,1,3,2,1,4,2,3,1,5,2,1,3,4,1,2];
                                        foreach ($bars as $b): ?>
                                            <div class="h-full bg-slate-900" style="width: <?php echo $b; ?>px;"></div>
                                        <?php endforeach; ?>
                                    </div>
                                    <span class="block text-[9px] font-mono tracking-widest text-slate-400 mt-1">TK<?php echo str_pad($t['ticket_id'], 8, '0', STR_PAD_LEFT); ?></span>
                                </div>
                            </div>

                            <!-- Action Buttons Directly on the Ticket -->
                            <div class="space-y-2 pt-2">
                                <!-- Print Ticket Form -->
                                <form action="e_ticket.php" target="_blank" method="POST" class="m-0">
                                    <input type="hidden" name="ticket_id" value="<?php echo $t['ticket_id']; ?>">
                                    <button type="submit" name="print_but" class="w-full py-2.5 px-3 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold border border-sky-700 transition-colors flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-print"></i>
                                        <span>Print E-Ticket</span>
                                    </button>
                                </form>

                                <!-- Cancel Ticket Form -->
                                <form action="ticket.php" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to cancel Ticket #TK-<?php echo $t['ticket_id']; ?>? This cannot be undone.');">
                                    <input type="hidden" name="ticket_id" value="<?php echo $t['ticket_id']; ?>">
                                    <button type="submit" name="cancel_but" class="w-full py-2 px-3 bg-white hover:bg-rose-50 text-rose-700 hover:text-rose-800 text-xs font-bold border border-rose-300 transition-colors flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                        <span>Cancel Ticket</span>
                                    </button>
                                </form>
                            </div>

                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php subview('footer.php'); ?>