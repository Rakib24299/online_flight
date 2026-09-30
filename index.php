<?php
include_once 'helpers/helper.php';
subview('header.php');
require 'config/db.php';

// Fetch available cities
$cities = [];
$sql = "SELECT DISTINCT city FROM cities ORDER BY city ASC";
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $cities[] = $row['city'];
    }
}
$today = date('Y-m-d');
?>

<!-- Alerts & Notification Messages (rounded-none, shadow-none, subtle border) -->
<?php if (isset($_GET['error'])): ?>
    <div class="max-w-5xl mx-auto px-4 mt-6">
        <div class="flex items-center gap-3 p-4 rounded-none bg-rose-50 border border-rose-300 text-rose-900">
            <div class="w-8 h-8 rounded-none bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 border border-rose-200">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
            </div>
            <div class="text-sm font-medium">
                <?php
                if ($_GET['error'] === 'sameval') {
                    echo '<strong>Departure & Arrival error:</strong> Departure city and arrival city cannot be the same. Please choose different destinations.';
                } elseif ($_GET['error'] === 'seldep') {
                    echo '<strong>Missing City:</strong> Please select a departure city to continue.';
                } elseif ($_GET['error'] === 'selarr') {
                    echo '<strong>Missing City:</strong> Please select an arrival destination city to continue.';
                } else {
                    echo 'An error occurred while processing your request. Please try again.';
                }
                ?>
            </div>
        </div>
    </div>
<?php elseif (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
    <div class="max-w-5xl mx-auto px-4 mt-6">
        <div class="flex items-center gap-3 p-4 rounded-none bg-emerald-50 border border-emerald-300 text-emerald-900">
            <div class="w-8 h-8 rounded-none bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0 border border-emerald-200">
                <i class="fa-solid fa-circle-check text-base"></i>
            </div>
            <div class="text-sm font-medium">
                Welcome back! You have successfully signed in.
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['userId'])): ?>
    <div class="max-w-5xl mx-auto px-4 mt-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 rounded-none bg-sky-950/60 border border-sky-600 text-sky-200">
            <div class="flex items-center gap-2 text-sm font-semibold">
                <i class="fa-solid fa-circle-user text-sky-400"></i>
                <span>You are signed in as <strong><?php echo htmlspecialchars($_SESSION['userUid'] ?? 'Passenger'); ?></strong></span>
            </div>
            <a href="dashboard.php" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold border border-sky-400/40 text-decoration-none transition-colors flex items-center gap-2">
                <span>Go to Passenger Dashboard</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Hero & Search Section (Clean, Flat, Crisp Modernist Design) -->
<section class="relative bg-slate-900 text-white pt-12 pb-24 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
    <div class="max-w-6xl mx-auto relative z-10">
        
        <!-- Hero Title -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-none text-xs font-semibold bg-sky-950/60 text-sky-400 border border-sky-800/80 mb-4">
                <i class="fa-solid fa-plane-up text-sky-400"></i>
                <span>Fast & Simple Flight Reservations</span>
            </div>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white mb-4 leading-tight">
                Where will your journey <br class="hidden sm:inline">
                <span class="text-sky-400">take you today?</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-300 leading-relaxed">
                Discover the best flight deals across top destinations worldwide with guaranteed transparent fares, instant e-tickets, and 24/7 dedicated support.
            </p>
        </div>

        <!-- Flight Search Widget Card (rounded-none, shadow-none, subtle border) -->
        <div class="bg-white rounded-none border border-slate-300 p-6 sm:p-8 text-slate-800">
            
            <!-- Trip Type Toggle Tabs -->
            <div class="flex items-center border-b border-slate-200 mb-6">
                <button type="button" id="tabRoundBtn" onclick="switchTripTab('round')" class="flex items-center gap-2 px-6 py-3 rounded-none font-bold text-sm transition-colors border-b-2 border-sky-600 text-sky-600 bg-sky-50/50">
                    <i class="fa-solid fa-arrow-right-arrow-left text-xs"></i>
                    <span>Round Trip</span>
                </button>
                <button type="button" id="tabOneWayBtn" onclick="switchTripTab('one')" class="flex items-center gap-2 px-6 py-3 rounded-none font-bold text-sm text-slate-600 hover:text-slate-900 transition-colors border-b-2 border-transparent">
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                    <span>One Way</span>
                </button>
            </div>

            <!-- ROUND TRIP FORM -->
            <form id="roundTripForm" action="book.php" method="POST" class="space-y-6">
                <input type="hidden" name="type" value="round">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- From (Departure) -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-plane-departure text-sky-600 mr-1"></i> From (Departure)
                        </label>
                        <select name="dep_city" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none cursor-pointer text-sm sm:text-base">
                            <option value="0" disabled selected>Select Departure City</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- To (Arrival) -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-plane-arrival text-emerald-600 mr-1"></i> To (Destination)
                        </label>
                        <select name="arr_city" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none cursor-pointer text-sm sm:text-base">
                            <option value="0" disabled selected>Select Arrival City</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Departure Date -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-calendar-days text-sky-600 mr-1"></i> Departure Date
                        </label>
                        <input type="date" name="dep_date" min="<?php echo $today; ?>" value="<?php echo $today; ?>" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none text-sm sm:text-base cursor-pointer">
                    </div>

                    <!-- Return Date -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-calendar-check text-indigo-600 mr-1"></i> Return Date
                        </label>
                        <input type="date" name="ret_date" min="<?php echo $today; ?>" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none text-sm sm:text-base cursor-pointer">
                    </div>

                </div>

                <!-- Secondary Row: Class, Passengers & Submit Button -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                    
                    <!-- Cabin Class -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-couch text-sky-600 mr-1"></i> Cabin Class
                        </label>
                        <select name="f_class" class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none cursor-pointer text-sm sm:text-base">
                            <option value="E" selected>Economy Class</option>
                            <option value="B">Business Class</option>
                        </select>
                    </div>

                    <!-- Passenger Counter -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 transition-colors flex items-center justify-between">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                <i class="fa-solid fa-users text-sky-600 mr-1"></i> Passengers
                            </span>
                            <span class="text-sm font-semibold text-slate-900">Number of Seats</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="adjustPassengers('round', -1)" class="w-8 h-8 rounded-none bg-slate-200 hover:bg-slate-300 text-slate-800 flex items-center justify-center font-bold text-sm border border-slate-300 transition-colors">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <span id="roundPassengerDisplay" class="font-bold text-base text-slate-900 w-6 text-center">1</span>
                            <input type="hidden" id="roundPassengerInput" name="passengers" value="1">
                            <button type="button" onclick="adjustPassengers('round', 1)" class="w-8 h-8 rounded-none bg-sky-600 hover:bg-sky-700 text-white flex items-center justify-center font-bold text-sm border border-sky-700 transition-colors">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Search Flights Submit Button -->
                    <div class="flex items-end">
                        <button type="submit" name="search_but" class="w-full py-3.5 px-6 rounded-none bg-sky-600 hover:bg-sky-700 text-white font-bold text-base border border-sky-700 transition-colors flex items-center justify-center gap-2">
                            <span>Search Flights</span>
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </button>
                    </div>

                </div>
            </form>

            <!-- ONE WAY FORM (Hidden by default) -->
            <form id="oneWayForm" action="book.php" method="POST" class="hidden space-y-6">
                <input type="hidden" name="type" value="one">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <!-- From (Departure) -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-plane-departure text-sky-600 mr-1"></i> From (Departure)
                        </label>
                        <select name="dep_city" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none cursor-pointer text-sm sm:text-base">
                            <option value="0" disabled selected>Select Departure City</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- To (Arrival) -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-plane-arrival text-emerald-600 mr-1"></i> To (Destination)
                        </label>
                        <select name="arr_city" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none cursor-pointer text-sm sm:text-base">
                            <option value="0" disabled selected>Select Arrival City</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Departure Date -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-calendar-days text-sky-600 mr-1"></i> Departure Date
                        </label>
                        <input type="date" name="dep_date" min="<?php echo $today; ?>" value="<?php echo $today; ?>" required class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none text-sm sm:text-base cursor-pointer">
                    </div>

                </div>

                <!-- Secondary Row: Class, Passengers & Submit Button -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                    
                    <!-- Cabin Class -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 focus-within:border-sky-600 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-couch text-sky-600 mr-1"></i> Cabin Class
                        </label>
                        <select name="f_class" class="w-full bg-transparent font-semibold text-slate-900 focus:outline-none cursor-pointer text-sm sm:text-base">
                            <option value="E" selected>Economy Class</option>
                            <option value="B">Business Class</option>
                        </select>
                    </div>

                    <!-- Passenger Counter -->
                    <div class="bg-slate-50 p-3.5 rounded-none border border-slate-300 transition-colors flex items-center justify-between">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                <i class="fa-solid fa-users text-sky-600 mr-1"></i> Passengers
                            </span>
                            <span class="text-sm font-semibold text-slate-900">Number of Seats</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="adjustPassengers('one', -1)" class="w-8 h-8 rounded-none bg-slate-200 hover:bg-slate-300 text-slate-800 flex items-center justify-center font-bold text-sm border border-slate-300 transition-colors">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <span id="onePassengerDisplay" class="font-bold text-base text-slate-900 w-6 text-center">1</span>
                            <input type="hidden" id="onePassengerInput" name="passengers" value="1">
                            <button type="button" onclick="adjustPassengers('one', 1)" class="w-8 h-8 rounded-none bg-sky-600 hover:bg-sky-700 text-white flex items-center justify-center font-bold text-sm border border-sky-700 transition-colors">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Search Flights Submit Button -->
                    <div class="flex items-end">
                        <button type="submit" name="search_but" class="w-full py-3.5 px-6 rounded-none bg-sky-600 hover:bg-sky-700 text-white font-bold text-base border border-sky-700 transition-colors flex items-center justify-center gap-2">
                            <span>Search Flights</span>
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</section>

<!-- Why Choose Us / Value Proposition Section (rounded-none, shadow-none, subtle border) -->
<section class="py-20 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-sky-600 font-bold uppercase tracking-wider text-xs">Why Fly With Us</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 tracking-tight">
                Designed for Seamless Aviation
            </h2>
            <p class="text-slate-600 mt-3 text-base">
                We make your trip planning simple, reliable, and affordable from takeoff to touchdown.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Feature Card 1 -->
            <div class="p-8 rounded-none bg-slate-50 border border-slate-200 hover:border-sky-500 transition-colors">
                <div class="w-14 h-14 rounded-none bg-sky-100 border border-sky-200 flex items-center justify-center mb-6">
                    <img src="assets/images/beach.svg" alt="Top Destinations" class="w-8 h-8">
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Top Global Destinations</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Access dozens of premier routes and connected cities with top-rated international and domestic airline partners.
                </p>
            </div>

            <!-- Feature Card 2 -->
            <div class="p-8 rounded-none bg-slate-50 border border-slate-200 hover:border-emerald-500 transition-colors">
                <div class="w-14 h-14 rounded-none bg-emerald-100 border border-emerald-200 flex items-center justify-center mb-6">
                    <img src="assets/images/wallet.svg" alt="Best Prices" class="w-8 h-8">
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Best Price Guarantee</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Enjoy completely transparent airfare with zero hidden convenience fees or last-minute surprise surcharges.
                </p>
            </div>

            <!-- Feature Card 3 -->
            <div class="p-8 rounded-none bg-slate-50 border border-slate-200 hover:border-indigo-500 transition-colors">
                <div class="w-14 h-14 rounded-none bg-indigo-100 border border-indigo-200 flex items-center justify-center mb-6">
                    <img src="assets/images/suitcase.svg" alt="Amazing Services" class="w-8 h-8">
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Instant E-Tickets & Care</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Instant ticket confirmation with downloadable boarding passes and 24/7 dedicated customer journey support.
                </p>
            </div>

        </div>

    </div>
</section>

<!-- Popular Routes Showcase (rounded-none, shadow-none, subtle border) -->
<section class="py-20 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-sky-600 font-bold uppercase tracking-wider text-xs">Trending Itineraries</span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">Popular Flight Routes</h2>
            </div>
            <p class="text-slate-500 text-sm mt-2 md:mt-0">Curated trending flights with daily departures</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Route Card 1 -->
            <div class="bg-white rounded-none border border-slate-200 p-5 hover:border-sky-500 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-none bg-sky-100 text-sky-800 border border-sky-200">Direct Flight</span>
                    <span class="text-xs font-semibold text-slate-500">Daily</span>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="block text-lg font-extrabold text-slate-900">San Jose</span>
                        <span class="text-xs text-slate-500">SJC</span>
                    </div>
                    <div class="text-slate-400">
                        <i class="fa-solid fa-plane text-sm"></i>
                    </div>
                    <div class="text-right">
                        <span class="block text-lg font-extrabold text-slate-900">Chicago</span>
                        <span class="text-xs text-slate-500">ORD</span>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-slate-500 block">Starting from</span>
                        <span class="text-base font-extrabold text-sky-600">$185</span>
                    </div>
                    <a href="book.php" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                        Book Now <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Route Card 2 -->
            <div class="bg-white rounded-none border border-slate-200 p-5 hover:border-emerald-500 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-none bg-emerald-100 text-emerald-800 border border-emerald-200">Non-Stop</span>
                    <span class="text-xs font-semibold text-slate-500">5x Weekly</span>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="block text-lg font-extrabold text-slate-900">Chicago</span>
                        <span class="text-xs text-slate-500">ORD</span>
                    </div>
                    <div class="text-slate-400">
                        <i class="fa-solid fa-plane text-sm"></i>
                    </div>
                    <div class="text-right">
                        <span class="block text-lg font-extrabold text-slate-900">Olisphis</span>
                        <span class="text-xs text-slate-500">OLS</span>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-slate-500 block">Starting from</span>
                        <span class="text-base font-extrabold text-sky-600">$220</span>
                    </div>
                    <a href="book.php" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                        Book Now <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Route Card 3 -->
            <div class="bg-white rounded-none border border-slate-200 p-5 hover:border-indigo-500 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-none bg-indigo-100 text-indigo-800 border border-indigo-200">Popular</span>
                    <span class="text-xs font-semibold text-slate-500">Daily</span>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="block text-lg font-extrabold text-slate-900">Weling</span>
                        <span class="text-xs text-slate-500">WEL</span>
                    </div>
                    <div class="text-slate-400">
                        <i class="fa-solid fa-plane text-sm"></i>
                    </div>
                    <div class="text-right">
                        <span class="block text-lg font-extrabold text-slate-900">Chiby</span>
                        <span class="text-xs text-slate-500">CBY</span>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-slate-500 block">Starting from</span>
                        <span class="text-base font-extrabold text-sky-600">$140</span>
                    </div>
                    <a href="book.php" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                        Book Now <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Route Card 4 -->
            <div class="bg-white rounded-none border border-slate-200 p-5 hover:border-amber-500 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-none bg-amber-100 text-amber-800 border border-amber-200">Best Deal</span>
                    <span class="text-xs font-semibold text-slate-500">Daily</span>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="block text-lg font-extrabold text-slate-900">San Jose</span>
                        <span class="text-xs text-slate-500">SJC</span>
                    </div>
                    <div class="text-slate-400">
                        <i class="fa-solid fa-plane text-sm"></i>
                    </div>
                    <div class="text-right">
                        <span class="block text-lg font-extrabold text-slate-900">Flerough</span>
                        <span class="text-xs text-slate-500">FLR</span>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-slate-500 block">Starting from</span>
                        <span class="text-base font-extrabold text-sky-600">$165</span>
                    </div>
                    <a href="book.php" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                        Book Now <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Modern Flat Footer (rounded-none, shadow-none, subtle border) -->
<footer class="mt-auto bg-slate-950 text-slate-400 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            
            <!-- Brand Column -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-none bg-sky-600 flex items-center justify-center text-white border border-sky-500/30">
                        <i class="fa-solid fa-plane-departure text-sm"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-white font-brand">SkyWings</span>
                </div>
                <p class="text-sm text-slate-400 max-w-sm leading-relaxed mb-4">
                    Your premier online flight booking portal. Fly with confidence, transparency, and top-tier airline partners.
                </p>
                <div class="flex items-center gap-2 text-slate-400">
                    <a href="#" class="w-8 h-8 rounded-none bg-slate-900 border border-slate-800 flex items-center justify-center hover:text-sky-400 hover:border-sky-500 transition-colors"><i class="fa-brands fa-facebook-f text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-none bg-slate-900 border border-slate-800 flex items-center justify-center hover:text-sky-400 hover:border-sky-500 transition-colors"><i class="fa-brands fa-twitter text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-none bg-slate-900 border border-slate-800 flex items-center justify-center hover:text-sky-400 hover:border-sky-500 transition-colors"><i class="fa-brands fa-instagram text-xs"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">Quick Navigation</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="index.php" class="hover:text-white transition-colors">Home</a></li>
                    <li><a href="book.php" class="hover:text-white transition-colors">Search Flights</a></li>
                    <li><a href="feedback.php" class="hover:text-white transition-colors">Passenger Feedback</a></li>
                    <li><a href="admin/login.php" class="hover:text-white transition-colors">Admin Portal</a></li>
                </ul>
            </div>

            <!-- Security & Compliance -->
            <div>
                <h4 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">Security & Trust</h4>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-shield-halved text-emerald-500 text-xs"></i> SSL 256-bit Encrypted</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-bolt text-amber-500 text-xs"></i> Instant E-Ticket Delivery</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-clock text-sky-500 text-xs"></i> 24/7 Dedicated Support</li>
                </ul>
            </div>

        </div>

        <div class="pt-8 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>&copy; <?php echo date('Y'); ?> SkyWings Aviation. All rights reserved.</p>
            <p>Crafted for Seamless Airline Booking Experiences</p>
        </div>
    </div>
</footer>

<!-- Interactive Scripts for Tabs & Counters -->
<script>
    function switchTripTab(type) {
        const roundForm = document.getElementById('roundTripForm');
        const oneForm = document.getElementById('oneWayForm');
        const roundBtn = document.getElementById('tabRoundBtn');
        const oneBtn = document.getElementById('tabOneWayBtn');

        if (type === 'round') {
            roundForm.classList.remove('hidden');
            oneForm.classList.add('hidden');
            
            roundBtn.className = "flex items-center gap-2 px-6 py-3 rounded-none font-bold text-sm transition-colors border-b-2 border-sky-600 text-sky-600 bg-sky-50/50";
            oneBtn.className = "flex items-center gap-2 px-6 py-3 rounded-none font-bold text-sm text-slate-600 hover:text-slate-900 transition-colors border-b-2 border-transparent";
        } else {
            roundForm.classList.add('hidden');
            oneForm.classList.remove('hidden');
            
            oneBtn.className = "flex items-center gap-2 px-6 py-3 rounded-none font-bold text-sm transition-colors border-b-2 border-sky-600 text-sky-600 bg-sky-50/50";
            roundBtn.className = "flex items-center gap-2 px-6 py-3 rounded-none font-bold text-sm text-slate-600 hover:text-slate-900 transition-colors border-b-2 border-transparent";
        }
    }

    function adjustPassengers(formType, delta) {
        const display = document.getElementById(formType + 'PassengerDisplay');
        const input = document.getElementById(formType + 'PassengerInput');
        
        let current = parseInt(input.value, 10) || 1;
        let updated = current + delta;
        
        if (updated < 1) updated = 1;
        if (updated > 10) updated = 10;
        
        input.value = updated;
        display.textContent = updated;
    }
</script>

<?php subview('footer.php'); ?>