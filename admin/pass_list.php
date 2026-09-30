<?php
include_once 'header.php';
require '../helpers/init_conn_db.php';

// Auth check
if (!isset($_SESSION['adminId'])) {
    header('Location: login.php');
    exit();
}

$flight_id = isset($_GET['flight_id']) ? (int)$_GET['flight_id'] : 0;

// Fetch flight details
$flight = null;
$stmt_f = mysqli_stmt_init($conn);
$sql_f = 'SELECT * FROM flight WHERE flight_id=?';
if (mysqli_stmt_prepare($stmt_f, $sql_f)) {
    mysqli_stmt_bind_param($stmt_f, 'i', $flight_id);
    mysqli_stmt_execute($stmt_f);
    $res_f = mysqli_stmt_get_result($stmt_f);
    $flight = mysqli_fetch_assoc($res_f);
}

// Fetch all passenger manifest records for this flight
$manifest = [];
if ($flight_id > 0) {
    $sql_m = "SELECT t.ticket_id, t.seat_no, t.cost, t.class,
                     p.passenger_id, p.f_name, p.m_name, p.l_name, p.mobile, p.dob,
                     u.username, u.email
              FROM ticket t
              LEFT JOIN passenger_profile p ON t.passenger_id = p.passenger_id
              LEFT JOIN users u ON t.user_id = u.user_id
              WHERE t.flight_id = ?
              ORDER BY t.ticket_id ASC";
    $stmt_m = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt_m, $sql_m)) {
        mysqli_stmt_bind_param($stmt_m, 'i', $flight_id);
        mysqli_stmt_execute($stmt_m);
        $res_m = mysqli_stmt_get_result($stmt_m);
        while ($row = mysqli_fetch_assoc($res_m)) {
            $manifest[] = $row;
        }
    }
}
?>

<!-- Passenger Manifest Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Page Header -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-amber-500/10 text-amber-800 border border-amber-300 mb-1.5">
                    <i class="fa-solid fa-users text-xs"></i>
                    <span>Passenger Manifest Log</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Flight #FL-<?php echo $flight_id; ?> Passenger Manifest
                </h1>
                <?php if ($flight): 
                    $dep_t = strtotime($flight['departure']);
                    $arr_t = strtotime($flight['arrivale']);
                ?>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        <strong><?php echo htmlspecialchars($flight['airline']); ?></strong> &bull;
                        <span><?php echo htmlspecialchars($flight['source']); ?> &rarr; <?php echo htmlspecialchars($flight['Destination']); ?></span> &bull;
                        <span>Dep: <?php echo date('M d, Y h:i A', $dep_t); ?></span>
                    </p>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-2">
                <a href="all_flights.php" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold border border-slate-300 flex items-center gap-1.5 transition-colors text-decoration-none">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>All Flights</span>
                </a>
                <button onclick="window.print();" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold border border-slate-900 flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-print text-amber-400 text-xs"></i>
                    <span>Print Manifest</span>
                </button>
            </div>
        </div>

        <!-- Manifest Table Container -->
        <div class="bg-white border border-slate-300">
            
            <!-- Toolbar -->
            <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-900">
                        Booked Passengers
                    </h3>
                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 text-xs font-bold border border-slate-300">
                        Total: <?php echo count($manifest); ?>
                    </span>
                </div>

                <!-- Live Search Box -->
                <div class="relative w-full sm:w-64">
                    <input type="text" id="manifestSearch" onkeyup="filterManifestRows()" placeholder="Search passenger name, mobile, seat..." class="w-full bg-slate-50 pl-8 pr-3 py-1.5 text-xs border border-slate-300 text-slate-900 focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2 text-xs text-slate-400"></i>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700" id="manifestTable">
                    <thead class="bg-slate-900 text-white uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-4">#</th>
                            <th class="py-3.5 px-4">Ticket ID</th>
                            <th class="py-3.5 px-4">Passenger Name</th>
                            <th class="py-3.5 px-4">Seat / Class</th>
                            <th class="py-3.5 px-4">Contact Mobile</th>
                            <th class="py-3.5 px-4">Date of Birth</th>
                            <th class="py-3.5 px-4">Account User</th>
                            <th class="py-3.5 px-4 text-right">Fare Paid</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium">
                        <?php if (empty($manifest)): ?>
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400">
                                    <i class="fa-solid fa-users-slash text-2xl block mb-2"></i>
                                    No passenger bookings found for this flight schedule yet.
                                </td>
                            </tr>
                        <?php else: 
                            $cnt = 1;
                            foreach ($manifest as $m): 
                                $full_name = trim($m['f_name'] . ' ' . $m['m_name'] . ' ' . $m['l_name']);
                                if (empty($full_name)) $full_name = 'Passenger';
                                $class_badge = ($m['class'] === 'B') ? 'bg-indigo-50 text-indigo-800 border-indigo-200' : 'bg-sky-50 text-sky-800 border-sky-200';
                            ?>
                                <tr class="manifest-row hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4 font-mono text-slate-400 font-bold"><?php echo $cnt++; ?></td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-sky-700">#TK-<?php echo $m['ticket_id']; ?></td>
                                    <td class="py-3.5 px-4 font-extrabold text-slate-900 uppercase">
                                        <?php echo htmlspecialchars($full_name); ?>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 font-bold text-[11px] border <?php echo $class_badge; ?>">
                                            Seat <?php echo htmlspecialchars($m['seat_no']); ?> (<?php echo $m['class']; ?>)
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                        <?php echo htmlspecialchars($m['mobile'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        <?php echo !empty($m['dob']) ? date('M d, Y', strtotime($m['dob'])) : 'N/A'; ?>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="block font-bold text-slate-900"><?php echo htmlspecialchars($m['username'] ?? 'User'); ?></span>
                                        <span class="block text-[10px] text-slate-400"><?php echo htmlspecialchars($m['email'] ?? ''); ?></span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-extrabold text-slate-900">
                                        $<?php echo number_format((float)$m['cost'], 2); ?>
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
    function filterManifestRows() {
        const input = document.getElementById('manifestSearch').value.toLowerCase();
        const rows = document.querySelectorAll('.manifest-row');
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
