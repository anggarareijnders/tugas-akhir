<?php
// paket.php
require_once 'config/database.php';
require_once 'classes/Trip.php';
require_once 'classes/Auth.php';

$db = new Database();
$pdo = $db->getConnection();
$tripClass = new Trip($pdo);

// Get search & category filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$trips = $tripClass->getAll($search, null, $kategori ?: null);

$current_page = 'paket';
$page_title = 'Katalog Paket Trip - Jelajahi Gunung';
$base_path = "./";

include 'includes/header.php';
?>

<!-- Header Banner -->
<section class="bg-forest-900 bg-[radial-gradient(ellipse_at_bottom,_rgba(16,185,129,0.15)_0%,transparent_60%)] text-white py-14 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-emerald-400 via-transparent to-transparent"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Katalog Paket Trip Pendakian</h1>
        <p class="text-emerald-200 text-sm sm:text-base max-w-xl mx-auto">
            Temukan berbagai paket trip pendakian gunung di Indonesia yang disediakan oleh vendor terpercaya.
        </p>
    </div>
</section>

<!-- Filter and Cards Section -->
<section class="py-12 bg-neutral-50 flex-grow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Search and SPK Action bar -->
        <div class="bg-white p-6 rounded-3xl border border-neutral-100 shadow-[0_4px_20px_rgb(0,0,0,0.04)] mb-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <form action="paket.php" method="GET" class="w-full md:max-w-xl flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Cari nama paket, gunung..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm font-medium">
                </div>

                <select name="kategori" class="px-3 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:ring-2 focus:ring-forest-500 text-xs font-bold bg-neutral-50">
                    <option value="">Semua Kategori</option>
                    <option value="Camping" <?php echo $kategori === 'Camping' ? 'selected' : ''; ?>>Camping (Menginap)</option>
                    <option value="Tektok" <?php echo $kategori === 'Tektok' ? 'selected' : ''; ?>>Tektok (1 Hari)</option>
                </select>

                <button type="submit" class="bg-forest-800 hover:bg-forest-900 text-white font-semibold px-5 py-2.5 rounded-xl text-xs transition hover:-translate-y-0.5 duration-200">
                    Filter
                </button>
            </form>

            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <?php if (Auth::isLoggedIn() && Auth::getRole() === 'pendaki'): ?>
                    <a href="pendaki/preferensi_moora.php" class="w-full md:w-auto px-5 py-3 rounded-xl bg-forest-800 text-white hover:bg-forest-900 shadow-lg shadow-forest-800/15 font-bold text-sm transition hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Hitung Rekomendasi MOORA
                    </a>
                <?php else: ?>
                    <div class="text-xs text-neutral-500 text-right hidden lg:block">
                        Ingin mendapat rekomendasi terperinci?<br>
                        <a href="auth/login.php" class="text-forest-800 font-bold hover:underline">Masuk sebagai Pendaki</a>
                    </div>
                    <a href="auth/register.php" class="w-full md:w-auto px-5 py-3 rounded-xl border border-forest-800 text-forest-800 hover:bg-forest-800 hover:text-white font-bold text-sm transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-plus"></i> Gabung Pendaki
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($search)): ?>
            <div class="mb-6 text-sm text-neutral-600">
                Menampilkan hasil pencarian untuk: <strong class="text-neutral-800">"<?php echo htmlspecialchars($search); ?>"</strong> 
                (<?php echo count($trips); ?> paket ditemukan)
            </div>
        <?php endif; ?>

        <!-- Catalog Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($trips)): ?>
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-16 bg-white rounded-[2rem] border border-neutral-100 shadow-sm text-neutral-500">
                    <div class="h-16 w-16 bg-neutral-100 text-neutral-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <h3 class="text-lg font-bold text-neutral-800 mb-1">Paket Tidak Ditemukan</h3>
                    <p class="text-sm max-w-sm mx-auto">Kami tidak menemukan paket trip pendakian yang cocok dengan pencarian Anda. Silakan coba kata kunci lain.</p>
                </div>
            <?php else: ?>
                <?php foreach ($trips as $trip): ?>
                    <!-- TAMBAHAN LOGIKA STYLE: Jika penuh, opacity agak diturunkan -->
                    <div class="bg-white rounded-3xl overflow-hidden border border-neutral-100 shadow-[0_4px_20px_rgb(0,0,0,0.04)] hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col h-full group <?php echo (isset($trip['status_kuota']) && $trip['status_kuota'] === 'Penuh') ? 'opacity-85 grayscale-[15%]' : ''; ?>">
                        
                        <!-- Trip Image -->
                        <div class="relative overflow-hidden aspect-[16/10]">
                            <!-- TAMBAHAN: Overlay Gelap jika Penuh -->
                            <?php if (isset($trip['status_kuota']) && $trip['status_kuota'] === 'Penuh'): ?>
                                <div class="absolute inset-0 bg-neutral-900/40 z-10"></div>
                            <?php endif; ?>
                            
                            <img src="<?php echo !empty($trip['gambar']) ? htmlspecialchars($trip['gambar']) : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80'; ?>" 
                                 alt="<?php echo htmlspecialchars($trip['nama_paket']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            
                            <!-- Ditambahkan z-20 agar tetap di atas overlay gelap -->
                            <span class="absolute top-4 left-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-forest-800/90 text-white backdrop-blur-sm z-20">
                                <i class="fa-solid fa-mountain mr-1"></i> <?php echo htmlspecialchars($trip['gunung']); ?>
                            </span>

                            <!-- TAMBAHAN: Badge Sold Out -->
                            <?php if (isset($trip['status_kuota']) && $trip['status_kuota'] === 'Penuh'): ?>
                                <span class="absolute top-4 right-4 inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-red-600 shadow-lg text-white tracking-widest uppercase z-20 border border-red-400">
                                    <i class="fa-solid fa-ban mr-1"></i> Sold Out
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex-grow flex flex-col justify-between space-y-5">
                            <div class="space-y-2">
                                <span class="text-xs font-bold text-soil-700 uppercase tracking-wider block">
                                    <i class="fa-solid fa-campground mr-1"></i> <?php echo htmlspecialchars($trip['vendor_name']); ?>
                                </span>
                                <h3 class="text-lg font-bold text-neutral-800 line-clamp-1 group-hover:text-forest-800 transition">
                                    <?php echo htmlspecialchars($trip['nama_paket']); ?>
                                </h3>
                                <p class="text-neutral-500 text-sm line-clamp-3 leading-relaxed">
                                    <?php echo htmlspecialchars($trip['deskripsi']); ?>
                                </p>
                            </div>

                            <!-- Criteria Values for MOORA -->
                            <div class="space-y-2">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Spesifikasi Kriteria SPK</span>
                                <div class="grid grid-cols-2 gap-2.5 text-xs text-neutral-700 bg-neutral-50 p-4 rounded-2xl border border-neutral-100 shadow-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded bg-emerald-100 text-emerald-800 flex items-center justify-center text-[10px]" title="Ketinggian Gunung (Benefit)">
                                            <i class="fa-solid fa-up-long"></i>
                                        </div>
                                        <div>
                                            <span class="block text-[8px] text-neutral-400 uppercase font-bold">Ketinggian</span>
                                            <span class="font-bold text-neutral-800"><?php echo number_format($trip['c2_ketinggian']); ?> mdpl</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded bg-amber-100 text-amber-800 flex items-center justify-center text-[10px]" title="Kesulitan Medan (Cost)">
                                            <i class="fa-solid fa-route"></i>
                                        </div>
                                        <div>
                                            <span class="block text-[8px] text-neutral-400 uppercase font-bold">Medan</span>
                                            <span class="font-bold text-neutral-800"><?php 
                                                if ($trip['c3_medan'] == 1) echo 'Mudah';
                                                elseif ($trip['c3_medan'] == 2) echo 'Sedang';
                                                else echo 'Sulit';
                                            ?></span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded bg-blue-100 text-blue-800 flex items-center justify-center text-[10px]" title="Fasilitas (Benefit)">
                                            <i class="fa-solid fa-kitchen-set"></i>
                                        </div>
                                        <div>
                                            <span class="block text-[8px] text-neutral-400 uppercase font-bold">Fasilitas</span>
                                            <span class="font-bold text-neutral-800"><?php echo count(explode(',', $trip['c4_fasilitas'])); ?> Item</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded bg-indigo-100 text-indigo-800 flex items-center justify-center text-[10px]" title="Waktu Tempuh (Cost)">
                                            <i class="fa-solid fa-clock"></i>
                                        </div>
                                        <div>
                                            <span class="block text-[8px] text-neutral-400 uppercase font-bold">Waktu ke Camp</span>
                                            <span class="font-bold text-neutral-800"><?php echo $trip['c5_waktu_tempuh']; ?> Jam</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-neutral-100 flex items-center justify-between gap-2">
                                <div>
                                    <span class="block text-[10px] text-neutral-400 font-bold uppercase tracking-wider">Harga Paket (Cost)</span>
                                    <span class="text-lg font-extrabold text-forest-800">Rp <?php echo number_format($trip['c1_harga'], 0, ',', '.'); ?></span>
                                </div>
                                
                                <!-- TAMBAHAN: Matikan tombol jika Penuh (dan perbaikan escaping json untuk keamanan) -->
                                <?php if (isset($trip['status_kuota']) && $trip['status_kuota'] === 'Penuh'): ?>
                                    <button disabled class="px-4 py-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-400 text-xs font-bold cursor-not-allowed">
                                        Kuota Habis
                                    </button>
                                <?php else: ?>
                                    <button onclick='showTripDetail(<?php echo htmlspecialchars(json_encode($trip), ENT_QUOTES, 'UTF-8'); ?>)' class="px-4 py-2.5 rounded-xl bg-forest-800 hover:bg-forest-900 text-white text-xs font-bold transition active:scale-95 shadow-md shadow-forest-800/10">
                                        Lihat Detail
                                    </button>
                                <?php endif; ?>
                                
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>