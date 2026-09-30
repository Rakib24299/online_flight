<?php
include_once 'helpers/helper.php';
subview('header.php');
require 'config/db.php';

// Fetch cities for search fallback
$cities = [];
$res_c = mysqli_query($conn, "SELECT city FROM cities ORDER BY city ASC");
if ($res_c) {
    while ($rc = mysqli_fetch_assoc($res_c)) {
        $cities[] = $rc['city'];
    }
}

$is_search = isset($_POST['search_but']);
$dep_city = $_POST['dep_city'] ?? '';
$arr_city = $_POST['arr_city'] ?? '';
$dep_date = $_POST['dep_date'] ?? '';
$ret_date = $_POST['ret_date'] ?? 'NULL';
$type = $_POST['type'] ?? 'one';
$f_class = $_POST['f_class'] ?? 'E';
$passengers = isset($_POST['passengers']) ? (int)$_POST['passengers'] : 1;
if ($passengers < 1) $passengers = 1;

// Validate cities
if ($is_search) {
    if ($dep_city === $arr_city) {
        header('Location: index.php?error=sameval');
        exit();
    }
    if ($dep_city === '0' || empty($dep_city)) {
        header('Location: index.php?error=seldep');
        exit();
    }
    if ($arr_city === '0' || empty($arr_city)) {
        header('Location: index.php?error=selarr');
        exit();
    }
}

// Query flights
$flights = [];
if ($is_search) {
    $sql = 'SELECT * FROM flight WHERE source=? AND Destination=? AND DATE(departure)=? ORDER BY Price ASC';
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 'sss', $dep_city, $arr_city, $dep_date);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $flights[] = $row;
        }
    }
} else {
    // If accessed directly, show all upcoming available flights
    $sql = "SELECT * FROM flight WHERE status='' OR status='dep' ORDER BY departure ASC LIMIT 20";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $flights[] = $row;
        }
    }
}
?>

<!-- Flight Search & Results Main (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Search Summary / Header Bar -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300 mb-1.5">
                    <i class="fa-solid fa-plane-departure text-xs"></i>
                    <span>Flight Availability</span>
                </div>

                <?php if ($is_search): ?>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex flex-wrap items-center gap-2">
                        <span><?php echo htmlspecialchars($dep_city); ?></span>
                        <i class="fa-solid fa-arrow-right text-sm text-slate-400"></i>
                        <span><?php echo htmlspecialchars($arr_city); ?></span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        <i class="fa-regular fa-calendar mr-1"></i>Departure: <strong class="text-slate-700"><?php echo date('D, M d, Y', strtotime($dep_date)); ?></strong>
                        &bull; Passengers: <strong class="text-slate-700"><?php echo $passengers; ?></strong>
                        &bull; Class: <strong class="text-slate-700"><?php echo ($f_class === 'B') ? 'Business' : 'Economy'; ?></strong>
                        &bull; Trip: <strong class="text-slate-700"><?php echo ($type === 'round') ? 'Round Trip' : 'One Way'; ?></strong>
                    </p>
                <?php else: ?>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Available Flight Schedules
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Select an available flight route below or customize your search from the homepage.
                    </p>
                <?php endif; ?>
            </div>

            <div>
                <a href="index.php" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold border border-slate-900 flex items-center gap-2 transition-colors text-decoration-none">
                    <i class="fa-solid fa-magnifying-glass text-sky-400"></i>
                    <span>Modify Search</span>
                </a>
            </div>
        </div>

        <!-- Flight Results List -->
        <?php if (empty($flights)): ?>
            <!-- No Flights Found -->
            <div class="bg-white border border-slate-300 p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 border border-slate-200">
                    <i class="fa-solid fa-plane-slash text-2xl"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-800">No Direct Flights Found</h2>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-6">
                    We couldn't find any scheduled flights matching your exact route on this date. Try selecting another date or destination.
                </p>
                <a href="index.php" class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold border border-sky-700 text-decoration-none transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Search Other Dates</span>
                </a>
            </div>
        <?php else: ?>

            <div class="space-y-4">
                <?php foreach ($flights as $row): 
                    $dep_ts = strtotime($row['departure']);
                    $arr_ts = strtotime($row['arrivale']);
                    
                    // Price calculation
                    $calculated_price = (float)$row['Price'] * $passengers;
                    if ($type === 'round') {
                        $calculated_price = $calculated_price * 2;
                    }
                    if ($f_class === 'B') {
                        $calculated_price += 0.5 * $calculated_price;
                    }

                    // Status
                    $status_badge = "bg-sky-100 text-sky-800 border-sky-300";
                    $status_label = "On-Time / Scheduled";
                    if ($row['status'] === 'dep') {
                        $status_badge = "bg-blue-100 text-blue-800 border-blue-300";
                        $status_label = "Departed / In Flight";
                    } elseif ($row['status'] === 'issue') {
                        $status_badge = "bg-rose-100 text-rose-800 border-rose-300";
                        $status_label = !empty($row['issue']) ? "Delayed (" . $row['issue'] . "m)" : "Delayed";
                    } elseif ($row['status'] === 'arr') {
                        $status_badge = "bg-emerald-100 text-emerald-800 border-emerald-300";
                        $status_label = "Arrived";
                    }
                ?>
                    <!-- Individual Flight Result Card -->
                    <div class="bg-white border border-slate-300 p-6 sm:p-7 hover:border-slate-400 transition-colors">
                        <div class="grid grid-cols-1 lg:grid-cols-12 items-center gap-6">

                            <!-- Airline & Flight Code (3 cols) -->
                            <div class="lg:col-span-3 flex items-center gap-3.5">
                                <div class="w-12 h-12 bg-slate-900 text-white flex items-center justify-center font-bold text-base border border-slate-800 flex-shrink-0">
                                    <i class="fa-solid fa-plane-departure text-sky-400"></i>
                                </div>
                                <div>
                                    <span class="block text-base font-extrabold text-slate-900"><?php echo htmlspecialchars($row['airline']); ?></span>
                                    <span class="block text-xs text-slate-500 font-semibold font-mono">Flight #FL-<?php echo $row['flight_id']; ?></span>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold border <?php echo $status_badge; ?>">
                                        <?php echo $status_label; ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Route Visual Schedule (6 cols) -->
                            <div class="lg:col-span-6 grid grid-cols-5 items-center text-center">
                                
                                <!-- Departure -->
                                <div class="col-span-2 text-left">
                                    <span class="block text-xl sm:text-2xl font-black text-slate-900 leading-none">
                                        <?php echo date('h:i A', $dep_ts); ?>
                                    </span>
                                    <span class="block text-xs font-extrabold text-slate-700 mt-1">
                                        <?php echo htmlspecialchars($row['source']); ?>
                                    </span>
                                    <span class="block text-[10px] text-slate-400 font-semibold">
                                        <?php echo date('D, M d', $dep_ts); ?>
                                    </span>
                                </div>

                                <!-- Trail Indicator -->
                                <div class="col-span-1 flex flex-col items-center gap-0.5">
                                    <span class="text-[10px] font-bold text-slate-400">
                                        <?php echo !empty($row['duration']) ? htmlspecialchars($row['duration']) : 'Non-Stop'; ?>
                                    </span>
                                    <div class="w-full flex items-center justify-center gap-1 text-slate-300">
                                        <div class="h-[1px] w-full bg-slate-300"></div>
                                        <i class="fa-solid fa-plane text-xs text-sky-600"></i>
                                        <div class="h-[1px] w-full bg-slate-300"></div>
                                    </div>
                                    <span class="text-[9px] uppercase font-bold text-sky-600">Direct</span>
                                </div>

                                <!-- Destination -->
                                <div class="col-span-2 text-right">
                                    <span class="block text-xl sm:text-2xl font-black text-slate-900 leading-none">
                                        <?php echo date('h:i A', $arr_ts); ?>
                                    </span>
                                    <span class="block text-xs font-extrabold text-slate-700 mt-1">
                                        <?php echo htmlspecialchars($row['Destination']); ?>
                                    </span>
                                    <span class="block text-[10px] text-slate-400 font-semibold">
                                        <?php echo date('D, M d', $arr_ts); ?>
                                    </span>
                                </div>

                            </div>

                            <!-- Fare & Book Action (3 cols) -->
                            <div class="lg:col-span-3 flex lg:flex-col items-center lg:items-end justify-between gap-3 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-200">
                                <div class="text-left lg:text-right">
                                    <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Total Fare</span>
                                    <span class="block text-2xl font-black text-slate-900 leading-none">
                                        $<?php echo number_format($calculated_price, 2); ?>
                                    </span>
                                    <span class="block text-[10px] text-slate-500 font-semibold mt-0.5">
                                        Includes taxes & fees
                                    </span>
                                </div>

                                <div>
                                    <?php if (isset($_SESSION['userId'])): ?>
                                        <?php if ($row['status'] === ''): ?>
                                            <form action="passengers.php" method="POST" class="m-0">
                                                <input type="hidden" name="flight_id" value="<?php echo $row['flight_id']; ?>">
                                                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                                                <input type="hidden" name="passengers" value="<?php echo $passengers; ?>">
                                                <input type="hidden" name="price" value="<?php echo $calculated_price; ?>">
                                                <input type="hidden" name="ret_date" value="<?php echo htmlspecialchars($ret_date); ?>">
                                                <input type="hidden" name="class" value="<?php echo htmlspecialchars($f_class); ?>">
                                                <button type="submit" name="book_but" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs uppercase tracking-wider border border-sky-700 transition-colors flex items-center gap-1.5">
                                                    <span>Select Flight</span>
                                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                                </button>
                                            </form>
                                        <?php elseif ($row['status'] === 'dep'): ?>
                                            <span class="px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-bold border border-slate-200 block text-center">
                                                Departed
                                            </span>
                                        <?php else: ?>
                                            <span class="px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-bold border border-slate-200 block text-center">
                                                Unavailable
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="login.php" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs border border-slate-900 transition-colors text-decoration-none flex items-center gap-1.5">
                                            <i class="fa-solid fa-lock text-[10px] text-amber-400"></i>
                                            <span>Login to Book</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php subview('footer.php'); ?>