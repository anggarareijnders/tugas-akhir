<?php
// edukasi.php
require_once 'config/database.php';
require_once 'classes/Education.php';
require_once 'classes/Auth.php';

$db = new Database();
$pdo = $db->getConnection();
$eduClass = new Education($pdo);

// Get available mountains that have guides
$allGuides = $eduClass->getAll();
$mountains = array_unique(array_column($allGuides, 'gunung'));

// Get selected mountain guide
$selected_mountain = isset($_GET['gunung']) ? trim($_GET['gunung']) : '';
$guides = [];
if (!empty($selected_mountain)) {
    $guides = $eduClass->getByMountain($selected_mountain);
}

$current_page = 'edukasi';
$page_title = 'Edukasi Logistik & Persiapan Pendakian';
$base_path = "./";

include 'includes/header.php';
?>

<!-- Header Banner -->
<section class="bg-forest-900 text-white py-12 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-emerald-400 via-transparent to-transparent"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase tracking-widest">
            <i class="fa-solid fa-circle-info mr-1.5"></i> Panduan Packing & Fisik
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Edukasi Logistik Pendakian</h1>
        <p class="text-emerald-200 text-sm sm:text-base max-w-xl mx-auto">
            Persiapan logistik yang matang menentukan keselamatan pendakian Anda. Pilih gunung tujuan Anda untuk melihat panduan khusus.
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-12 bg-neutral-50 flex-grow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Mountain Selector Grid -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm mb-10">
            <h2 class="text-sm font-bold text-neutral-400 uppercase tracking-wider mb-4">Pilih Gunung Tujuan Anda:</h2>
            <div class="flex flex-wrap gap-3">
                <a href="edukasi.php" class="px-5 py-2.5 rounded-xl text-sm font-bold transition <?php echo empty($selected_mountain) ? 'bg-forest-800 text-white shadow shadow-forest-800/10' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200'; ?>">
                    <i class="fa-solid fa-list mr-1.5"></i> Semua Panduan
                </a>
                <?php foreach ($mountains as $mtn): ?>
                    <a href="edukasi.php?gunung=<?php echo urlencode($mtn); ?>" 
                       class="px-5 py-2.5 rounded-xl text-sm font-bold transition <?php echo (strcasecmp($selected_mountain, $mtn) === 0) ? 'bg-forest-800 text-white shadow shadow-forest-800/10' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200'; ?>">
                        <i class="fa-solid fa-mountain mr-1.5"></i> Gunung <?php echo htmlspecialchars($mtn); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($selected_mountain)): ?>
            <!-- Displaying All Available Articles -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php if (empty($allGuides)): ?>
                    <div class="col-span-2 text-center py-16 bg-white rounded-3xl border border-neutral-100 shadow-sm text-neutral-500">
                        <i class="fa-solid fa-book text-4xl mb-3 block text-neutral-300"></i>
                        Belum ada artikel edukasi logistik yang dipublikasikan.
                    </div>
                <?php else: ?>
                    <?php foreach ($allGuides as $guide): ?>
                        <div class="bg-white rounded-2xl p-8 border border-neutral-100 shadow-sm hover:shadow-md transition space-y-4 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-forest-50 text-forest-800 border border-forest-100">
                                        <i class="fa-solid fa-mountain mr-1"></i> Gunung <?php echo htmlspecialchars($guide['gunung']); ?>
                                    </span>
                                    <span class="text-xs text-neutral-400"><i class="fa-regular fa-clock mr-1"></i> <?php echo date("d M Y", strtotime($guide['created_at'])); ?></span>
                                </div>
                                <h3 class="text-xl font-bold text-neutral-800 hover:text-forest-800 transition line-clamp-1">
                                    <a href="edukasi.php?gunung=<?php echo urlencode($guide['gunung']); ?>"><?php echo htmlspecialchars($guide['judul']); ?></a>
                                </h3>
                                <p class="text-neutral-500 text-sm leading-relaxed line-clamp-3">
                                    <?php echo htmlspecialchars($guide['konten']); ?>
                                </p>
                            </div>
                            
                            <div class="pt-4 border-t border-neutral-100 flex items-center justify-between">
                                <span class="text-xs text-neutral-400">Dipublikasi oleh: <strong class="text-neutral-600"><?php echo htmlspecialchars($guide['vendor_name']); ?></strong></span>
                                <a href="edukasi.php?gunung=<?php echo urlencode($guide['gunung']); ?>" class="text-forest-800 hover:text-forest-900 font-bold text-sm flex items-center gap-1">
                                    Lihat Detail & Checklist <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Displaying Selected Mountain Guides -->
            <?php if (empty($guides)): ?>
                <div class="text-center py-16 bg-white rounded-3xl border border-neutral-100 shadow-sm text-neutral-500">
                    <i class="fa-solid fa-magnifying-glass text-4xl mb-3 block text-neutral-300"></i>
                    Panduan untuk Gunung <strong>"<?php echo htmlspecialchars($selected_mountain); ?>"</strong> tidak ditemukan.
                </div>
            <?php else: ?>
                <div class="space-y-8">
                    <?php foreach ($guides as $guide): ?>
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                            
                            <!-- Left: Article Content -->
                            <div class="lg:col-span-8 bg-white rounded-2xl p-8 border border-neutral-100 shadow-sm space-y-6">
                                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-100 pb-4">
                                    <div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-forest-50 text-forest-800 border border-forest-100 mb-2">
                                            <i class="fa-solid fa-mountain mr-1"></i> Gunung <?php echo htmlspecialchars($guide['gunung']); ?>
                                        </span>
                                        <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-800"><?php echo htmlspecialchars($guide['judul']); ?></h2>
                                    </div>
                                </div>

                                <div class="text-xs text-neutral-400 flex gap-4">
                                    <span><i class="fa-solid fa-campground mr-1"></i> Vendor: <strong><?php echo htmlspecialchars($guide['vendor_name']); ?></strong></span>
                                    <span>&bull;</span>
                                    <span><i class="fa-solid fa-calendar mr-1"></i> Diposting: <?php echo date("d M Y", strtotime($guide['created_at'])); ?></span>
                                    <?php if (!empty($guide['nama_paket'])): ?>
                                        <span>&bull;</span>
                                        <span><i class="fa-solid fa-compass mr-1"></i> Terkait Paket: <strong class="text-forest-850"><?php echo htmlspecialchars($guide['nama_paket']); ?></strong></span>
                                    <?php endif; ?>
                                </div>

                                <div class="text-neutral-600 text-sm sm:text-base leading-relaxed space-y-4 whitespace-pre-line border-t border-neutral-100 pt-6">
                                    <?php echo htmlspecialchars($guide['konten']); ?>
                                </div>
                            </div>

                            <!-- Right: Packing List Checklist Helper -->
                            <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-neutral-100 shadow-sm space-y-6 sticky top-24">
                                <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                                    <div class="h-10 w-10 bg-forest-50 text-forest-800 flex items-center justify-center rounded-xl text-lg font-bold">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-neutral-800">Checklist Logistik</h3>
                                        <p class="text-[10px] text-neutral-400 font-bold uppercase">Panduan Bawaan Wajib</p>
                                    </div>
                                </div>

                                <p class="text-xs text-neutral-500 leading-relaxed">
                                    Centang barang-barang berikut saat Anda memasukkannya ke dalam carrier/tas ransel Anda.
                                </p>

                                <div class="space-y-2.5">
                                    <?php 
                                    $logistik_items = explode("\n", $guide['logistik']);
                                    $index = 1;
                                    foreach ($logistik_items as $item):
                                        $item = trim($item);
                                        if (empty($item)) continue;
                                    ?>
                                        <label class="flex items-start gap-3 p-3 rounded-xl border border-neutral-100 hover:bg-neutral-50 cursor-pointer select-none transition group">
                                            <input type="checkbox" id="item-<?php echo $index; ?>" 
                                                   class="mt-1 h-4 w-4 rounded border-neutral-300 text-forest-800 focus:ring-forest-500 accent-forest-850"
                                                   onclick="toggleItem(this)">
                                            <span class="text-sm font-semibold text-neutral-700 group-[&:has(input:checked)]:line-through group-[&:has(input:checked)]:text-neutral-400 transition">
                                                <?php echo htmlspecialchars($item); ?>
                                            </span>
                                        </label>
                                    <?php 
                                        $index++;
                                    endforeach; 
                                    ?>
                                </div>

                                <div class="bg-forest-50 border border-forest-100 p-4 rounded-xl text-center">
                                    <span class="block text-xs font-bold text-forest-900 mb-1">Butuh paket porter/trip lengkap?</span>
                                    <a href="paket.php?search=<?php echo urlencode($guide['gunung']); ?>" class="text-xs font-extrabold text-forest-750 hover:underline">
                                        Lihat Trip Gunung <?php echo htmlspecialchars($guide['gunung']); ?> <i class="fa-solid fa-angle-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<script>
    // Client-side visual packing helper
    function toggleItem(checkbox) {
        // Safe to leave empty, CSS handle the toggle styling via peer-checked or parent :has selector in Tailwind.
        // We added custom tailwind dynamic classes `group-[&:has(input:checked)]` to strike through and mute text.
    }
</script>

<?php include 'includes/footer.php'; ?>
