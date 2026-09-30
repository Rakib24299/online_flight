<?php
include_once 'helpers/helper.php';
subview('header.php');
require 'config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}

$user_id = (int)$_SESSION['userId'];
$username = $_SESSION['userUid'] ?? 'Passenger';
$usermail = $_SESSION['userMail'] ?? '';

// Fetch stats for logged-in user
$total_tickets = 0;
$upcoming_trips = 0;
$past_trips = 0;

$count_sql = "SELECT 
    COUNT(t.ticket_id) AS total_count,
    SUM(CASE WHEN f.departure >= NOW() THEN 1 ELSE 0 END) AS upcoming_count,
    SUM(CASE WHEN f.departure < NOW() THEN 1 ELSE 0 END) AS past_count
    FROM ticket t 
    JOIN flight f ON t.flight_id = f.flight_id 
    WHERE t.user_id = $user_id";

$count_res = mysqli_query($conn, $count_sql);
if ($count_res && $row = mysqli_fetch_assoc($count_res)) {
    $total_tickets = (int)$row['total_count'];
    $upcoming_trips = (int)($row['upcoming_count'] ?? 0);
    $past_trips = (int)($row['past_count'] ?? 0);
}

// Fetch user's recent bookings
$recent_bookings = [];
$booking_sql = "SELECT t.ticket_id, t.seat_no, t.cost, t.class, 
                       f.flight_id, f.source, f.Destination, f.airline, f.departure, f.arrivale, f.status
                FROM ticket t
                JOIN flight f ON t.flight_id = f.flight_id
                WHERE t.user_id = $user_id
                ORDER BY t.ticket_id DESC
                LIMIT 5";
$booking_res = mysqli_query($conn, $booking_sql);
if ($booking_res) {
    while ($b_row = mysqli_fetch_assoc($booking_res)) {
        $recent_bookings[] = $b_row;
    }
}

// Fetch cities for quick flight search
$cities = [];
$c_res = mysqli_query($conn, "SELECT DISTINCT city FROM cities ORDER BY city ASC");
if ($c_res) {
    while ($c = mysqli_fetch_assoc($c_res)) {
        $cities[] = $c['city'];
    }
}
$today = date('Y-m-d');
?>

<!-- User Portal Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Welcome Hero Header -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-sky-600 text-white flex items-center justify-center text-2xl font-bold border border-sky-700">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-0.5 text-xs font-semibold bg-sky-100 text-sky-800 border border-sky-300 mb-1">
                        <i class="fa-solid fa-passport text-xs"></i>
                        <span>Passenger Portal</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Welcome back, <?php echo htmlspecialchars($username); ?>!
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        <i class="fa-solid fa-envelope mr-1 text-slate-400"></i><?php echo htmlspecialchars($usermail); ?> &bull; Passenger ID: #<?php echo $user_id; ?>
                    </p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="booking_history.php" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold border border-slate-900 flex items-center gap-2 transition-colors text-decoration-none">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Booking History</span>
                </a>
                <a href="ticket.php" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs sm:text-sm font-bold border border-sky-700 flex items-center gap-2 transition-colors text-decoration-none">
                    <i class="fa-solid fa-ticket"></i>
                    <span>My E-Tickets</span>
                </a>
            </div>
        </div>

        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Stat 1: Total Flights -->
            <div class="bg-white border border-slate-300 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Bookings</span>
                    <div class="w-8 h-8 bg-sky-100 text-sky-700 flex items-center justify-center border border-sky-200">
                        <i class="fa-solid fa-plane"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900"><?php echo $total_tickets; ?></span>
                    <span class="text-xs text-slate-500">Tickets Issued</span>
                </div>
            </div>

            <!-- Stat 2: Upcoming Trips -->
            <div class="bg-white border border-slate-300 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Upcoming Trips</span>
                    <div class="w-8 h-8 bg-emerald-100 text-emerald-700 flex items-center justify-center border border-emerald-200">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-emerald-600"><?php echo $upcoming_trips; ?></span>
                    <span class="text-xs text-slate-500">Active Schedules</span>
                </div>
            </div>

            <!-- Stat 3: Completed Flights -->
            <div class="bg-white border border-slate-300 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Completed Flights</span>
                    <div class="w-8 h-8 bg-indigo-100 text-indigo-700 flex items-center justify-center border border-indigo-200">
                        <i class="fa-solid fa-plane-arrival"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-indigo-600"><?php echo $past_trips; ?></span>
                    <span class="text-xs text-slate-500">Past Journeys</span>
                </div>
            </div>

            <!-- Stat 4: Account Status -->
            <div class="bg-white border border-slate-300 p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Membership Tier</span>
                    <div class="w-8 h-8 bg-amber-100 text-amber-700 flex items-center justify-center border border-amber-200">
                        <i class="fa-solid fa-medal"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-xl font-extrabold text-slate-900">Standard</span>
                    <span class="text-xs px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold border border-emerald-200">Active</span>
                </div>
            </div>

        </div>

        <!-- Quick Flight Search Widget for Logged-in User -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass text-sky-600 text-base"></i>
                        <span>Book a New Flight</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Search real-time airline schedules and reserve your seats instantly</p>
                </div>
                <span class="text-xs px-3 py-1 bg-slate-100 border border-slate-300 font-bold text-slate-700">Quick Booking</span>
            </div>

            <form action="book.php" method="POST" class="space-y-4">
                <input type="hidden" name="type" value="one">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    
                    <!-- From -->
                    <div class="bg-slate-50 p-3 border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-plane-departure text-sky-600 mr-1"></i> From (Departure)
                        </label>
                        <select name="dep_city" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none cursor-pointer text-sm">
                            <option value="0" disabled selected>Select Departure</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- To -->
                    <div class="bg-slate-50 p-3 border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-plane-arrival text-emerald-600 mr-1"></i> To (Destination)
                        </label>
                        <select name="arr_city" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none cursor-pointer text-sm">
                            <option value="0" disabled selected>Select Destination</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="bg-slate-50 p-3 border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-calendar-days text-sky-600 mr-1"></i> Travel Date
                        </label>
                        <input type="date" name="dep_date" min="<?php echo $today; ?>" value="<?php echo $today; ?>" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none text-sm cursor-pointer">
                    </div>

                    <!-- Class -->
                    <div class="bg-slate-50 p-3 border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-couch text-sky-600 mr-1"></i> Cabin Class
                        </label>
                        <select name="f_class" class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none cursor-pointer text-sm">
                            <option value="E" selected>Economy Class</option>
                            <option value="B">Business Class</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-end">
                        <input type="hidden" name="passengers" value="1">
                        <button type="submit" name="search_but" class="w-full py-3 px-4 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm border border-sky-700 transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Search Flights</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <!-- Recent Booking Table -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-sky-600 text-base"></i>
                        <span>Recent Flight Bookings</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Review your latest flight reservations and boarding details</p>
                </div>
                <a href="booking_history.php" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                    <span>View All</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <?php if (empty($recent_bookings)): ?>
                <div class="text-center py-12 border border-dashed border-slate-300 bg-slate-50">
                    <div class="w-12 h-12 mx-auto bg-slate-200 text-slate-500 flex items-center justify-center text-xl mb-3">
                        <i class="fa-solid fa-ticket-simple"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">No Bookings Yet</h3>
                    <p class="text-xs text-slate-500 mt-1">You haven't booked any flights so far. Use the search bar above to book your first flight!</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 text-xs uppercase tracking-wider border-b border-slate-300">
                                <th class="p-3.5 font-bold">Ticket #</th>
                                <th class="p-3.5 font-bold">Airline & Flight</th>
                                <th class="p-3.5 font-bold">Route</th>
                                <th class="p-3.5 font-bold">Departure</th>
                                <th class="p-3.5 font-bold">Seat & Class</th>
                                <th class="p-3.5 font-bold">Status</th>
                                <th class="p-3.5 font-bold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <?php foreach ($recent_bookings as $b): 
                                $is_upcoming = strtotime($b['departure']) >= time();
                            ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-3.5 font-extrabold text-sky-700">
                                        #TK-<?php echo $b['ticket_id']; ?>
                                    </td>
                                    <td class="p-3.5 font-semibold text-slate-900">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-plane text-xs text-sky-600"></i>
                                            <span><?php echo htmlspecialchars($b['airline']); ?></span>
                                        </div>
                                    </td>
                                    <td class="p-3.5 font-medium text-slate-700">
                                        <?php echo htmlspecialchars($b['source']); ?> 
                                        <i class="fa-solid fa-arrow-right text-[10px] text-slate-400 mx-1"></i> 
                                        <?php echo htmlspecialchars($b['Destination']); ?>
                                    </td>
                                    <td class="p-3.5 text-xs text-slate-600">
                                        <div class="font-semibold text-slate-800"><?php echo date('M d, Y', strtotime($b['departure'])); ?></div>
                                        <div><?php echo date('h:i A', strtotime($b['departure'])); ?></div>
                                    </td>
                                    <td class="p-3.5 text-xs font-semibold text-slate-800">
                                        Seat: <span class="text-sky-600 font-bold"><?php echo htmlspecialchars($b['seat_no']); ?></span>
                                        <span class="ml-1 text-[11px] text-slate-500">(<?php echo $b['class'] === 'B' ? 'Business' : 'Economy'; ?>)</span>
                                    </td>
                                    <td class="p-3.5">
                                        <?php if ($is_upcoming): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <i class="fa-solid fa-circle-dot text-[8px]"></i> Confirmed
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                                Completed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3.5 text-right">
                                        <a href="ticket.php" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold border border-slate-900 transition-colors text-decoration-none">
                                            <i class="fa-solid fa-ticket text-xs"></i>
                                            <span>View Ticket</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php subview('footer.php'); ?>
