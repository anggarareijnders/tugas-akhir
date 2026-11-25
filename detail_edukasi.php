<?php
// detail_edukasi.php
require_once 'config/database.php';
require_once 'classes/Trip.php';
require_once 'classes/Logistics.php';
require_once 'classes/Auth.php';

$db = new Database();
$pdo = $db->getConnection();
$tripClass = new Trip($pdo);

$paketId = isset($_GET['id_paket']) ? intval($_GET['id_paket']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

if ($paketId <= 0) {
    // If no ID specified, pick the first available package
    $allTrips = $tripClass->getAll();
    if (!empty($allTrips)) {
        $paketId = $allTrips[0]['id'];
    }
}

$paket = $tripClass->getById($paketId);

if (!$paket) {
    header("Location: index.php");
    exit();
}

$segments = $tripClass->getRuteSegments($paketId);
$contacts = $tripClass->getKontakDarurat($paketId);

// Facility parsing
$providedFacilities = !empty($paket['c4_fasilitas']) ? explode(',', $paket['c4_fasilitas']) : [];
$selfBroughtItems = Logistics::getSelfBroughtEquipment($providedFacilities, $paket['kategori']);

$current_page = 'edukasi_detail';
$page_title = 'Detail Edukasi & Karakteristik - ' . htmlspecialchars($paket['nama_paket']);
$base_path = "./";

include 'includes/header.php';
?>

<section class="py-12 bg-neutral-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="index.php" class="hover:text-forest-800 transition">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a href="paket.php" class="hover:text-forest-800 transition">Paket Trip</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Detail Edukasi & Karakteristik</span>
        </div>

        <!-- HERO BANNER MOUNTAIN PHOTO -->
        <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-forest-900 text-white mb-10 border border-neutral-100">
            <div class="relative h-72 sm:h-96 w-full overflow-hidden">
                <img src="<?php echo htmlspecialchars($paket['gambar'] ?: 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=80'); ?>" 
                     alt="<?php echo htmlspecialchars($paket['nama_paket']); ?>" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-forest-950 via-forest-950/50 to-transparent"></div>
            </div>

            <div class="absolute bottom-6 left-6 right-6 sm:bottom-8 sm:left-8 sm:right-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4 z-10">
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-400 text-forest-950 uppercase tracking-wider shadow">
                            <i class="fa-solid fa-campground mr-1"></i> <?php echo htmlspecialchars($paket['kategori']); ?>
                        </span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-white/20 text-white backdrop-blur-md border border-white/20 uppercase tracking-wider">
                            <i class="fa-solid fa-mountain mr-1"></i> Gunung <?php echo htmlspecialchars($paket['gunung']); ?> (<?php echo number_format($paket['c2_ketinggian']); ?> mdpl)
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-white leading-tight">
                        <?php echo htmlspecialchars($paket['nama_paket']); ?>
                    </h1>
                    <p class="text-emerald-200 text-xs sm:text-sm font-semibold">
                        Dikelola oleh Vendor Resmi: <strong class="text-white"><?php echo htmlspecialchars($paket['vendor_name']); ?></strong>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="pendaki/preferensi_moora.php" class="px-5 py-3 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-forest-950 font-black text-xs shadow-lg transition active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Evaluasi SPK MOORA
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN: Overview, Timeline Pos, & Emergency Contacts -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- 1. INFORMASI UMUM & KARAKTERISTIK GUNUNG -->
                <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl space-y-6">
                    <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                        <div class="h-10 w-10 bg-emerald-100 text-emerald-800 rounded-xl flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-neutral-800">Informasi Umum & Karakteristik Medan</h2>
                            <p class="text-xs text-neutral-500">Profil vegetasi, cuaca, dan tingkat tantangan fisik.</p>
                        </div>
                    </div>

                    <p class="text-neutral-600 text-sm leading-relaxed font-medium">
                        <?php echo nl2br(htmlspecialchars($paket['deskripsi'])); ?>
                    </p>

                    <!-- Spec Badges -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 text-center space-y-1">
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Harga Paket</span>
                            <span class="text-base font-black text-forest-800">Rp <?php echo number_format($paket['c1_harga'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 text-center space-y-1">
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Ketinggian</span>
                            <span class="text-base font-black text-neutral-800"><?php echo number_format($paket['c2_ketinggian']); ?> mdpl</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 text-center space-y-1">
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Kesulitan Medan</span>
                            <span class="text-base font-black text-neutral-800">
                                <?php 
                                    if ($paket['c3_medan'] == 1) echo 'Mudah (Landai)';
                                    elseif ($paket['c3_medan'] == 2) echo 'Sedang (Akar)';
                                    else echo 'Sulit (Terjal/Pasir)';
                                ?>
                            </span>
                        </div>
                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 text-center space-y-1">
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Waktu Tempuh</span>
                            <span class="text-base font-black text-neutral-800"><?php echo $paket['c5_waktu_tempuh']; ?> Jam</span>
                        </div>
                    </div>
                </div>

                <!-- 2. TIMELINE SEGMENTASI POS JALUR -->
                <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl space-y-6">
                    <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                        <div class="h-10 w-10 bg-forest-50 text-forest-800 rounded-xl flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-route"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-neutral-800">Timeline Segmentasi Pos Jalur Pendakian</h2>
                            <p class="text-xs text-neutral-500">Rincian estimasi waktu tempuh, kenaikan elevasi, dan karakteristik medan dari pos ke pos.</p>
                        </div>
                    </div>

                    <?php if (empty($segments)): ?>
                        <div class="p-6 rounded-2xl bg-neutral-50 text-center text-xs text-neutral-400 italic">
                            Belum ada segmentasi pos rute terdaftar dari pihak vendor.
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($segments as $idx => $seg): ?>
                                <div class="p-5 rounded-2xl bg-neutral-50 border border-neutral-200/80 hover:border-emerald-300 transition space-y-3 relative group">
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                                        <div class="flex items-center gap-3">
                                            <span class="h-7 w-7 rounded-full bg-forest-800 text-white text-xs font-black flex items-center justify-center flex-shrink-0 shadow">
                                                <?php echo $idx + 1; ?>
                                            </span>
                                            <h3 class="font-extrabold text-neutral-800 text-sm">
                                                <?php echo htmlspecialchars($seg['pos_asal']); ?> &rarr; <?php echo htmlspecialchars($seg['pos_tujuan']); ?>
                                            </h3>
                                        </div>
                                        <div class="flex items-center gap-2 pl-10 sm:pl-0">
                                            <span class="px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">
                                                <i class="fa-solid fa-clock mr-1"></i> <?php echo $seg['waktu_tempuh_menit']; ?> Menit
                                            </span>
                                            <span class="px-3 py-1 rounded-lg bg-forest-100 text-forest-800 text-xs font-bold">
                                                <i class="fa-solid fa-arrow-trend-up mr-1"></i> +<?php echo $seg['elevasi_gain']; ?> mdpl
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-neutral-600 pl-10 leading-relaxed font-medium">
                                        <strong class="text-neutral-700">Karakteristik Medan:</strong> <?php echo htmlspecialchars($seg['kondisi_medan']); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 3. KONTAK DARURAT & PORTER LOKAL -->
                <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl space-y-6">
                    <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                        <div class="h-10 w-10 bg-amber-100 text-amber-800 rounded-xl flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-neutral-800">Kontak Darurat & Porter Lokal Gunung</h2>
                            <p class="text-xs text-neutral-500">Nomor kontak terverifikasi untuk bantuan porter, pengelola basecamp, dan P3K.</p>
                        </div>
                    </div>

                    <?php if (empty($contacts)): ?>
                        <div class="p-6 rounded-2xl bg-neutral-50 text-center text-xs text-neutral-400 italic">
                            Belum ada kontak darurat terdaftar untuk gunung ini.
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach ($contacts as $c): ?>
                                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex items-center gap-4">
                                    <div class="h-11 w-11 rounded-xl bg-amber-600 text-white flex items-center justify-center text-lg font-bold flex-shrink-0 shadow">
                                        <i class="fa-solid fa-user-shield"></i>
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="text-sm font-extrabold text-neutral-800 block"><?php echo htmlspecialchars($c['nama']); ?></span>
                                        <span class="text-xs font-bold text-amber-900 block"><?php echo htmlspecialchars($c['peran']); ?></span>
                                        <span class="text-xs text-neutral-500 font-mono block"><i class="fa-solid fa-phone text-[10px] mr-1"></i> <?php echo htmlspecialchars($c['no_hp']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- RIGHT COLUMN: LOGISTIK MANDIRI & FASILITAS VENDOR -->
            <div class="lg:col-span-4 space-y-8">
                
                <!-- 1. BARANG YANG HARUS DIBAWA MANDIRI (LOGIKA PHP LOGISTICS) -->
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-3xl p-6 text-white shadow-xl space-y-4">
                    <div class="flex items-center gap-3 border-b border-white/20 pb-3">
                        <div class="h-9 w-9 bg-white/20 rounded-xl flex items-center justify-center text-base font-bold">
                            <i class="fa-solid fa-suitcase"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-white text-base leading-tight">Barang Bawaan Mandiri</h3>
                            <p class="text-[11px] text-amber-100">Perlengkapan wajib di luar fasilitas vendor</p>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 space-y-3">
                        <p class="text-xs text-amber-50 font-medium leading-relaxed">
                            Berdasarkan fasilitas yang disediakan vendor (<strong><?php echo count($providedFacilities); ?> item</strong>), berikut perlengkapan pribadi yang <strong class="underline">wajib Anda bawa sendiri</strong>:
                        </p>

                        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                            <?php if (empty($selfBroughtItems)): ?>
                                <p class="text-xs text-white font-bold italic">Selamat! Seluruh peralatan utama sudah ditanggung oleh paket vendor.</p>
                            <?php else: ?>
                                <?php foreach ($selfBroughtItems as $item): ?>
                                    <div class="flex items-start gap-2 text-xs bg-black/10 p-2.5 rounded-xl border border-white/10">
                                        <i class="fa-solid fa-circle-check text-amber-300 mt-0.5 flex-shrink-0"></i>
                                        <div>
                                            <span class="font-bold text-white block"><?php echo htmlspecialchars($item['name']); ?></span>
                                            <span class="text-[10px] text-amber-200 block"><?php echo htmlspecialchars($item['category']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 2. FASILITAS TERSEDIA DARI VENDOR -->
                <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-xl space-y-4">
                    <div class="flex items-center gap-3 border-b border-neutral-100 pb-3">
                        <div class="h-9 w-9 bg-emerald-100 text-emerald-800 rounded-xl flex items-center justify-center text-base font-bold">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-neutral-800 text-base">Fasilitas Termasuk Paket</h3>
                            <p class="text-xs text-neutral-400">Ditanggung oleh <?php echo htmlspecialchars($paket['vendor_name']); ?></p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <?php foreach ($providedFacilities as $facCode): ?>
                            <div class="flex items-center gap-2 p-2.5 rounded-xl bg-emerald-50 text-emerald-900 text-xs font-bold border border-emerald-200">
                                <i class="fa-solid fa-check text-emerald-600 text-sm"></i>
                                <span><?php echo htmlspecialchars(Logistics::getFacilityName(trim($facCode))); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
