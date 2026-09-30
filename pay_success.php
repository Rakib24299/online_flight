<?php
include_once 'helpers/helper.php';
subview('header.php');
?>

<!-- Payment Success Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-16 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-xl w-full bg-white border border-slate-300 p-8 sm:p-12 text-center space-y-6">

        <!-- Animated / Flat Success Icon Badge -->
        <div class="w-20 h-20 bg-emerald-50 border-2 border-emerald-500 text-emerald-600 flex items-center justify-center mx-auto text-3xl">
            <i class="fa-solid fa-check"></i>
        </div>

        <!-- Success Message -->
        <div class="space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300 mb-1">
                <i class="fa-solid fa-circle-check text-xs"></i>
                <span>Payment Confirmed & Verified</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Booking Confirmed!
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                Thank you for choosing SkyWings. Your payment has been processed successfully and your official digital boarding passes are now issued and ready to print.
            </p>
        </div>

        <!-- Booking Next Steps -->
        <div class="bg-slate-50 border border-slate-200 p-5 text-left text-xs space-y-2.5">
            <div class="flex items-center gap-2.5 text-slate-700">
                <i class="fa-solid fa-ticket text-sky-600 text-sm"></i>
                <span>Digital E-Tickets generated with official barcode & seat allocation.</span>
            </div>
            <div class="flex items-center gap-2.5 text-slate-700">
                <i class="fa-solid fa-plane text-emerald-600 text-sm"></i>
                <span>Real-time flight departure and gate tracking is available in your history.</span>
            </div>
            <div class="flex items-center gap-2.5 text-slate-700">
                <i class="fa-solid fa-envelope text-amber-600 text-sm"></i>
                <span>Automated confirmation receipt recorded under your account.</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="ticket.php" class="w-full sm:w-auto px-6 py-3.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs uppercase tracking-wider border border-sky-700 transition-colors flex items-center justify-center gap-2 text-decoration-none">
                <i class="fa-solid fa-ticket text-xs"></i>
                <span>View & Print E-Tickets</span>
            </a>
            <a href="booking_history.php" class="w-full sm:w-auto px-6 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider border border-slate-900 transition-colors flex items-center justify-center gap-2 text-decoration-none">
                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                <span>Booking History</span>
            </a>
        </div>

        <div>
            <a href="dashboard.php" class="text-xs font-bold text-slate-500 hover:text-slate-800 text-decoration-none">
                &larr; Return to Passenger Dashboard
            </a>
        </div>

    </div>
</main>

<?php subview('footer.php'); ?>
