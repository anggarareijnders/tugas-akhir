<?php
// index.php
require_once 'config/database.php';
require_once 'classes/Trip.php';
require_once 'classes/Auth.php';

$db = new Database();
$pdo = $db->getConnection();

// Safety Check
$setup_needed = false;
try {
    if ($pdo) {
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() == 0) {
            $setup_needed = true;
        }
    } else {
        $setup_needed = true;
    }
} catch (Exception $e) {
    $setup_needed = true;
}

if ($setup_needed) {
    header("Location: setup.php");
    exit();
}

$tripClass = new Trip($pdo);
$allTrips = $tripClass->getAll();
$popularTrips = array_slice($allTrips, 0, 3); // Get top 3 packages

// Logika mencari paket termurah sejauh ini
$cheapestTrip = null;
if (!empty($allTrips)) {
    $cheapestTrip = array_reduce($allTrips, function($min, $trip) {
        return ($min === null || $trip['c1_harga'] < $min['c1_harga']) ? $trip : $min;
    });
}

$current_page = 'home';
$page_title = 'Beranda - Solusi Cerdas Trip Pendakian Gunung';
$base_path = "./";

include 'includes/header.php';
?>

<!-- Hero Section -->
<header class="relative bg-gradient-to-br from-slate-900 via-emerald-900 to-teal-900 text-white overflow-hidden py-28 md:py-36">
    <!-- Nature-themed Background Elements -->
    <div class="absolute inset-0 opacity-15">
        <svg class="w-full h-full object-cover" viewBox="0 0 1440 800" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 500L240 420L480 540L720 380L960 480L1200 350L1440 450V800H1200H960H720H480H240H0V500Z" fill="currentColor"/>
            <path d="M0 620L360 550L720 660L1080 570L1440 680V800H1080H720H360H0V620Z" fill="currentColor" opacity="0.5"/>
        </svg>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-500/20 text-white border border-emerald-400/30 uppercase tracking-widest backdrop-blur-sm">
                    <i class="fa-solid fa-mountain-sun mr-2"></i> Sistem Pendukung Keputusan MOORA
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-none text-white drop-shadow-lg">
                    Temukan Trip Gunung <span class="text-emerald-400">Impian</span> Anda Secara Cerdas
                </h1>
                <p class="text-lg text-emerald-100/90 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Bandingkan harga, fasilitas, ketinggian, medan, dan waktu tempuh secara ilmiah dengan metode MOORA. Dapatkan rekomendasi trip terbaik.
                </p>
                
                <!-- Search Box with Category Pre-Filtering -->
                <form action="paket.php" method="GET" class="bg-white/95 backdrop-blur-xl p-2.5 rounded-3xl shadow-[0_20px_60px_rgb(0,0,0,0.15)] ring-1 ring-white/80 flex flex-col sm:flex-row gap-2 max-w-2xl mx-auto lg:mx-0">
                    <div class="flex items-center px-3 gap-2 border-b sm:border-b-0 sm:border-r border-neutral-200 py-1 sm:py-0 sm:w-44 flex-shrink-0">
                        <i class="fa-solid fa-layer-group text-forest-800"></i>
                        <select name="kategori" class="w-full bg-transparent text-neutral-800 font-bold text-xs focus:outline-none cursor-pointer">
                            <option value="">Semua Kategori</option>
                            <option value="Camping">Camping (Menginap)</option>
                            <option value="Tektok">Tektok (1 Hari)</option>
                        </select>
                    </div>

                    <div class="flex-grow flex items-center px-3 gap-2">
                        <i class="fa-solid fa-magnifying-glass text-neutral-400"></i>
                        <input type="text" name="search" placeholder="Cari gunung (contoh: Semeru, Rinjani)..." 
                               class="w-full py-3 bg-transparent text-neutral-800 placeholder-neutral-400 font-medium focus:outline-none text-sm">
                    </div>

                    <button type="submit" class="bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 shadow-lg shadow-emerald-500/30 text-white font-bold px-6 py-3.5 rounded-2xl hover:-translate-y-0.5 transition active:scale-95 text-xs flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter"></i> Cari & Filter
                    </button>
                </form>
            </div>

            <!-- Right Visual Graphic -->
            <div class="lg:col-span-5 hidden lg:block">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-2 border-white/20 ring-1 ring-emerald-500/20 group">
                    <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80" 
                         alt="Mountain Climbing" 
                         class="w-full object-cover aspect-[4/3] group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-forest-950/60 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6 text-white">
                        <span class="text-xs font-bold text-emerald-300 uppercase tracking-widest">Gunung Gede Pangrango</span>
                        <h3 class="text-lg font-bold">Trip Ramah Pemula & Keindahan Sabana Suryakencana</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- SPK MOORA Feature Intro -->
<section class="py-20 bg-gradient-to-b from-white via-emerald-50/20 to-white border-b border-neutral-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12 space-y-3">
            <h2 class="text-3xl font-extrabold text-emerald-800">Mengapa Menggunakan MOORATrip?</h2>
            <p class="text-neutral-500 text-sm">Sistem kami menghitung parameter secara objektif untuk memberikan rekomendasi trip yang paling sesuai dengan preferensi budget dan fisik Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="p-6 rounded-3xl bg-white border border-neutral-100/80 shadow-lg shadow-neutral-900/5 hover:shadow-xl hover:shadow-emerald-500/10 space-y-4 hover:scale-[1.02] hover:-translate-y-1 transition-all duration-300">
                <div class="h-12 w-12 rounded-2xl shadow-sm bg-gradient-to-br from-emerald-100 to-teal-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <h3 class="text-lg font-bold text-neutral-800">Perhitungan Ilmiah MOORA</h3>
                <p class="text-neutral-500 text-sm leading-relaxed">
                    Menggunakan metode <em>Multi-Objective Optimization on the basis of Ratio Analysis</em> untuk memproses kriteria <em>cost</em> (biaya, waktu, medan) dan <em>benefit</em> (ketinggian, fasilitas) secara berimbang.
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="p-6 rounded-3xl bg-white border border-neutral-100/80 shadow-lg shadow-neutral-900/5 hover:shadow-xl hover:shadow-emerald-500/10 space-y-4 hover:scale-[1.02] hover:-translate-y-1 transition-all duration-300">
                <div class="h-12 w-12 rounded-2xl shadow-sm bg-gradient-to-br from-amber-100 to-orange-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <h3 class="text-lg font-bold text-neutral-800">Preferensi Bobot Kustom</h3>
                <p class="text-neutral-500 text-sm leading-relaxed">
                    Setiap pendaki unik. Anda dapat mengatur preferensi budget, tinggi gunung, medan, dan waktu tempuh Anda sendiri. Sistem akan menyaring dan menghitung kecocokan secara presisi.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="p-6 rounded-3xl bg-white border border-neutral-100/80 shadow-lg shadow-neutral-900/5 hover:shadow-xl hover:shadow-emerald-500/10 space-y-4 hover:scale-[1.02] hover:-translate-y-1 transition-all duration-300">
                <div class="h-12 w-12 rounded-2xl shadow-sm bg-gradient-to-br from-teal-100 to-cyan-50 text-teal-600 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-lg font-bold text-neutral-800">Edukasi Persiapan Khusus</h3>
                <p class="text-neutral-500 text-sm leading-relaxed">
                    Kami membekali Anda dengan panduan logistik wajib dan packing checklist berdasarkan gunung rekomendasi utama yang terpilih setelah evaluasi.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Highlight Paket Termurah -->
<?php if (!empty($cheapestTrip)): ?>
<section class="py-12 bg-gradient-to-b from-white to-emerald-50/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500 rounded-3xl p-1 shadow-2xl overflow-hidden group hover:shadow-amber-500/20 transition-all duration-300">
            <!-- Kontainer Dalam -->
            <div class="bg-white rounded-[22px] p-6 md:p-8 flex flex-col md:flex-row gap-8 items-center relative">
                
                <!-- Badge Penanda -->
                <div class="absolute top-0 right-0 bg-red-600 text-white text-xs font-black px-4 py-2 rounded-bl-2xl rounded-tr-[22px] uppercase tracking-widest shadow-lg z-10 border-b border-l border-red-700">
                    <i class="fa-solid fa-fire mr-1"></i> Termurah Sejauh Ini
                </div>

                <!-- Gambar -->
                <div class="w-full md:w-1/3 aspect-[4/3] rounded-2xl overflow-hidden relative">
                    <?php if (isset($cheapestTrip['status_kuota']) && $cheapestTrip['status_kuota'] === 'Penuh'): ?>
                        <div class="absolute inset-0 bg-neutral-900/40 z-10"></div>
                        <span class="absolute top-4 left-4 inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-red-600 shadow-lg text-white tracking-widest uppercase z-20">
                            Sold Out
                        </span>
                    <?php endif; ?>
                    <img src="<?php echo !empty($cheapestTrip['gambar']) ? htmlspecialchars($cheapestTrip['gambar']) : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80'; ?>" 
                         alt="<?php echo htmlspecialchars($cheapestTrip['nama_paket']); ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-700 <?php echo (isset($cheapestTrip['status_kuota']) && $cheapestTrip['status_kuota'] === 'Penuh') ? 'grayscale-[20%]' : ''; ?>">
                </div>

                <!-- Detail & Fasilitas -->
                <div class="w-full md:w-2/3 space-y-4">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-soil-700 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-campground"></i> Vendor: <?php echo htmlspecialchars($cheapestTrip['vendor_name']); ?>
                        </span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-neutral-800">
                            <?php echo htmlspecialchars($cheapestTrip['nama_paket']); ?>
                        </h3>
                        <p class="text-sm font-bold text-emerald-600">
                            <i class="fa-solid fa-mountain mr-1"></i> Gunung <?php echo htmlspecialchars($cheapestTrip['gunung']); ?>
                        </p>
                    </div>

                    <!-- Menampilkan List Fasilitas -->
                    <div class="pt-2">
                        <p class="text-xs font-bold text-neutral-400 uppercase mb-2">Fasilitas Termasuk:</p>
                        <div class="flex flex-wrap gap-2">
                            <?php 
                            $fasilitas_list = explode(',', $cheapestTrip['c4_fasilitas']);
                            foreach($fasilitas_list as $fas): 
                                if(trim($fas) !== ''):
                            ?>
                                <span class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-semibold border border-emerald-100 flex items-center gap-1.5">
                                    <i class="fa-solid fa-check-circle"></i> <?php echo htmlspecialchars(trim($fas)); ?>
                                </span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>

                    <!-- Harga & CTA -->
                    <div class="pt-4 border-t border-neutral-100 flex flex-wrap items-center justify-between gap-4 mt-2">
                        <div>
                            <span class="block text-[10px] text-neutral-400 font-bold uppercase tracking-wider">Harga Spesial</span>
                            <span class="text-3xl font-black text-amber-600">Rp <?php echo number_format($cheapestTrip['c1_harga'], 0, ',', '.'); ?></span>
                        </div>
                        
                        <?php if (isset($cheapestTrip['status_kuota']) && $cheapestTrip['status_kuota'] === 'Penuh'): ?>
                            <button disabled class="px-6 py-3 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-400 text-sm font-bold cursor-not-allowed">
                                Kuota Habis
                            </button>
                        <?php else: ?>
                            <button onclick='showTripDetail(<?php echo htmlspecialchars(json_encode($cheapestTrip), ENT_QUOTES, 'UTF-8'); ?>)' class="px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 shadow-lg shadow-amber-500/30 text-white text-sm font-bold transition active:scale-95">
                                Lihat Detail Trip
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular Mountain Highlights -->
<section class="py-16 bg-gradient-to-b from-emerald-50/40 via-teal-50/20 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-emerald-800">Paket Trip Unggulan</h2>
                <p class="text-neutral-500 text-sm mt-1">Jelajahi paket trip terpopuler dari mitra vendor terpercaya kami.</p>
            </div>
            <a href="paket.php" class="px-5 py-2.5 rounded-xl border border-emerald-500 text-emerald-600 hover:bg-gradient-to-r hover:from-emerald-500 hover:to-teal-500 hover:text-white hover:border-transparent hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5 transition-all duration-300 font-semibold text-sm">
                Lihat Semua Paket <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if (empty($popularTrips)): ?>
                <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-neutral-100 text-neutral-500 font-medium">
                    <i class="fa-solid fa-mountain text-4xl mb-3 block text-neutral-300"></i>
                    Belum ada paket trip tersedia.
                </div>
            <?php else: ?>
                <?php foreach ($popularTrips as $trip): ?>
                    <!-- TAMBAHAN LOGIKA STYLE: Jika penuh, opacity agak diturunkan -->
                    <div class="bg-white rounded-3xl overflow-hidden border border-neutral-100 shadow-lg shadow-neutral-900/5 hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group <?php echo (isset($trip['status_kuota']) && $trip['status_kuota'] === 'Penuh') ? 'opacity-85 grayscale-[15%]' : ''; ?>">
                        
                        <!-- Trip Image -->
                        <div class="relative overflow-hidden aspect-[16/10]">
                            <!-- TAMBAHAN: Overlay Gelap jika Penuh -->
                            <?php if (isset($trip['status_kuota']) && $trip['status_kuota'] === 'Penuh'): ?>
                                <div class="absolute inset-0 bg-neutral-900/40 z-10"></div>
                            <?php endif; ?>
                            
                            <img src="<?php echo !empty($trip['gambar']) ? htmlspecialchars($trip['gambar']) : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80'; ?>" 
                                 alt="<?php echo htmlspecialchars($trip['nama_paket']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            
                            <span class="absolute top-4 left-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-emerald-600/90 to-teal-500/90 shadow-lg text-white backdrop-blur-sm z-20">
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
                        <div class="p-6 flex-grow flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <span class="text-xs font-bold text-soil-700 uppercase tracking-wider block">
                                    <i class="fa-solid fa-campground mr-1"></i> <?php echo htmlspecialchars($trip['vendor_name']); ?>
                                </span>
                                <h3 class="text-lg font-bold text-neutral-800 line-clamp-1 group-hover:text-forest-800 transition">
                                    <?php echo htmlspecialchars($trip['nama_paket']); ?>
                                </h3>
                                <p class="text-neutral-500 text-sm line-clamp-2 leading-relaxed">
                                    <?php echo htmlspecialchars($trip['deskripsi']); ?>
                                </p>
                            </div>

                            <!-- Criteria Badges -->
                            <div class="grid grid-cols-2 gap-2 text-xs text-neutral-600 bg-neutral-50 p-3 rounded-xl border border-neutral-100">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-up-long text-emerald-600 w-4"></i>
                                    <span><?php echo number_format($trip['c2_ketinggian']); ?> mdpl</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-route text-amber-700 w-4"></i>
                                    <span>Medan: <?php 
                                        if ($trip['c3_medan'] == 1) echo 'Mudah';
                                        elseif ($trip['c3_medan'] == 2) echo 'Sedang';
                                        else echo 'Sulit';
                                    ?></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-kitchen-set text-blue-600 w-4"></i>
                                    <span><?php echo count(explode(',', $trip['c4_fasilitas'])); ?> Fasilitas</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock text-indigo-600 w-4"></i>
                                    <span>Ke Camp: <?php echo $trip['c5_waktu_tempuh']; ?> Jam</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-neutral-100 flex items-center justify-between gap-2">
                                <div>
                                    <span class="block text-[10px] text-neutral-400 font-bold uppercase tracking-wider">Harga Paket</span>
                                    <span class="text-lg font-extrabold text-emerald-700">Rp <?php echo number_format($trip['c1_harga'], 0, ',', '.'); ?></span>
                                </div>
                                
                                <!-- TAMBAHAN: Matikan tombol jika Penuh -->
                                <?php if (isset($trip['status_kuota']) && $trip['status_kuota'] === 'Penuh'): ?>
                                    <button disabled class="px-4 py-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-400 text-xs font-bold cursor-not-allowed">
                                        Kuota Habis
                                    </button>
                                <?php else: ?>
                                    <button onclick='showTripDetail(<?php echo htmlspecialchars(json_encode($trip), ENT_QUOTES, 'UTF-8'); ?>)' class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 shadow-md shadow-emerald-500/20 text-white text-xs font-bold transition active:scale-95">
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

<!-- Call to Action -->
<section class="py-20 bg-gradient-to-br from-emerald-900 via-teal-800 to-emerald-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative z-10 space-y-6">
        <h2 class="text-3xl sm:text-4xl font-extrabold">Sudah Siap Mendapatkan Rekomendasi Terbaik?</h2>
        <p class="text-neutral-200 text-base max-w-xl mx-auto leading-relaxed">
            Daftarkan diri Anda sebagai Pendaki dan gunakan fitur SPK MOORA untuk menyaring paket trip gunung dengan kriteria kustom Anda.
        </p>
        <div class="flex flex-wrap justify-center gap-4 pt-2">
            <?php if ($is_logged_in): ?>
                <?php if ($user_role === 'pendaki'): ?>
                    <a href="pendaki/preferensi_moora.php" class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 shadow-lg shadow-emerald-500/30 text-white font-bold active:scale-95 transition">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1.5"></i> Hitung Rekomendasi MOORA
                    </a>
                <?php else: ?>
                    <a href="<?php echo ($user_role === 'admin') ? 'admin/dashboard.php' : 'vendor/dashboard.php'; ?>" class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 shadow-lg shadow-emerald-500/30 text-white font-bold active:scale-95 transition">
                        <i class="fa-solid fa-gauge mr-1.5"></i> Ke Dashboard Anda
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="auth/register.php" class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 shadow-lg shadow-emerald-500/30 text-white font-bold active:scale-95 transition">
                    Daftar Sekarang
                </a>
                <a href="auth/login.php" class="px-6 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/30 hover:bg-white/20 text-white font-bold active:scale-95 transition">
                    Masuk Akun
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>