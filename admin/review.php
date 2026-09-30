<?php
include_once 'header.php';
require '../helpers/init_conn_db.php';

// Auth check
if (!isset($_SESSION['adminId'])) {
    header('Location: login.php');
    exit();
}

// Fetch all feedback
$feedbacks = [];
$total_stars = 0;
$sql = 'SELECT * FROM feedback ORDER BY feed_id DESC';
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($rf = mysqli_fetch_assoc($res)) {
        $feedbacks[] = $rf;
        $total_stars += (int)$rf['rate'];
    }
}

$feedback_count = count($feedbacks);
$avg_rating = $feedback_count > 0 ? round($total_stars / $feedback_count, 1) : 0;
?>

<!-- Customer Reviews Admin Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Page Header -->
        <div class="bg-white border border-slate-300 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-amber-500/10 text-amber-800 border border-amber-300 mb-1.5">
                    <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                    <span>Passenger Feedback Portal</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Customer Experience & Reviews
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Review passenger impressions, satisfaction ratings, referral sources, and improvement suggestions.
                </p>
            </div>

            <!-- Review Metrics Badge -->
            <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 p-3 sm:px-5">
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Reviews</span>
                    <span class="text-xl font-black text-slate-900"><?php echo $feedback_count; ?></span>
                </div>
                <div class="h-8 w-[1px] bg-slate-200"></div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Average Rating</span>
                    <div class="flex items-center gap-1 text-amber-500 font-black text-xl leading-none">
                        <span><?php echo $avg_rating; ?></span>
                        <i class="fa-solid fa-star text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Grid -->
        <?php if (empty($feedbacks)): ?>
            <div class="bg-white border border-slate-300 p-12 text-center">
                <div class="w-14 h-14 bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 border border-slate-200 text-2xl">
                    <i class="fa-solid fa-comment-slash"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">No Feedback Submitted Yet</h3>
                <p class="text-xs text-slate-500 mt-1">
                    When passengers submit feedback from the website, their reviews will appear here.
                </p>
            </div>
        <?php else: ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($feedbacks as $fb): 
                    $stars = (int)$fb['rate'];
                ?>
                    <div class="bg-white border border-slate-300 p-6 space-y-4 hover:border-slate-400 transition-colors">
                        
                        <!-- Review Card Header -->
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-slate-900 text-white flex items-center justify-center font-bold text-xs border border-slate-800">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-extrabold text-slate-900"><?php echo htmlspecialchars($fb['email']); ?></span>
                                    <span class="block text-[10px] text-slate-400 font-semibold font-mono">Feedback #<?php echo $fb['feed_id']; ?></span>
                                </div>
                            </div>

                            <!-- Star Rating -->
                            <div class="flex items-center gap-1 text-amber-400 text-xs">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <?php if ($s <= $stars): ?>
                                        <i class="fa-solid fa-star"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-star text-slate-200"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <span class="ml-1 font-extrabold text-slate-700 text-xs">(<?php echo $stars; ?>/5)</span>
                            </div>
                        </div>

                        <!-- Questions & Answers Breakdown -->
                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">First Impression:</span>
                                <p class="text-slate-800 bg-slate-50 p-2.5 border border-slate-200 font-medium">
                                    <?php echo htmlspecialchars($fb['q1']); ?>
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Discovered Via:</span>
                                    <span class="inline-block px-2.5 py-1 bg-sky-50 text-sky-800 font-bold border border-sky-200">
                                        <?php echo htmlspecialchars($fb['q2']); ?>
                                    </span>
                                </div>

                                <?php if (!empty($fb['q3'])): ?>
                                    <div>
                                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Suggestions:</span>
                                        <p class="text-slate-700 bg-slate-50 p-1.5 border border-slate-200">
                                            <?php echo htmlspecialchars($fb['q3']); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php include_once 'footer.php'; ?>
