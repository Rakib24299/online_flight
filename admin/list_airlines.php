<?php
include_once 'header.php';
require '../helpers/init_conn_db.php';

// Auth check
if (!isset($_SESSION['adminId'])) {
    header('Location: login.php');
    exit();
}

// Handle Delete Airline
$del_msg = null;
if (isset($_POST['del_airlines'])) {
    $airline_id = (int)$_POST['airline_id'];
    $stmt = mysqli_stmt_init($conn);
    $sql = 'DELETE FROM airline WHERE airline_id=?';
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $airline_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $del_msg = 'Airline fleet removed successfully.';
    }
}

// Handle Add Airline
$add_msg = null;
$add_error = null;
if (isset($_POST['add_airline_but'])) {
    $air_name = trim($_POST['airline_name']);
    $seats = (int)$_POST['seats'];
    if (!empty($air_name) && $seats > 0) {
        $stmt_add = mysqli_stmt_init($conn);
        $sql_add = 'INSERT INTO airline (name, seats) VALUES (?, ?)';
        if (mysqli_stmt_prepare($stmt_add, $sql_add)) {
            mysqli_stmt_bind_param($stmt_add, 'si', $air_name, $seats);
            mysqli_stmt_execute($stmt_add);
            mysqli_stmt_close($stmt_add);
            $add_msg = 'New airline operator registered successfully!';
        } else {
            $add_error = 'Database error adding airline.';
        }
    } else {
        $add_error = 'Please provide valid airline name and seat capacity.';
    }
}

// Fetch all airlines with active flights count
$airlines_list = [];
$sql_list = "SELECT a.airline_id, a.name, a.seats, COUNT(f.flight_id) as total_flights
             FROM airline a
             LEFT JOIN flight f ON a.name = f.airline
             GROUP BY a.airline_id, a.name, a.seats
             ORDER BY a.airline_id ASC";
$res_list = mysqli_query($conn, $sql_list);
if ($res_list) {
    while ($ra = mysqli_fetch_assoc($res_list)) {
        $airlines_list[] = $ra;
    }
}
?>

<!-- Airlines Management Main (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Page Header -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-amber-500/10 text-amber-800 border border-amber-300 mb-1.5">
                    <i class="fa-solid fa-building text-xs"></i>
                    <span>Fleet Operations</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Airline Operators & Fleet Capacity
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Manage partner airline fleets, aircraft seat configurations, and view linked flight operations.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="flight.php" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold border border-slate-900 flex items-center gap-2 transition-colors text-decoration-none">
                    <i class="fa-solid fa-plus text-amber-400"></i>
                    <span>Add New Flight</span>
                </a>
            </div>
        </div>

        <!-- Success / Error Alert Notifications -->
        <?php if ($del_msg): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span><?php echo htmlspecialchars($del_msg); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($add_msg): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span><?php echo htmlspecialchars($add_msg); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($add_error): ?>
            <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span><?php echo htmlspecialchars($add_error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Split Grid: Add Form (4 cols) & Fleet Table (8 cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left: Add New Airline Card (4 cols) -->
            <div class="lg:col-span-4 bg-white border border-slate-300 p-6 space-y-4 h-fit">
                <div class="border-b border-slate-200 pb-3">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-square-plus text-amber-500"></i>
                        <span>Register New Airline</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Add an airline brand and its default seating capacity</p>
                </div>

                <form action="list_airlines.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                            Airline Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="airline_name" required placeholder="e.g. Qatar Airways" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-slate-900 focus:outline-none font-medium transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                            Seat Capacity <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="seats" required min="10" max="850" placeholder="e.g. 180" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-slate-900 focus:outline-none font-medium transition-colors">
                    </div>

                    <div class="pt-2">
                        <button type="submit" name="add_airline_but" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider border border-slate-900 transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-plus text-amber-400"></i>
                            <span>Save Airline Fleet</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Airlines Fleet Table (8 cols) -->
            <div class="lg:col-span-8 bg-white border border-slate-300">
                
                <!-- Table Header & Search -->
                <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-plane-tail text-sky-600"></i>
                            <span>Active Airline Fleets</span>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-bold border border-slate-300 ml-1"><?php echo count($airlines_list); ?></span>
                        </h3>
                    </div>

                    <div class="relative w-full sm:w-56">
                        <input type="text" id="airlineSearchInput" onkeyup="filterAirlineRows()" placeholder="Search airline..." class="w-full bg-slate-50 pl-8 pr-3 py-1.5 text-xs border border-slate-300 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2 text-[11px] text-slate-400"></i>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700" id="airlinesTable">
                        <thead class="bg-slate-900 text-white uppercase text-[10px] tracking-wider font-bold">
                            <tr>
                                <th class="py-3 px-4">#</th>
                                <th class="py-3 px-4">Airline Operator</th>
                                <th class="py-3 px-4 text-center">Seat Capacity</th>
                                <th class="py-3 px-4 text-center">Scheduled Flights</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-medium">
                            <?php if (empty($airlines_list)): ?>
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        No airlines registered yet.
                                    </td>
                                </tr>
                            <?php else: 
                                $cnt = 1;
                                foreach ($airlines_list as $al): ?>
                                    <tr class="airline-row hover:bg-slate-50/80 transition-colors">
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-400">
                                            <?php echo $cnt++; ?>
                                        </td>
                                        <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 text-xs">
                                                    <i class="fa-solid fa-plane"></i>
                                                </div>
                                                <span><?php echo htmlspecialchars($al['name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="px-2.5 py-1 bg-sky-50 text-sky-800 font-bold border border-sky-200">
                                                <?php echo htmlspecialchars($al['seats']); ?> Seats
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="font-extrabold text-slate-800">
                                                <?php echo $al['total_flights']; ?> Routes
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <form action="list_airlines.php" method="POST" onsubmit="return confirm('Are you sure you want to delete airline: <?php echo htmlspecialchars($al['name']); ?>?');" class="m-0 inline">
                                                <input type="hidden" name="airline_id" value="<?php echo $al['airline_id']; ?>">
                                                <button type="submit" name="del_airlines" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 transition-colors font-bold text-xs flex items-center gap-1 ml-auto">
                                                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>
</main>

<script>
    function filterAirlineRows() {
        const input = document.getElementById('airlineSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.airline-row');
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
