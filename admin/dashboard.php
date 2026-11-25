<?php
// admin/dashboard.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/User.php';
require_once '../classes/Trip.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('admin');

$admin = Auth::getUser();
$userClass = new User($pdo);
$tripClass = new Trip($pdo);

// Fetch statistics
$stats = $userClass->getStats();
$totalTrips = $tripClass->countAll();

$current_page = 'admin_dashboard';
$page_title = 'Dashboard Admin - MOORATrip';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-10 bg-gradient-to-br from-neutral-50 via-emerald-50/10 to-teal-50/10 flex-grow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-emerald-800 tracking-tight">Panel Administrator</h1>
            <p class="text-neutral-500 text-sm mt-1">Selamat datang kembali, <strong class="text-neutral-700"><?php echo htmlspecialchars($admin['fullname']); ?></strong>. Pantau dan kelola seluruh sistem pendukung keputusan MOORA.</p>
        </div>

        <!-- Admin Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Stat 1: Total Pengguna -->
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-6 rounded-3xl border border-neutral-100 shadow-xl shadow-neutral-900/5 ring-1 ring-neutral-100/50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl shadow-sm bg-white/20 text-white flex items-center justify-center text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-white/80 uppercase tracking-wider">Total Akun</span>
                    <span class="text-2xl font-black text-white tracking-tight"><?php echo $stats['total_users']; ?></span>
                </div>
            </div>

            <!-- Stat 2: Total Vendor -->
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white p-6 rounded-3xl border border-neutral-100 shadow-xl shadow-neutral-900/5 ring-1 ring-neutral-100/50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl shadow-sm bg-white/20 text-white flex items-center justify-center text-xl">
                    <i class="fa-solid fa-campground"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-white/80 uppercase tracking-wider">Vendor Mitra</span>
                    <span class="text-2xl font-black text-white tracking-tight"><?php echo $stats['total_vendors']; ?></span>
                </div>
            </div>

            <!-- Stat 3: Total Pendaki -->
            <div class="bg-gradient-to-br from-amber-400 to-orange-500 text-white p-6 rounded-3xl border border-neutral-100 shadow-xl shadow-neutral-900/5 ring-1 ring-neutral-100/50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl shadow-sm bg-white/20 text-white flex items-center justify-center text-xl">
                    <i class="fa-solid fa-person-hiking"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-white/80 uppercase tracking-wider">Pendaki Aktif</span>
                    <span class="text-2xl font-black text-white tracking-tight"><?php echo $stats['total_pendaki']; ?></span>
                </div>
            </div>

            <!-- Stat 4: Total Paket -->
            <div class="bg-gradient-to-br from-rose-500 to-pink-600 text-white p-6 rounded-3xl border border-neutral-100 shadow-xl shadow-neutral-900/5 ring-1 ring-neutral-100/50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl shadow-sm bg-white/20 text-white flex items-center justify-center text-xl">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-white/80 uppercase tracking-wider">Paket Trip</span>
                    <span class="text-2xl font-black text-white tracking-tight"><?php echo $totalTrips; ?></span>
                </div>
            </div>
        </div>

        <!-- Action Card Links Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Link 1: Kelola Akun -->
            <div class="bg-white p-8 rounded-3xl border border-neutral-100 shadow-xl shadow-neutral-900/5 ring-1 ring-neutral-100/50 hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between space-y-6 group">
                <div class="space-y-4">
                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-50 text-emerald-600 flex items-center justify-center text-xl shadow-lg shadow-emerald-500/10 ring-2 ring-emerald-500/10 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <h2 class="text-xl font-extrabold text-neutral-800">Kelola Akun Pengguna</h2>
                    <p class="text-neutral-500 text-xs leading-relaxed">
                        Lihat statistik pendaftaran akun, kelola kemitraan vendor, dan hapus akun pengguna yang melanggar ketentuan layanan.
                    </p>
                </div>
                <!-- PERUBAHAN LINK ADA DI BAWAH INI -->
                <a href="kelola_pengguna.php" class="py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-0.5 transition-all duration-300 text-white font-bold text-xs flex items-center justify-center gap-1.5">
                    Kelola Pengguna <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Link 2: Kelola Preferensi Kriteria -->
            <div class="bg-white p-8 rounded-3xl border border-neutral-100 shadow-xl shadow-neutral-900/5 ring-1 ring-neutral-100/50 hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between space-y-6 group">
                <div class="space-y-4">
                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-50 text-emerald-600 flex items-center justify-center text-xl shadow-lg shadow-emerald-500/10 ring-2 ring-emerald-500/10 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <h2 class="text-xl font-extrabold text-neutral-800">Kelola Preferensi Kriteria</h2>
                    <p class="text-neutral-500 text-xs leading-relaxed">
                        Sesuaikan bobot bawaan kriteria C1 s.d. C5 dan ubah jenis kriteria (Cost atau Benefit) secara dinamis.
                    </p>
                </div>
                <a href="kelola_kriteria.php" class="py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-0.5 transition-all duration-300 text-white font-bold text-xs flex items-center justify-center gap-1.5">
                    Kelola Kriteria & Bobot <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Link 3: Menjalankan Algoritma MOORA -->
            <div class="bg-white p-8 rounded-3xl border border-neutral-100 shadow-xl shadow-neutral-900/5 ring-1 ring-neutral-100/50 hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between space-y-6 group">
                <div class="space-y-4">
                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-50 text-emerald-600 flex items-center justify-center text-xl shadow-lg shadow-emerald-500/10 ring-2 ring-emerald-500/10 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <h2 class="text-xl font-extrabold text-neutral-800">Menjalankan Algoritma MOORA</h2>
                    <p class="text-neutral-500 text-xs leading-relaxed">
                        Validasi dan pemicu manual perhitungan matriks keputusan, normalisasi rasio, terbobot, hingga ranking final MOORA.
                    </p>
                </div>
                <a href="simulasi_moora.php" class="py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-0.5 transition-all duration-300 text-white font-bold text-xs flex items-center justify-center gap-1.5">
                    Jalankan Algoritma MOORA <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>