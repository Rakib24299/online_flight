<?php
include_once 'helpers/helper.php';
subview('header.php');
require 'config/db.php';

// Auth check
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}

// Check booking parameters
if (!isset($_POST['book_but']) && !isset($_SESSION['flight_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = (int)$_SESSION['userId'];
$flight_id = isset($_POST['flight_id']) ? (int)$_POST['flight_id'] : (int)($_SESSION['flight_id'] ?? 0);
$passengers = isset($_POST['passengers']) ? (int)$_POST['passengers'] : (int)($_SESSION['passengers'] ?? 1);
$price = $_POST['price'] ?? ($_SESSION['price'] ?? 0);
$class = $_POST['class'] ?? ($_SESSION['class'] ?? 'E');
$type = $_POST['type'] ?? ($_SESSION['type'] ?? 'one');
$ret_date = $_POST['ret_date'] ?? ($_SESSION['ret_date'] ?? 'NULL');

// Fetch flight info for summary
$flight = null;
$stmt_f = mysqli_stmt_init($conn);
$sql_f = 'SELECT * FROM flight WHERE flight_id=?';
if (mysqli_stmt_prepare($stmt_f, $sql_f)) {
    mysqli_stmt_bind_param($stmt_f, 'i', $flight_id);
    mysqli_stmt_execute($stmt_f);
    $res_f = mysqli_stmt_get_result($stmt_f);
    $flight = mysqli_fetch_assoc($res_f);
}
?>

<!-- Passenger Form Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Header / Stepper Bar -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300 mb-1.5">
                    <i class="fa-solid fa-users text-xs"></i>
                    <span>Booking Step 2 of 3</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Passenger Details Manifest
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Please provide full legal names and travel contact information for each passenger.
                </p>
            </div>

            <!-- Booking Stepper Indicator -->
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="px-3 py-1.5 bg-slate-100 text-slate-600 border border-slate-300">1. Flight</span>
                <span class="px-3 py-1.5 bg-slate-900 text-white border border-slate-900">2. Passengers</span>
                <span class="px-3 py-1.5 bg-slate-100 text-slate-400 border border-slate-200">3. Payment</span>
            </div>
        </div>

        <!-- Alert Notifications -->
        <?php if (isset($_GET['error'])): ?>
            <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-3">
                <div class="w-7 h-7 bg-rose-100 text-rose-700 flex items-center justify-center border border-rose-300 flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <div>
                    <?php
                    if ($_GET['error'] === 'moblen') {
                        echo '<strong>Invalid Contact Number:</strong> Mobile numbers must be exactly 11 digits (e.g. 01712345678).';
                    } elseif ($_GET['error'] === 'invdate') {
                        echo '<strong>Invalid Date of Birth:</strong> Date of birth cannot be today or in the future.';
                    } elseif ($_GET['error'] === 'sqlerror') {
                        echo '<strong>Database Error:</strong> System could not register passenger records. Please try again.';
                    } else {
                        echo '<strong>Error:</strong> Please verify all required passenger fields.';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Split Grid: Flight Summary & Passenger Forms -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left: Flight Reservation Summary Card (4 cols) -->
            <?php if ($flight): 
                $dep_t = strtotime($flight['departure']);
                $arr_t = strtotime($flight['arrivale']);
            ?>
                <div class="lg:col-span-4 bg-slate-900 text-white border border-slate-800 p-6 space-y-5 h-fit">
                    <div class="border-b border-slate-800 pb-3">
                        <span class="text-[10px] uppercase font-bold tracking-widest text-sky-400 block">Flight Summary</span>
                        <h3 class="text-lg font-extrabold text-white mt-0.5"><?php echo htmlspecialchars($flight['airline']); ?></h3>
                        <span class="text-xs text-slate-400 font-mono">FL-<?php echo $flight['flight_id']; ?> &bull; <?php echo ($class === 'B') ? 'Business Class' : 'Economy Class'; ?></span>
                    </div>

                    <!-- Route details -->
                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] font-bold uppercase text-slate-500 block">Origin</span>
                                <strong class="text-sm font-extrabold text-white block"><?php echo htmlspecialchars($flight['source']); ?></strong>
                                <span class="text-slate-400 text-[11px]"><?php echo date('M d, Y &bull; h:i A', $dep_t); ?></span>
                            </div>
                            <i class="fa-solid fa-plane text-sky-500 mt-2"></i>
                            <div class="text-right">
                                <span class="text-[10px] font-bold uppercase text-slate-500 block">Destination</span>
                                <strong class="text-sm font-extrabold text-white block"><?php echo htmlspecialchars($flight['Destination']); ?></strong>
                                <span class="text-slate-400 text-[11px]"><?php echo date('M d, Y &bull; h:i A', $arr_t); ?></span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 space-y-2">
                            <div class="flex justify-between text-slate-400">
                                <span>Duration:</span>
                                <span class="text-white font-bold"><?php echo !empty($flight['duration']) ? htmlspecialchars($flight['duration']) : 'Direct'; ?></span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span>Passengers:</span>
                                <span class="text-white font-bold"><?php echo $passengers; ?> Person(s)</span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span>Trip Type:</span>
                                <span class="text-white font-bold"><?php echo ($type === 'round') ? 'Round Trip' : 'One Way'; ?></span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center text-sm">
                            <span class="font-bold text-slate-300">Total Amount:</span>
                            <span class="text-xl font-black text-sky-400">$<?php echo number_format((float)$price, 2); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Right: Dynamic Passenger Forms (8 cols) -->
            <div class="<?php echo $flight ? 'lg:col-span-8' : 'lg:col-span-12'; ?>">
                <form action="includes/pass_detail.inc.php" method="POST" class="space-y-6">
                    <input type="hidden" name="userId" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                    <input type="hidden" name="ret_date" value="<?php echo htmlspecialchars($ret_date); ?>">
                    <input type="hidden" name="class" value="<?php echo htmlspecialchars($class); ?>">
                    <input type="hidden" name="passengers" value="<?php echo $passengers; ?>">
                    <input type="hidden" name="price" value="<?php echo $price; ?>">
                    <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">

                    <?php for ($i = 1; $i <= $passengers; $i++): ?>
                        <div class="bg-white border border-slate-300 p-6 space-y-4">
                            
                            <!-- Card Header -->
                            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                                    <div class="w-6 h-6 bg-slate-900 text-white flex items-center justify-center text-xs font-mono">
                                        <?php echo $i; ?>
                                    </div>
                                    <span>Passenger #<?php echo $i; ?> Information</span>
                                </h3>
                                <span class="text-[11px] font-bold text-slate-400 uppercase">Seat & Manifest Entry</span>
                            </div>

                            <!-- Names Grid (3 cols) -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                        First Name <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="firstname[]" required placeholder="e.g. John" class="w-full bg-slate-50 p-2.5 rounded-none border border-slate-300 text-slate-900 text-xs font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                        Middle Name
                                    </label>
                                    <input type="text" name="midname[]" placeholder="Optional" class="w-full bg-slate-50 p-2.5 rounded-none border border-slate-300 text-slate-900 text-xs font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                        Last Name <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="lastname[]" required placeholder="e.g. Doe" class="w-full bg-slate-50 p-2.5 rounded-none border border-slate-300 text-slate-900 text-xs font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                                </div>
                            </div>

                            <!-- Contact & DOB Grid (2 cols) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                        Contact Number (11 digits) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="tel" name="mobile[]" required pattern="[0-9]{11}" placeholder="01712345678" class="w-full bg-slate-50 p-2.5 rounded-none border border-slate-300 text-slate-900 text-xs font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                                    <span class="block text-[10px] text-slate-400 mt-1">Must be exactly 11 numeric digits</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                        Date of Birth <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" name="date[]" required max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>" class="w-full bg-slate-50 p-2.5 rounded-none border border-slate-300 text-slate-900 text-xs font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                                </div>
                            </div>

                        </div>
                    <?php endfor; ?>

                    <!-- Proceed to Checkout Button -->
                    <div class="flex items-center justify-between pt-2">
                        <a href="book.php" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 transition-colors text-decoration-none">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Flights
                        </a>
                        <button type="submit" name="pass_but" class="px-8 py-3.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm uppercase tracking-wider border border-sky-700 transition-colors flex items-center gap-2">
                            <span>Proceed to Payment</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>

                </form>
            </div>

        </div>

    </div>
</main>

<?php subview('footer.php'); ?>
