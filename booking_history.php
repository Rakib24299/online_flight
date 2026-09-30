<?php
include_once 'helpers/helper.php';
subview('header.php');
require 'config/db.php';

// Auth check
if (!isset($_SESSION['userId'])) {
    echo '
    <main class="min-h-screen bg-slate-100 py-16 px-4">
        <div class="max-w-md mx-auto bg-white border border-slate-300 p-8 rounded-none text-center">
            <div class="w-12 h-12 bg-sky-100 text-sky-700 flex items-center justify-center mx-auto mb-4 border border-sky-200">
                <i class="fa-solid fa-lock text-lg"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-900 mb-2">Sign In Required</h2>
            <p class="text-xs text-slate-500 mb-6">Please log in to your account to view your flight booking history and real-time flight statuses.</p>
            <a href="login.php" class="block w-full py-3 px-4 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm border border-sky-700 text-decoration-none transition-colors">
                Go to Passenger Login
            </a>
        </div>
    </main>';
    subview('footer.php');
    exit();
}

$user_id = (int)$_SESSION['userId'];
$username = $_SESSION['userUid'] ?? 'Passenger';

// Fetch all bookings for logged-in user with flight & passenger info
$bookings = [];
$sql = "SELECT t.ticket_id, t.seat_no, t.cost, t.class,
               p.f_name, p.m_name, p.l_name, p.mobile,
               f.flight_id, f.airline, f.source, f.Destination, f.departure, f.arrivale, f.duration, f.status, f.issue
        FROM ticket t
        LEFT JOIN passenger_profile p ON t.passenger_id = p.passenger_id
        LEFT JOIN flight f ON t.flight_id = f.flight_id
        WHERE t.user_id = ?
        ORDER BY t.ticket_id DESC";

$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $bookings[] = $row;
    }
}

// Stats count
$total_count = count($bookings);
$upcoming_count = 0;
$arrived_count = 0;
$delayed_count = 0;

foreach ($bookings as $b) {
    $dep_time = strtotime($b['departure']);
    if ($b['status'] === 'issue') {
        $delayed_count++;
    } elseif ($b['status'] === 'arr' || $dep_time < time()) {
        $arrived_count++;
    } else {
        $upcoming_count++;
    }
}
?>

<!-- Booking History Page Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Page Header & Metrics Strip -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300 mb-1">
                    <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                    <span>Passenger Travel Record</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Flight Booking History
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Track all your reserved flights, departures, real-time schedule statuses, and ticket receipts.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="dashboard.php" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold border border-slate-900 flex items-center gap-1.5 transition-colors text-decoration-none">
                    <i class="fa-solid fa-gauge-high text-xs"></i>
                    <span>Dashboard</span>
                </a>
                <a href="ticket.php" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs sm:text-sm font-bold border border-sky-700 flex items-center gap-1.5 transition-colors text-decoration-none">
                    <i class="fa-solid fa-ticket text-xs"></i>
                    <span>E-Tickets</span>
                </a>
            </div>
        </div>

        <!-- Metric Stat Counters (4 Columns) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-300 p-4">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Trips</span>
                <span class="block text-2xl font-black text-slate-900 mt-1"><?php echo $total_count; ?></span>
            </div>
            <div class="bg-white border border-slate-300 p-4">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Upcoming / On-Time</span>
                <span class="block text-2xl font-black text-sky-600 mt-1"><?php echo $upcoming_count; ?></span>
            </div>
            <div class="bg-white border border-slate-300 p-4">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Completed / Arrived</span>
                <span class="block text-2xl font-black text-emerald-600 mt-1"><?php echo $arrived_count; ?></span>
            </div>
            <div class="bg-white border border-slate-300 p-4">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Alerts / Delayed</span>
                <span class="block text-2xl font-black text-amber-600 mt-1"><?php echo $delayed_count; ?></span>
            </div>
        </div>

        <!-- Bookings List -->
        <?php if (empty($bookings)): ?>
            <!-- Empty State -->
            <div class="bg-white border border-slate-300 p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 border border-slate-200">
                    <i class="fa-solid fa-plane-slash text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">No Booking Records Found</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-6">
                    You haven't booked any flights yet. Start planning your next journey now with the best airline deals.
                </p>
                <a href="book.php" class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold border border-sky-700 text-decoration-none transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Search & Book Flights</span>
                </a>
            </div>
        <?php else: ?>

            <div class="space-y-5">
                <?php foreach ($bookings as $b): 
                    $dep_time = strtotime($b['departure']);
                    $arr_time = strtotime($b['arrivale']);
                    $class_label = ($b['class'] === 'B') ? 'Business Class' : 'Economy Class';
                    $full_name = trim($b['f_name'] . ' ' . $b['m_name'] . ' ' . $b['l_name']);
                    if (empty($full_name)) $full_name = 'Passenger';

                    // Determine Status & Styling
                    $status_text = "Scheduled";
                    $status_badge = "bg-sky-100 text-sky-800 border-sky-300";
                    $flight_icon_color = "text-sky-600";

                    if ($b['status'] === 'arr' || $dep_time < time()) {
                        $status_text = "Arrived / Completed";
                        $status_badge = "bg-emerald-100 text-emerald-800 border-emerald-300";
                        $flight_icon_color = "text-emerald-600";
                    } elseif ($b['status'] === 'dep') {
                        $status_text = "Departed / In Flight";
                        $status_badge = "bg-blue-100 text-blue-800 border-blue-300";
                        $flight_icon_color = "text-blue-600";
                    } elseif ($b['status'] === 'issue') {
                        $status_text = !empty($b['issue']) ? "Delayed: " . $b['issue'] : "Delayed / Issue";
                        $status_badge = "bg-rose-100 text-rose-800 border-rose-300";
                        $flight_icon_color = "text-rose-600";
                    }
                ?>
                    <!-- Individual Flight Booking Card -->
                    <div class="bg-white border border-slate-300 p-6 sm:p-7 transition-colors hover:border-slate-400 space-y-6">
                        
                        <!-- Top Info Bar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-900 text-white flex items-center justify-center font-bold text-sm border border-slate-800">
                                    <i class="fa-solid fa-plane-departure text-sky-400"></i>
                                </div>
                                <div>
                                    <span class="block text-base font-extrabold text-slate-900"><?php echo htmlspecialchars($b['airline']); ?></span>
                                    <span class="block text-xs text-slate-500 font-semibold">
                                        Flight #FL-<?php echo $b['flight_id']; ?> &bull; Ticket #TK-<?php echo $b['ticket_id']; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                    <?php echo $class_label; ?>
                                </span>
                                <span class="px-3 py-1 text-xs font-bold border <?php echo $status_badge; ?>">
                                    <?php echo htmlspecialchars($status_text); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Route & Time Schedule Visual Block -->
                        <div class="grid grid-cols-1 md:grid-cols-12 items-center gap-6 py-1">
                            
                            <!-- Departure City Info (4 cols) -->
                            <div class="md:col-span-4">
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Origin / Departure</span>
                                <span class="block text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                    <?php echo htmlspecialchars($b['source']); ?>
                                </span>
                                <span class="block text-xs font-semibold text-slate-600 mt-1">
                                    <i class="fa-regular fa-calendar mr-1 text-slate-400"></i><?php echo date('D, M d, Y', $dep_time); ?>
                                </span>
                                <span class="block text-base font-extrabold text-slate-900">
                                    <i class="fa-regular fa-clock mr-1 text-slate-400"></i><?php echo date('h:i A', $dep_time); ?>
                                </span>
                            </div>

                            <!-- Flight Trail Center Indicator (4 cols) -->
                            <div class="md:col-span-4 text-center px-4">
                                <div class="flex items-center justify-center gap-2 text-slate-300">
                                    <div class="h-[1px] flex-1 bg-slate-300"></div>
                                    <div class="w-8 h-8 bg-slate-50 border border-slate-200 flex items-center justify-center">
                                        <i class="fa-solid fa-plane <?php echo $flight_icon_color; ?> text-xs"></i>
                                    </div>
                                    <div class="h-[1px] flex-1 bg-slate-300"></div>
                                </div>
                                <span class="block text-xs font-bold text-slate-600 mt-1.5">
                                    <?php echo !empty($b['duration']) ? htmlspecialchars($b['duration']) : 'Non-Stop Direct'; ?>
                                </span>
                                <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                                    Status: <?php echo htmlspecialchars($status_text); ?>
                                </span>
                            </div>

                            <!-- Arrival Destination Info (4 cols) -->
                            <div class="md:col-span-4 md:text-right">
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Destination / Arrival</span>
                                <span class="block text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                                    <?php echo htmlspecialchars($b['Destination']); ?>
                                </span>
                                <span class="block text-xs font-semibold text-slate-600 mt-1">
                                    <i class="fa-regular fa-calendar mr-1 text-slate-400"></i><?php echo date('D, M d, Y', $arr_time); ?>
                                </span>
                                <span class="block text-base font-extrabold text-slate-900">
                                    <i class="fa-regular fa-clock mr-1 text-slate-400"></i><?php echo date('h:i A', $arr_time); ?>
                                </span>
                            </div>

                        </div>

                        <!-- Bottom Passenger Meta & Action Strip -->
                        <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                                <div>
                                    <span class="block text-[10px] font-bold uppercase text-slate-400">Passenger</span>
                                    <span class="font-extrabold text-slate-900 uppercase truncate block max-w-[130px]"><?php echo htmlspecialchars($full_name); ?></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold uppercase text-slate-400">Seat Number</span>
                                    <span class="font-extrabold text-sky-700 block"><?php echo htmlspecialchars($b['seat_no']); ?> (<?php echo $b['class']; ?>)</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold uppercase text-slate-400">Fare Paid</span>
                                    <span class="font-extrabold text-slate-900 block">$<?php echo htmlspecialchars($b['cost']); ?></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold uppercase text-slate-400">Contact</span>
                                    <span class="font-bold text-slate-700 block truncate"><?php echo htmlspecialchars($b['mobile'] ?? 'N/A'); ?></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <form action="e_ticket.php" target="_blank" method="POST" class="m-0">
                                    <input type="hidden" name="ticket_id" value="<?php echo $b['ticket_id']; ?>">
                                    <button type="submit" name="print_but" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold border border-sky-700 transition-colors flex items-center gap-1.5">
                                        <i class="fa-solid fa-print text-xs"></i>
                                        <span>Print Ticket</span>
                                    </button>
                                </form>
                                <a href="ticket.php" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-colors text-decoration-none">
                                    <span>Manage</span>
                                </a>
                            </div>

                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php subview('footer.php'); ?>
