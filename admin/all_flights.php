<?php
include_once 'header.php';
require '../helpers/init_conn_db.php';

// Auth check
if (!isset($_SESSION['adminId'])) {
    header('Location: login.php');
    exit();
}

// Handle Delete Flight
$del_msg = null;
if (isset($_POST['del_flight'])) {
    $flight_id = (int)$_POST['flight_id'];
    $stmt = mysqli_stmt_init($conn);
    $sql = 'DELETE FROM flight WHERE flight_id=?';
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $flight_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $del_msg = 'Flight record #FL-' . $flight_id . ' deleted successfully.';
    }
}

// Fetch all flights
$flights = [];
$sql = 'SELECT * FROM flight ORDER BY flight_id DESC';
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($rf = mysqli_fetch_assoc($res)) {
        $flights[] = $rf;
    }
}
?>

<!-- All Flights Master Catalog (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Page Header -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-amber-500/10 text-amber-800 border border-amber-300 mb-1.5">
                    <i class="fa-solid fa-plane-tail text-xs"></i>
                    <span>Flight Master Database</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    All Scheduled Flight Records
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Master list of all current, departed, completed, and archived flight schedules.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="flight.php" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold border border-slate-900 flex items-center gap-2 transition-colors text-decoration-none">
                    <i class="fa-solid fa-plus text-amber-400"></i>
                    <span>Add New Flight</span>
                </a>
            </div>
        </div>

        <!-- Delete Notification Alert -->
        <?php if ($del_msg): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span><?php echo htmlspecialchars($del_msg); ?></span>
            </div>
        <?php endif; ?>

        <!-- Flight Master Table Container -->
        <div class="bg-white border border-slate-300">
            
            <!-- Table Action Toolbar -->
            <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-sky-600"></i>
                        <span>Flight Schedule Catalog</span>
                    </h3>
                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 text-xs font-bold border border-slate-300">
                        Total: <?php echo count($flights); ?>
                    </span>
                </div>

                <!-- Live Search Box -->
                <div class="relative w-full sm:w-64">
                    <input type="text" id="masterFlightSearch" onkeyup="filterMasterFlights()" placeholder="Filter by ID, airline, city..." class="w-full bg-slate-50 pl-8 pr-3 py-2 text-xs border border-slate-300 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-xs text-slate-400"></i>
                </div>
            </div>

            <!-- Flights Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700" id="allFlightsTable">
                    <thead class="bg-slate-900 text-white uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4">Flight</th>
                            <th class="py-3.5 px-4">Airline</th>
                            <th class="py-3.5 px-4">Route (Origin &rarr; Dest)</th>
                            <th class="py-3.5 px-4">Departure</th>
                            <th class="py-3.5 px-4">Arrival</th>
                            <th class="py-3.5 px-4 text-center">Seats</th>
                            <th class="py-3.5 px-4 text-center">Fare</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium">
                        <?php if (empty($flights)): ?>
                            <tr>
                                <td colspan="9" class="py-10 text-center text-slate-400">
                                    <i class="fa-solid fa-plane-slash text-2xl block mb-2"></i>
                                    No flight records available in database.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($flights as $f): 
                                $dep_t = strtotime($f['departure']);
                                $arr_t = strtotime($f['arrivale']);

                                // Status Badge styling
                                if ($f['status'] === 'issue') {
                                    $badge = 'bg-rose-100 text-rose-800 border-rose-300';
                                    $label = !empty($f['issue']) ? 'Delayed (' . $f['issue'] . 'm)' : 'Delayed';
                                } elseif ($f['status'] === 'dep') {
                                    $badge = 'bg-blue-100 text-blue-800 border-blue-300';
                                    $label = 'Departed';
                                } elseif ($f['status'] === 'arr') {
                                    $badge = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                                    $label = 'Arrived';
                                } else {
                                    $badge = 'bg-sky-100 text-sky-800 border-sky-300';
                                    $label = 'Scheduled';
                                }
                            ?>
                                <tr class="master-flight-row hover:bg-slate-50/80 transition-colors">
                                    
                                    <!-- Flight ID -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <a href="pass_list.php?flight_id=<?php echo $f['flight_id']; ?>" class="font-mono font-bold text-sky-700 hover:text-sky-900 flex items-center gap-1.5 text-decoration-none">
                                            <i class="fa-solid fa-ticket-simple text-xs"></i>
                                            <span>#FL-<?php echo $f['flight_id']; ?></span>
                                        </a>
                                    </td>

                                    <!-- Airline -->
                                    <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <?php echo htmlspecialchars($f['airline']); ?>
                                    </td>

                                    <!-- Route -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 font-bold text-slate-800">
                                            <span><?php echo htmlspecialchars($f['source']); ?></span>
                                            <i class="fa-solid fa-arrow-right text-[10px] text-slate-400"></i>
                                            <span><?php echo htmlspecialchars($f['Destination']); ?></span>
                                        </div>
                                    </td>

                                    <!-- Departure -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="block text-slate-900 font-bold"><?php echo date('h:i A', $dep_t); ?></span>
                                        <span class="block text-[10px] text-slate-500 font-semibold"><?php echo date('M d, Y', $dep_t); ?></span>
                                    </td>

                                    <!-- Arrival -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="block text-slate-900 font-bold"><?php echo date('h:i A', $arr_t); ?></span>
                                        <span class="block text-[10px] text-slate-500 font-semibold"><?php echo date('M d, Y', $arr_t); ?></span>
                                    </td>

                                    <!-- Seats -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="font-bold text-slate-700">
                                            <?php echo htmlspecialchars($f['Seats']); ?>
                                        </span>
                                    </td>

                                    <!-- Price -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap font-extrabold text-slate-900">
                                        $<?php echo htmlspecialchars($f['Price']); ?>
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
                                            <!-- Manifest -->
                                            <a href="pass_list.php?flight_id=<?php echo $f['flight_id']; ?>" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 transition-colors text-decoration-none">
                                                <i class="fa-solid fa-users mr-1"></i>Manifest
                                            </a>

                                            <!-- Delete -->
                                            <form action="all_flights.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete Flight #FL-<?php echo $f['flight_id']; ?>?');" class="m-0 inline">
                                                <input type="hidden" name="flight_id" value="<?php echo $f['flight_id']; ?>">
                                                <button type="submit" name="del_flight" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 transition-colors font-bold text-xs">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
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

<script>
    function filterMasterFlights() {
        const input = document.getElementById('masterFlightSearch').value.toLowerCase();
        const rows = document.querySelectorAll('.master-flight-row');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (text.includes(input)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<?php include_once 'footer.php'; ?>
