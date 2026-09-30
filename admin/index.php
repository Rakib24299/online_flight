<?php
include_once 'header.php';
require '../helpers/init_conn_db.php';

// Auth check
if (!isset($_SESSION['adminId'])) {
    header('Location: login.php');
    exit();
}

$admin_uname = $_SESSION['adminUname'] ?? 'Admin';

// 1. Fetch Metric Counters directly
// Total Passengers
$passenger_count = 0;
$res_p = mysqli_query($conn, "SELECT COUNT(*) as total FROM passenger_profile");
if ($res_p && $row_p = mysqli_fetch_assoc($res_p)) {
    $passenger_count = (int)$row_p['total'];
}

// Total Revenue
$total_revenue = 0;
$res_rev = mysqli_query($conn, "SELECT SUM(cost) as total FROM ticket");
if ($res_rev && $row_rev = mysqli_fetch_assoc($res_rev)) {
    $total_revenue = (float)($row_rev['total'] ?? 0);
}

// Total Flights
$flight_count = 0;
$res_f = mysqli_query($conn, "SELECT COUNT(*) as total FROM flight");
if ($res_f && $row_f = mysqli_fetch_assoc($res_f)) {
    $flight_count = (int)$row_f['total'];
}

// Total Airlines
$airline_count = 0;
$res_a = mysqli_query($conn, "SELECT COUNT(*) as total FROM airline");
if ($res_a && $row_a = mysqli_fetch_assoc($res_a)) {
    $airline_count = (int)$row_a['total'];
}

// 2. Fetch all flights with stats
$all_flights = [];
$scheduled_flights = [];
$delayed_flights = [];
$departed_flights = [];
$arrived_flights = [];

$sql_flights = "SELECT * FROM flight ORDER BY flight_id DESC";
$res_all = mysqli_query($conn, $sql_flights);
if ($res_all) {
    while ($fl = mysqli_fetch_assoc($res_all)) {
        $all_flights[] = $fl;
        if ($fl['status'] === 'issue') {
            $delayed_flights[] = $fl;
        } elseif ($fl['status'] === 'dep') {
            $departed_flights[] = $fl;
        } elseif ($fl['status'] === 'arr') {
            $arrived_flights[] = $fl;
        } else {
            $scheduled_flights[] = $fl;
        }
    }
}
?>

<!-- Admin Dashboard Main (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Welcome Banner & Quick Action Buttons -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-amber-500/10 text-amber-800 border border-amber-300 mb-1.5">
                    <i class="fa-solid fa-shield-halved text-xs"></i>
                    <span>Flight Operations Console</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Welcome, <?php echo htmlspecialchars($admin_uname); ?>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Live flight operations control, route schedules, passenger manifests, and revenue monitoring.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="flight.php" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold border border-slate-900 flex items-center gap-2 transition-colors text-decoration-none">
                    <i class="fa-solid fa-plus text-amber-400"></i>
                    <span>Add New Flight</span>
                </a>
                <a href="list_airlines.php" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs sm:text-sm font-bold border border-amber-500 flex items-center gap-2 transition-colors text-decoration-none">
                    <i class="fa-solid fa-building"></i>
                    <span>Manage Airlines</span>
                </a>
            </div>
        </div>

        <!-- 4 Key Metric Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Passengers -->
            <div class="bg-white border border-slate-300 p-5 flex items-center justify-between">
                <div>
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Passengers</span>
                    <span class="block text-2xl sm:text-3xl font-black text-slate-900 mt-1"><?php echo number_format($passenger_count); ?></span>
                    <span class="block text-[11px] text-slate-400 font-semibold mt-0.5">Registered Profiles</span>
                </div>
                <div class="w-12 h-12 bg-sky-50 border border-sky-200 text-sky-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white border border-slate-300 p-5 flex items-center justify-between">
                <div>
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Revenue</span>
                    <span class="block text-2xl sm:text-3xl font-black text-emerald-600 mt-1">$<?php echo number_format($total_revenue, 2); ?></span>
                    <span class="block text-[11px] text-slate-400 font-semibold mt-0.5">Processed Bookings</span>
                </div>
                <div class="w-12 h-12 bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>

            <!-- Total Flights -->
            <div class="bg-white border border-slate-300 p-5 flex items-center justify-between">
                <div>
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Flights</span>
                    <span class="block text-2xl sm:text-3xl font-black text-slate-900 mt-1"><?php echo number_format($flight_count); ?></span>
                    <span class="block text-[11px] text-slate-400 font-semibold mt-0.5"><?php echo count($scheduled_flights); ?> Active / Scheduled</span>
                </div>
                <div class="w-12 h-12 bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-plane-up"></i>
                </div>
            </div>

            <!-- Available Airlines -->
            <div class="bg-white border border-slate-300 p-5 flex items-center justify-between">
                <div>
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Partner Airlines</span>
                    <span class="block text-2xl sm:text-3xl font-black text-slate-900 mt-1"><?php echo number_format($airline_count); ?></span>
                    <span class="block text-[11px] text-slate-400 font-semibold mt-0.5">Active Fleet Operators</span>
                </div>
                <div class="w-12 h-12 bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>

        </div>

        <!-- Flight Operations Table Section with Interactive Filter Tabs -->
        <div class="bg-white border border-slate-300">
            
            <!-- Table Header Strip -->
            <div class="p-5 sm:p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-tower-control text-amber-500"></i>
                        <span>Flight Status & Control Center</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Directly update flight status (Departed, Arrived, Delay Notice) and view passenger manifests</p>
                </div>

                <!-- Live Search Box -->
                <div class="relative w-full sm:w-64">
                    <input type="text" id="flightSearchInput" onkeyup="filterFlightRows()" placeholder="Search route, airline, ID..." class="w-full bg-slate-50 pl-9 pr-3 py-2 text-xs border border-slate-300 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-400"></i>
                </div>
            </div>

            <!-- Tab Segment Controls -->
            <div class="flex flex-wrap border-b border-slate-200 bg-slate-50 text-xs font-bold overflow-x-auto">
                <button onclick="switchFlightTab('all')" id="tab-btn-all" class="flight-tab-btn px-5 py-3 border-b-2 border-slate-900 bg-white text-slate-900 flex items-center gap-2 transition-colors">
                    <span>All Flights</span>
                    <span class="px-1.5 py-0.2 bg-slate-200 text-slate-700 text-[10px]"><?php echo count($all_flights); ?></span>
                </button>
                <button onclick="switchFlightTab('scheduled')" id="tab-btn-scheduled" class="flight-tab-btn px-5 py-3 border-b-2 border-transparent text-slate-600 hover:text-slate-900 flex items-center gap-2 transition-colors">
                    <span>Scheduled / On-Time</span>
                    <span class="px-1.5 py-0.2 bg-sky-100 text-sky-800 text-[10px]"><?php echo count($scheduled_flights); ?></span>
                </button>
                <button onclick="switchFlightTab('issue')" id="tab-btn-issue" class="flight-tab-btn px-5 py-3 border-b-2 border-transparent text-slate-600 hover:text-slate-900 flex items-center gap-2 transition-colors">
                    <span>Delayed / Issues</span>
                    <span class="px-1.5 py-0.2 bg-rose-100 text-rose-800 text-[10px]"><?php echo count($delayed_flights); ?></span>
                </button>
                <button onclick="switchFlightTab('dep')" id="tab-btn-dep" class="flight-tab-btn px-5 py-3 border-b-2 border-transparent text-slate-600 hover:text-slate-900 flex items-center gap-2 transition-colors">
                    <span>Departed</span>
                    <span class="px-1.5 py-0.2 bg-blue-100 text-blue-800 text-[10px]"><?php echo count($departed_flights); ?></span>
                </button>
                <button onclick="switchFlightTab('arr')" id="tab-btn-arr" class="flight-tab-btn px-5 py-3 border-b-2 border-transparent text-slate-600 hover:text-slate-900 flex items-center gap-2 transition-colors">
                    <span>Arrived</span>
                    <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[10px]"><?php echo count($arrived_flights); ?></span>
                </button>
            </div>

            <!-- Flight Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700" id="flightsTable">
                    <thead class="bg-slate-900 text-white uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4">Flight</th>
                            <th class="py-3.5 px-4">Airline</th>
                            <th class="py-3.5 px-4">Route (Origin &rarr; Dest)</th>
                            <th class="py-3.5 px-4">Departure</th>
                            <th class="py-3.5 px-4">Arrival</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Operational Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium">
                        <?php if (empty($all_flights)): ?>
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">
                                    <i class="fa-solid fa-plane-slash text-2xl block mb-2"></i>
                                    No flight records available in database.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($all_flights as $fl): 
                                $dep_ts = strtotime($fl['departure']);
                                $arr_ts = strtotime($fl['arrivale']);
                                $status_type = $fl['status'];
                                if (empty($status_type)) $status_type = 'scheduled';

                                // Badge text & style
                                if ($fl['status'] === 'issue') {
                                    $badge = 'bg-rose-100 text-rose-800 border-rose-300';
                                    $label = !empty($fl['issue']) ? 'Delayed (' . $fl['issue'] . 'm)' : 'Delayed';
                                } elseif ($fl['status'] === 'dep') {
                                    $badge = 'bg-blue-100 text-blue-800 border-blue-300';
                                    $label = 'In Flight / Departed';
                                } elseif ($fl['status'] === 'arr') {
                                    $badge = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                                    $label = 'Arrived / Completed';
                                } else {
                                    $badge = 'bg-sky-100 text-sky-800 border-sky-300';
                                    $label = 'On-Time / Scheduled';
                                }
                            ?>
                                <tr class="flight-row hover:bg-slate-50/80 transition-colors" data-status="<?php echo $status_type; ?>">
                                    
                                    <!-- Flight ID -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <a href="pass_list.php?flight_id=<?php echo $fl['flight_id']; ?>" class="font-mono font-bold text-sky-700 hover:text-sky-900 flex items-center gap-1.5 text-decoration-none">
                                            <i class="fa-solid fa-ticket-simple text-xs"></i>
                                            <span>#FL-<?php echo $fl['flight_id']; ?></span>
                                        </a>
                                    </td>

                                    <!-- Airline -->
                                    <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <?php echo htmlspecialchars($fl['airline']); ?>
                                    </td>

                                    <!-- Route -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 font-bold text-slate-800">
                                            <span><?php echo htmlspecialchars($fl['source']); ?></span>
                                            <i class="fa-solid fa-arrow-right text-[10px] text-slate-400"></i>
                                            <span><?php echo htmlspecialchars($fl['Destination']); ?></span>
                                        </div>
                                    </td>

                                    <!-- Departure -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="block text-slate-900 font-bold"><?php echo date('h:i A', $dep_ts); ?></span>
                                        <span class="block text-[10px] text-slate-500 font-semibold"><?php echo date('M d, Y', $dep_ts); ?></span>
                                    </td>

                                    <!-- Arrival -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="block text-slate-900 font-bold"><?php echo date('h:i A', $arr_ts); ?></span>
                                        <span class="block text-[10px] text-slate-500 font-semibold"><?php echo date('M d, Y', $arr_ts); ?></span>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-[11px] font-bold border <?php echo $badge; ?>">
                                            <?php echo htmlspecialchars($label); ?>
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            
                                            <!-- Manifest link -->
                                            <a href="pass_list.php?flight_id=<?php echo $fl['flight_id']; ?>" title="Passenger Manifest" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] border border-slate-300 transition-colors text-decoration-none">
                                                <i class="fa-solid fa-users mr-1"></i>Manifest
                                            </a>

                                            <!-- Scheduled Status Actions -->
                                            <?php if (empty($fl['status'])): ?>
                                                <!-- Depart Button -->
                                                <form action="../includes/admin/admin.inc.php" method="POST" class="m-0 inline">
                                                    <input type="hidden" name="flight_id" value="<?php echo $fl['flight_id']; ?>">
                                                    <button type="submit" name="dep_but" title="Mark as Departed" class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] border border-blue-700 transition-colors">
                                                        <i class="fa-solid fa-plane-departure mr-1"></i>Depart
                                                    </button>
                                                </form>

                                                <!-- Delay Modal Button -->
                                                <button type="button" onclick="openDelayModal('<?php echo $fl['flight_id']; ?>')" title="Report Delay" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-[11px] border border-amber-600 transition-colors">
                                                    <i class="fa-solid fa-clock mr-1"></i>Delay
                                                </button>

                                            <?php elseif ($fl['status'] === 'dep'): ?>
                                                <!-- Arrived Button -->
                                                <form action="../includes/admin/admin.inc.php" method="POST" class="m-0 inline">
                                                    <input type="hidden" name="flight_id" value="<?php echo $fl['flight_id']; ?>">
                                                    <button type="submit" name="arr_but" title="Mark as Arrived" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] border border-emerald-700 transition-colors">
                                                        <i class="fa-solid fa-plane-arrival mr-1"></i>Arrived
                                                    </button>
                                                </form>

                                            <?php elseif ($fl['status'] === 'issue'): ?>
                                                <!-- Issue Solved Button -->
                                                <form action="../includes/admin/admin.inc.php" method="POST" class="m-0 inline">
                                                    <input type="hidden" name="flight_id" value="<?php echo $fl['flight_id']; ?>">
                                                    <button type="submit" name="issue_soved_but" title="Resolve Issue" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] border border-emerald-700 transition-colors">
                                                        <i class="fa-solid fa-check mr-1"></i>Resolved
                                                    </button>
                                                </form>
                                                <form action="../includes/admin/admin.inc.php" method="POST" class="m-0 inline">
                                                    <input type="hidden" name="flight_id" value="<?php echo $fl['flight_id']; ?>">
                                                    <button type="submit" name="dep_but" title="Depart now" class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] border border-blue-700 transition-colors">
                                                        <i class="fa-solid fa-plane-departure mr-1"></i>Depart
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</main>

<!-- Delay Modal (Flat Sharp) -->
<div id="delayModal" class="hidden fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white border-2 border-slate-900 max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-amber-500"></i>
                <span>Report Flight Delay</span>
            </h3>
            <button type="button" onclick="closeDelayModal()" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="../includes/admin/admin.inc.php" method="POST" class="space-y-4">
            <input type="hidden" id="modalFlightId" name="flight_id" value="">
            
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Delay Duration (in minutes)</label>
                <input type="number" name="issue" required min="5" placeholder="e.g. 60" class="w-full bg-slate-50 p-2.5 border border-slate-300 text-sm text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none font-bold">
                <p class="text-[11px] text-slate-400 mt-1">This will automatically adjust scheduled departure and arrival timestamps.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                <button type="button" onclick="closeDelayModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-300">
                    Cancel
                </button>
                <button type="submit" name="issue_but" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold border border-amber-600">
                    Apply Delay
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tab switching logic
    function switchFlightTab(tab) {
        const buttons = document.querySelectorAll('.flight-tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('border-slate-900', 'bg-white', 'text-slate-900');
            btn.classList.add('border-transparent', 'text-slate-600');
        });

        const activeBtn = document.getElementById('tab-btn-' + tab);
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-slate-600');
            activeBtn.classList.add('border-slate-900', 'bg-white', 'text-slate-900');
        }

        const rows = document.querySelectorAll('.flight-row');
        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            if (tab === 'all') {
                row.style.display = '';
            } else if (tab === 'scheduled' && status === 'scheduled') {
                row.style.display = '';
            } else if (tab === 'issue' && status === 'issue') {
                row.style.display = '';
            } else if (tab === 'dep' && status === 'dep') {
                row.style.display = '';
            } else if (tab === 'arr' && status === 'arr') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Live search filter
    function filterFlightRows() {
        const input = document.getElementById('flightSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.flight-row');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (text.includes(input)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Delay modal handlers
    function openDelayModal(flightId) {
        document.getElementById('modalFlightId').value = flightId;
        document.getElementById('delayModal').classList.remove('hidden');
    }
    function closeDelayModal() {
        document.getElementById('delayModal').classList.add('hidden');
    }
</script>

<?php include_once 'footer.php'; ?>
