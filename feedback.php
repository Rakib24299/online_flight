<?php
include_once 'helpers/helper.php';
subview('header.php');

$prefill_email = '';
if (isset($_SESSION['userMail'])) {
    $prefill_email = $_SESSION['userMail'];
}
?>

<!-- Feedback Page Container (Flat Modernist: rounded-none, shadow-none, subtle borders) -->
<main class="min-h-screen bg-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Notification / Alert Messages -->
        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'success'): ?>
                <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center gap-3">
                    <div class="w-8 h-8 bg-emerald-100 text-emerald-700 flex items-center justify-center border border-emerald-300 flex-shrink-0">
                        <i class="fa-solid fa-circle-check text-base"></i>
                    </div>
                    <div>
                        <strong>Thank you!</strong> Your feedback has been successfully submitted. We appreciate your insights!
                    </div>
                </div>
            <?php elseif ($_GET['error'] === 'invalidemail'): ?>
                <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 text-sm font-semibold flex items-center gap-3">
                    <div class="w-8 h-8 bg-rose-100 text-rose-700 flex items-center justify-center border border-rose-300 flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    </div>
                    <div>
                        <strong>Invalid Email:</strong> Please enter a valid email address before submitting.
                    </div>
                </div>
            <?php elseif ($_GET['error'] === 'sqlerror'): ?>
                <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 text-sm font-semibold flex items-center gap-3">
                    <div class="w-8 h-8 bg-rose-100 text-rose-700 flex items-center justify-center border border-rose-300 flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    </div>
                    <div>
                        <strong>System Error:</strong> An error occurred while submitting your feedback. Please try again.
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Main Feedback Grid (2 Columns on desktop) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left Info Column (5 Columns) -->
            <div class="lg:col-span-5 bg-slate-900 text-white border border-slate-800 p-8 flex flex-col justify-between space-y-8">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-bold bg-sky-950/80 text-sky-400 border border-sky-800">
                        <i class="fa-solid fa-comment-dots text-xs"></i>
                        <span>Passenger Experience</span>
                    </div>

                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                            We Value Your Travel Feedback
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-400 mt-2 leading-relaxed">
                            Your impressions, ratings, and suggestions help us deliver a smoother, faster, and more comfortable flight booking experience.
                        </p>
                    </div>

                    <!-- Trust Points -->
                    <div class="space-y-4 pt-4 border-t border-slate-800 text-xs text-slate-300">
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-sky-600/20 text-sky-400 flex items-center justify-center border border-sky-500/30 flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-heart text-xs"></i>
                            </div>
                            <div>
                                <strong class="block text-white font-bold">Continuous Improvement</strong>
                                <span class="text-slate-400 text-[11px]">Every review is directly reviewed by our quality and operations team.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-emerald-600/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-shield-halved text-xs"></i>
                            </div>
                            <div>
                                <strong class="block text-white font-bold">Privacy Guaranteed</strong>
                                <span class="text-slate-400 text-[11px]">Your contact details are strictly kept confidential and secure.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-amber-600/20 text-amber-400 flex items-center justify-center border border-amber-500/30 flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-headset text-xs"></i>
                            </div>
                            <div>
                                <strong class="block text-white font-bold">24/7 Dedicated Support</strong>
                                <span class="text-slate-400 text-[11px]">Need immediate assistance with a flight? Contact support anytime.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-800 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>SkyWings Feedback Portal</span>
                    <span class="font-mono text-slate-400">&copy; <?php echo date('Y'); ?></span>
                </div>
            </div>

            <!-- Right Form Column (7 Columns) -->
            <div class="lg:col-span-7 bg-white border border-slate-300 p-8">
                
                <div class="border-b border-slate-200 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-sky-600 text-base"></i>
                        <span>Share Your Thoughts</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Please take a moment to answer these short questions</p>
                </div>

                <form action="includes/feedback.inc.php" method="POST" class="space-y-6">
                    
                    <!-- Email Address -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            <i class="fa-solid fa-envelope text-sky-600 mr-1"></i> Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($prefill_email); ?>" required placeholder="you@example.com" class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors">
                    </div>

                    <!-- Question 1: First Impression -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            <i class="fa-solid fa-eye text-sky-600 mr-1"></i> What was your first impression of the website? <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="1" rows="3" required placeholder="Tell us what you liked, noticed, or how easy it was to navigate..." class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors"></textarea>
                    </div>

                    <!-- Question 2: Referral Source Dropdown -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            <i class="fa-solid fa-bullhorn text-sky-600 mr-1"></i> How did you first hear about us? <span class="text-rose-500">*</span>
                        </label>
                        <select name="2" required class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none cursor-pointer transition-colors">
                            <option value="" disabled selected>Please select an option</option>
                            <option value="Search Engine">Search Engine (Google, Bing)</option>
                            <option value="Social Media">Social Media (Facebook, Instagram, Twitter)</option>
                            <option value="Friend/Relative">Friend / Relative Recommendation</option>
                            <option value="Word of Mouth">Word of Mouth</option>
                            <option value="Television">Television / Online Ad</option>
                            <option value="Other">Other Source</option>
                        </select>
                    </div>

                    <!-- Question 3: Missing items or improvements (Optional) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            <i class="fa-solid fa-lightbulb text-sky-600 mr-1"></i> Any additional features or suggestions?
                        </label>
                        <textarea name="3" rows="2" placeholder="Let us know if there's anything else you'd like to see..." class="w-full bg-slate-50 p-3 rounded-none border border-slate-300 text-slate-900 text-sm focus:bg-white focus:border-sky-600 focus:outline-none transition-colors"></textarea>
                    </div>

                    <!-- Overall Star Rating -->
                    <div class="bg-slate-50 border border-slate-200 p-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            <i class="fa-solid fa-star text-amber-500 mr-1"></i> Overall Experience Rating <span class="text-rose-500">*</span>
                        </label>
                        
                        <!-- Interactive Star Rating Radio Group -->
                        <div class="flex items-center gap-3" id="starRatingContainer">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <label class="cursor-pointer flex flex-col items-center gap-1 group">
                                    <input type="radio" name="stars" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?> class="sr-only star-radio" required>
                                    <span class="star-icon text-2xl text-slate-300 group-hover:text-amber-400 transition-colors">
                                        <i class="fa-solid fa-star"></i>
                                    </span>
                                    <span class="inline-flex items-center text-[10px] font-bold text-slate-500"><?php echo $i; ?> <i class="fa-solid fa-star text-[8px] text-amber-500 ml-0.5"></i></span>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" name="feed_but" class="w-full py-3.5 px-6 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm border border-sky-700 transition-colors flex items-center justify-center gap-2">
                            <span>Submit Feedback</span>
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>
</main>

<script>
    // Interactive Star Rating Highlight Script
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('.star-radio');
        const icons = document.querySelectorAll('.star-icon');

        function updateStars(val) {
            icons.forEach((icon, index) => {
                if (index < val) {
                    icon.classList.remove('text-slate-300');
                    icon.classList.add('text-amber-400');
                } else {
                    icon.classList.remove('text-amber-400');
                    icon.classList.add('text-slate-300');
                }
            });
        }

        // Initialize with default checked
        const checked = document.querySelector('.star-radio:checked');
        if (checked) {
            updateStars(parseInt(checked.value, 10));
        }

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                updateStars(parseInt(this.value, 10));
            });
        });
    });
</script>

<?php subview('footer.php'); ?>