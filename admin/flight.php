<?php
include_once 'header.php';
require '../helpers/init_conn_db.php';

// Auth check
if (!isset($_SESSION['adminId'])) {
    header('Location: login.php');
    exit();
}

// Fetch cities and airlines for dropdowns
$cities = [];
$res_c = mysqli_query($conn, "SELECT city FROM cities ORDER BY city ASC");
if ($res_c) {
    while ($rc = mysqli_fetch_assoc($res_c)) {
        $cities[] = $rc['city'];
    }
}

$airlines = [];
$res_a = mysqli_query($conn, "SELECT airline_id, name, seats FROM airline ORDER BY name ASC");
if ($res_a) {
    while ($ra = mysqli_fetch_assoc($res_a)) {
        $airlines[] = $ra;
    }
}
?>

<!-- Add Flight Form Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Page Header -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-amber-500/10 text-amber-800 border border-amber-300 mb-1.5">
                    <i class="fa-solid fa-plane-up text-xs"></i>
                    <span>Flight Operations</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Schedule New Flight Route
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Enter departure schedule, destination arrival, airline fleet selection, and seat fare pricing.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="all_flights.php" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold border border-slate-900 flex items-center gap-1.5 transition-colors text-decoration-none">
                    <i class="fa-solid fa-list-check text-xs"></i>
                    <span>All Flight Records</span>
                </a>
            </div>
        </div>

        <!-- Alert Banners -->
        <?php if (isset($_GET['flight']) && $_GET['flight'] === 'success'): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold flex items-center gap-3">
                <div class="w-7 h-7 bg-emerald-100 text-emerald-700 flex items-center justify-center border border-emerald-300 flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
                <div>
                    <strong>Success!</strong> The new flight route has been successfully scheduled and published to the booking system.
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-3">
                <div class="w-7 h-7 bg-rose-100 text-rose-700 flex items-center justify-center border border-rose-300 flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <div>
                    <?php
                    if ($_GET['error'] === 'destless') {
                        echo '<strong>Invalid Timestamps:</strong> Destination arrival time must be after the origin departure time.';
                    } elseif ($_GET['error'] === 'same') {
                        echo '<strong>City Conflict:</strong> Origin and destination cities cannot be identical.';
                    } elseif ($_GET['error'] === 'sqlerr' || $_GET['error'] === 'sqlerr1') {
                        echo '<strong>Database Error:</strong> Failed to save flight record. Please check database connection.';
                    } else {
                        echo '<strong>Error:</strong> Failed to create flight. Please check form inputs.';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Flight Creation Form Card -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8">
            <form action="../includes/admin/flight.inc.php" method="POST" class="space-y-6">

                <!-- Section 1: Route & Cities -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-2 border-b border-slate-200 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-route text-sky-600"></i>
                        <span>Flight Route</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Origin City -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                Origin (From City) <span class="text-rose-500">*</span>
                            </label>
                            <select name="dep_city" required class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-slate-900 focus:outline-none transition-colors font-medium">
                                <option value="" disabled selected>Select origin city</option>
                                <?php foreach ($cities as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Destination City -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                Destination (To City) <span class="text-rose-500">*</span>
                            </label>
                            <select name="arr_city" required class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-slate-900 focus:outline-none transition-colors font-medium">
                                <option value="" disabled selected>Select destination city</option>
                                <?php foreach ($cities as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Departure & Arrival Timestamps -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-2 border-b border-slate-200 mb-4 flex items-center gap-2">
                        <i class="fa-regular fa-clock text-amber-500"></i>
                        <span>Schedule Timestamps</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Departure Schedule -->
                        <div class="bg-slate-50 border border-slate-200 p-4 space-y-3">
                            <span class="block text-xs font-bold uppercase text-sky-700">
                                <i class="fa-solid fa-plane-departure mr-1"></i> Departure Schedule
                            </span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Date</label>
                                    <input type="date" name="source_date" required class="w-full bg-white p-2.5 rounded-none border border-slate-300 text-xs font-bold text-slate-900 focus:border-slate-900 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Time</label>
                                    <input type="time" name="source_time" required class="w-full bg-white p-2.5 rounded-none border border-slate-300 text-xs font-bold text-slate-900 focus:border-slate-900 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Arrival Schedule -->
                        <div class="bg-slate-50 border border-slate-200 p-4 space-y-3">
                            <span class="block text-xs font-bold uppercase text-emerald-700">
                                <i class="fa-solid fa-plane-arrival mr-1"></i> Arrival Schedule
                            </span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Date</label>
                                    <input type="date" name="dest_date" required class="w-full bg-white p-2.5 rounded-none border border-slate-300 text-xs font-bold text-slate-900 focus:border-slate-900 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Time</label>
                                    <input type="time" name="dest_time" required class="w-full bg-white p-2.5 rounded-none border border-slate-300 text-xs font-bold text-slate-900 focus:border-slate-900 focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Airline Fleet & Pricing -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 pb-2 border-b border-slate-200 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-tags text-indigo-600"></i>
                        <span>Fleet & Fare Configuration</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Airline Selector -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                Operating Airline <span class="text-rose-500">*</span>
                            </label>
                            <select name="airline_name" required class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-slate-900 focus:outline-none transition-colors font-medium">
                                <option value="" disabled selected>Select Airline</option>
                                <?php foreach ($airlines as $a): ?>
                                    <option value="<?php echo $a['airline_id']; ?>">
                                        <?php echo htmlspecialchars($a['name']); ?> (<?php echo $a['seats']; ?> seats)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Duration -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                Est. Flight Duration <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="dura" required placeholder="e.g. 2 hrs 30 mins" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-slate-900 focus:outline-none transition-colors font-medium">
                        </div>

                        <!-- Base Price -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                Base Fare (USD $) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="price" required min="1" step="1" placeholder="e.g. 250" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-slate-900 focus:outline-none transition-colors font-medium">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                    <a href="index.php" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 transition-colors text-decoration-none">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Cancel
                    </a>
                    <button type="submit" name="flight_but" class="px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm border border-slate-900 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-plus text-amber-400"></i>
                        <span>Publish Flight Schedule</span>
                    </button>
                </div>

            </form>
        </div>

    </div>
</main>

<?php include_once 'footer.php'; ?>
