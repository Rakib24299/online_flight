<?php
include_once 'helpers/helper.php';
subview('header.php');
require 'config/db.php';

// Auth check
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}

// Ensure session booking parameters are available
if (!isset($_SESSION['flight_id']) || !isset($_SESSION['price'])) {
    header('Location: index.php');
    exit();
}

$flight_id = (int)$_SESSION['flight_id'];
$total_price = (float)$_SESSION['price'];
$passengers = (int)($_SESSION['passengers'] ?? 1);
$class = $_SESSION['class'] ?? 'E';
$type = $_SESSION['type'] ?? 'one';

// Fetch flight info for invoice
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

<!-- Payment Checkout Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Stepper Header -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 mb-1.5">
                    <i class="fa-solid fa-lock text-xs"></i>
                    <span>Booking Step 3 of 3 &bull; 256-Bit SSL Checkout</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Secure Payment Invoice
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Complete your transaction to instantly confirm seats and issue verified digital boarding passes.
                </p>
            </div>

            <!-- Stepper Indicator -->
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="px-3 py-1.5 bg-slate-100 text-slate-400 border border-slate-200">1. Flight</span>
                <span class="px-3 py-1.5 bg-slate-100 text-slate-400 border border-slate-200">2. Passengers</span>
                <span class="px-3 py-1.5 bg-slate-900 text-white border border-slate-900">3. Payment</span>
            </div>
        </div>

        <!-- Alert Notification -->
        <?php if (isset($_GET['error'])): ?>
            <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center gap-3">
                <div class="w-7 h-7 bg-rose-100 text-rose-700 flex items-center justify-center border border-rose-300 flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <div>
                    <?php
                    if ($_GET['error'] === 'sqlerror') {
                        echo '<strong>Transaction Error:</strong> Failed to record payment. Please try again.';
                    } else {
                        echo '<strong>Payment Error:</strong> Could not complete checkout. Please review card details.';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Split Grid: Invoice Breakdown (5 cols) & Card Form (7 cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left: Order Invoice Breakdown (5 cols) -->
            <div class="lg:col-span-5 bg-slate-900 text-white border border-slate-800 p-6 sm:p-8 flex flex-col justify-between space-y-6">
                <div>
                    <div class="border-b border-slate-800 pb-4 mb-5">
                        <span class="text-[10px] uppercase font-bold tracking-widest text-sky-400 block">Booking Invoice</span>
                        <h2 class="text-xl font-black text-white mt-1">Flight Fare Breakdown</h2>
                    </div>

                    <?php if ($flight): 
                        $dep_ts = strtotime($flight['departure']);
                        $arr_ts = strtotime($flight['arrivale']);
                    ?>
                        <div class="space-y-4 text-xs">
                            <div class="bg-slate-950 p-4 border border-slate-800 space-y-2.5">
                                <div class="flex justify-between items-center text-slate-400">
                                    <span>Operating Airline:</span>
                                    <strong class="text-white"><?php echo htmlspecialchars($flight['airline']); ?></strong>
                                </div>
                                <div class="flex justify-between items-center text-slate-400">
                                    <span>Flight Number:</span>
                                    <strong class="text-white font-mono">FL-<?php echo $flight['flight_id']; ?></strong>
                                </div>
                                <div class="flex justify-between items-center text-slate-400">
                                    <span>Route:</span>
                                    <strong class="text-white"><?php echo htmlspecialchars($flight['source']); ?> &rarr; <?php echo htmlspecialchars($flight['Destination']); ?></strong>
                                </div>
                                <div class="flex justify-between items-center text-slate-400">
                                    <span>Departure:</span>
                                    <strong class="text-white"><?php echo date('M d, Y &bull; h:i A', $dep_ts); ?></strong>
                                </div>
                                <div class="flex justify-between items-center text-slate-400">
                                    <span>Class / Passengers:</span>
                                    <strong class="text-white"><?php echo ($class === 'B') ? 'Business' : 'Economy'; ?> &bull; <?php echo $passengers; ?> Seat(s)</strong>
                                </div>
                            </div>

                            <!-- Cost Items -->
                            <div class="space-y-2 pt-2 text-slate-300">
                                <div class="flex justify-between">
                                    <span>Base Airfare:</span>
                                    <span>$<?php echo number_format($total_price * 0.85, 2); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Aviation Taxes & Fees:</span>
                                    <span>$<?php echo number_format($total_price * 0.15, 2); ?></span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-800 flex justify-between items-center">
                                <span class="text-sm font-bold text-slate-300">Total Payable:</span>
                                <span class="text-2xl font-black text-sky-400">$<?php echo number_format($total_price, 2); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Trust Guarantee Badge -->
                <div class="pt-6 border-t border-slate-800 text-[11px] text-slate-400 flex items-center gap-2.5">
                    <i class="fa-solid fa-shield-halved text-emerald-400 text-base"></i>
                    <span>Encrypted checkout. Your payment card information is strictly processed securely.</span>
                </div>
            </div>

            <!-- Right: Payment Card Form (7 cols) -->
            <div class="lg:col-span-7 bg-white border border-slate-300 p-6 sm:p-8 space-y-6">
                
                <div class="border-b border-slate-200 pb-4">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Payment Card Details</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Credit or Debit Card Transaction</p>
                        </div>
                        <div class="flex items-center gap-2 text-xl text-slate-600">
                            <i class="fa-brands fa-cc-visa text-blue-700"></i>
                            <i class="fa-brands fa-cc-mastercard text-rose-600"></i>
                            <i class="fa-brands fa-cc-amex text-sky-600"></i>
                            <i class="fa-brands fa-cc-discover text-amber-500"></i>
                        </div>
                    </div>
                </div>

                <form action="includes/payment.inc.php" method="POST" id="checkoutPaymentForm" class="space-y-4">
                    
                    <!-- Card Number -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                            Card Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="cc-number" name="cc-number" required maxlength="19" placeholder="4111 2222 3333 4444" class="w-full bg-slate-50 p-3 pl-10 rounded-none border border-slate-300 text-slate-900 text-sm font-mono font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                            <i class="fa-solid fa-credit-card absolute left-3 top-3.5 text-slate-400 text-sm"></i>
                        </div>
                        <span class="block text-[10px] text-slate-400 mt-1">Accepts any 12 to 16 digit card number</span>
                    </div>

                    <!-- Expiry & CVV Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Expiration -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                Expiration (MM/YY) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="cc-exp" name="cc-exp" required maxlength="5" placeholder="12/28" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm font-mono font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                        </div>

                        <!-- CVV -->
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">
                                Security CVV <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="x_card_code" name="x_card_code" required maxlength="4" placeholder="123" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm font-mono font-bold focus:bg-white focus:border-slate-900 focus:outline-none transition-colors">
                                <i class="fa-solid fa-lock absolute right-3 top-3.5 text-slate-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pay Now Submit Button -->
                    <div class="pt-4">
                        <button type="submit" id="payment-button" name="pay_but" class="w-full py-4 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm uppercase tracking-wider border border-emerald-700 transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span>Authorize & Pay $<?php echo number_format($total_price, 2); ?></span>
                        </button>
                    </div>

                </form>

                <p class="text-[11px] text-slate-400 text-center leading-relaxed">
                    By clicking Authorize & Pay, you agree to SkyWings flight carriage terms, boarding rules, and fare conditions.
                </p>

            </div>

        </div>

    </div>
</main>

<script>
    // Format card number with spaces automatically
    document.getElementById('cc-number').addEventListener('input', function(e) {
        let val = e.target.value.replace(/\D/g, '');
        let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
        e.target.value = formatted.substring(0, 19);
    });

    // Format MM/YY
    document.getElementById('cc-exp').addEventListener('input', function(e) {
        let val = e.target.value.replace(/\D/g, '');
        if (val.length >= 2) {
            e.target.value = val.substring(0, 2) + '/' + val.substring(2, 4);
        } else {
            e.target.value = val;
        }
    });
</script>

<?php subview('footer.php'); ?>