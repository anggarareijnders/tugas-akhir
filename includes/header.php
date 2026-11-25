<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set base path default if not defined
if (!isset($base_path)) {
    $base_path = "./";
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $is_logged_in ? $_SESSION['role'] : null;
$fullname = $is_logged_in ? $_SESSION['fullname'] : '';
$username = $is_logged_in ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - SPK MOORA Trip" : "Sistem Pendukung Keputusan MOORA - Trip Pendakian"; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: {
                            50: '#f2f8f3',
                            100: '#e1efe4',
                            200: '#c5e0cb',
                            700: '#2b5f33',
                            800: '#1e4620',
                            900: '#163318',
                        },
                        soil: {
                            50: '#f9f6f0',
                            100: '#f3ede1',
                            700: '#8c6239',
                            800: '#5c3f21',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-neutral-50 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-xl sticky top-0 z-50 border-b border-neutral-200/50 shadow-lg shadow-neutral-900/5 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 lg:h-20">
                <!-- Left: Logo & Nav Links -->
                <div class="flex items-center gap-8">
                    <a href="<?php echo $base_path; ?>index.php" class="flex items-center gap-3 group">
                        <div class="flex items-center justify-center h-11 w-11 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-500 text-white text-xl shadow-lg shadow-emerald-500/30 group-hover:scale-105 transition-transform ring-2 ring-forest-800/10">
                            <i class="fa-solid fa-mountain"></i>
                        </div>
                        <div>
                            <span class="text-lg font-extrabold text-neutral-800 block leading-tight tracking-tight">MOORA<span class="text-emerald-600">Trip</span></span>
                            <span class="text-[10px] text-neutral-400 font-medium tracking-wide uppercase">SPK Pendakian Gunung</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Menu -->
                    <div class="hidden md:flex items-center gap-1">
                        
                        <!-- Sembunyikan Beranda dan Paket Trip Khusus untuk Admin -->
                        <?php if ($user_role !== 'admin'): ?>
                            <a href="<?php echo $base_path; ?>index.php" class="px-4 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'home') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-house mr-1.5"></i> Beranda
                            </a>
                            <a href="<?php echo $base_path; ?>paket.php" class="px-4 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'paket') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-compass mr-1.5"></i> Paket Trip
                            </a>
                        <?php endif; ?>

                        <?php if ($is_logged_in && $user_role === 'pendaki'): ?>
                            <a href="<?php echo $base_path; ?>pendaki/preferensi_moora.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'preferensi' || $current_page === 'rekomendasi') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-wand-magic-sparkles mr-1.5 text-forest-700"></i> Hitung SPK
                            </a>
                            <a href="<?php echo $base_path; ?>pendaki/riwayat_pendaki.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'riwayat_pendaki' || $current_page === 'laporan_terpilih') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-clock-rotate-left mr-1.5 text-emerald-700"></i> Riwayat Terpilih
                            </a>
                        <?php elseif ($is_logged_in && $user_role === 'vendor'): ?>
                            <a href="<?php echo $base_path; ?>vendor/dashboard.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'vendor_dashboard' || $current_page === 'vendor_paket') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-campground mr-1.5 text-forest-700"></i> Dashboard
                            </a>
                            <a href="<?php echo $base_path; ?>vendor/laporan_vendor.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'vendor_laporan') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-chart-line mr-1.5 text-emerald-700"></i> Laporan Terpilih
                            </a>
                        <?php elseif ($is_logged_in && $user_role === 'admin'): ?>
                            <a href="<?php echo $base_path; ?>admin/dashboard.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'admin_dashboard') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-gauge mr-1.5"></i> Dashboard
                            </a>
                            <a href="<?php echo $base_path; ?>admin/simulasi_moora.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'admin_simulasi') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-calculator mr-1.5 text-forest-700"></i> Jalankan MOORA
                            </a>
                            <a href="<?php echo $base_path; ?>admin/kelola_kriteria.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'admin_kriteria') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-sliders mr-1.5"></i> Kriteria
                            </a>
                            <a href="<?php echo $base_path; ?>admin/kelola_pengguna.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition duration-200 <?php echo ($current_page === 'admin_pengguna') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                                <i class="fa-solid fa-users mr-1.5"></i> Pengguna
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right: Auth Action or Dashboard Navigation -->
                <div class="hidden md:flex items-center gap-3">
                    <?php if ($is_logged_in): ?>
                        <div class="flex items-center gap-3 border-l pl-4 border-neutral-100">
                            <div class="text-right">
                                <span class="block text-sm font-bold text-neutral-800"><?php echo htmlspecialchars($fullname); ?></span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?php 
                                    if ($user_role === 'admin') echo 'bg-gradient-to-r from-red-500 to-rose-500 text-white border-0 shadow-sm shadow-red-500/20';
                                    elseif ($user_role === 'vendor') echo 'bg-gradient-to-r from-amber-400 to-orange-500 text-white border-0 shadow-sm shadow-amber-500/20';
                                    else echo 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-0 shadow-sm shadow-emerald-500/20';
                                ?>">
                                    <?php echo $user_role; ?>
                                </span>
                            </div>
                            
                            <?php if ($user_role === 'admin'): ?>
                                <a href="<?php echo $base_path; ?>admin/profil_admin.php" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-neutral-100 text-neutral-700 hover:bg-emerald-50 hover:text-emerald-700 border transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-gear"></i> Profil
                                </a>
                            <?php else: ?>
                                <a href="<?php echo $base_path; ?>auth/kelola_akun.php" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-neutral-100 text-neutral-700 hover:bg-emerald-50 hover:text-emerald-700 border transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-gear"></i> Kelola Akun
                                </a>
                            <?php endif; ?>

                            <a href="<?php echo $base_path; ?>auth/logout.php" class="p-2.5 rounded-xl text-neutral-400 hover:text-red-500 hover:bg-red-50 hover:shadow-md hover:shadow-red-500/10 transition" title="Log Out">
                                <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $base_path; ?>auth/login.php" class="px-5 py-2.5 rounded-xl text-sm font-bold text-neutral-700 hover:bg-neutral-50 transition">
                            Masuk
                        </a>
                        <a href="<?php echo $base_path; ?>auth/register.php" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-0.5 transition">
                            Daftar Akun
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button type="button" onclick="toggleMobileMenu()" class="p-2.5 rounded-xl text-neutral-600 hover:bg-neutral-50 hover:text-neutral-800 focus:outline-none transition">
                        <i id="menuIcon" class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-neutral-100 bg-white/95 backdrop-blur-xl">
            <div class="px-2 pt-2 pb-4 space-y-1">
                
                <!-- Sembunyikan Beranda dan Paket Trip Khusus untuk Admin (Mobile View) -->
                <?php if ($user_role !== 'admin'): ?>
                    <a href="<?php echo $base_path; ?>index.php" class="block px-4 py-2.5 rounded-xl text-base font-semibold <?php echo ($current_page === 'home') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                        <i class="fa-solid fa-house mr-2 text-sm"></i> Beranda
                    </a>
                    <a href="<?php echo $base_path; ?>paket.php" class="block px-4 py-2.5 rounded-xl text-base font-semibold <?php echo ($current_page === 'paket') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                        <i class="fa-solid fa-compass mr-2 text-sm"></i> Paket Trip
                    </a>
                <?php endif; ?>

                <?php if ($is_logged_in && $user_role === 'pendaki'): ?>
                    <a href="<?php echo $base_path; ?>pendaki/preferensi_moora.php" class="block px-4 py-2.5 rounded-xl text-base font-semibold <?php echo ($current_page === 'preferensi' || $current_page === 'rekomendasi') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                        <i class="fa-solid fa-wand-magic-sparkles mr-2 text-sm"></i> Cari Rekomendasi
                    </a>
                <?php elseif ($is_logged_in && $user_role === 'vendor'): ?>
                    <a href="<?php echo $base_path; ?>vendor/dashboard.php" class="block px-4 py-2.5 rounded-xl text-base font-semibold <?php echo ($current_page === 'vendor_dashboard' || $current_page === 'vendor_paket' || $current_page === 'vendor_edukasi') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                        <i class="fa-solid fa-campground mr-2 text-sm"></i> Dashboard Vendor
                    </a>
                <?php elseif ($is_logged_in && $user_role === 'admin'): ?>
                    <a href="<?php echo $base_path; ?>admin/dashboard.php" class="block px-4 py-2.5 rounded-xl text-base font-semibold <?php echo ($current_page === 'admin_dashboard' || $current_page === 'admin_akun' || $current_page === 'admin_kriteria') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                        <i class="fa-solid fa-gauge mr-2 text-sm"></i> Panel Admin
                    </a>
                    <a href="<?php echo $base_path; ?>admin/kelola_pengguna.php" class="block px-4 py-2.5 rounded-xl text-base font-semibold <?php echo ($current_page === 'admin_pengguna') ? 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700' : 'text-neutral-600 hover:bg-emerald-50/50 hover:text-emerald-700'; ?>">
                        <i class="fa-solid fa-users mr-2 text-sm"></i> Kelola Pengguna
                    </a>
                <?php endif; ?>

                <div class="border-t border-neutral-100 my-2 pt-2"></div>

                <?php if ($is_logged_in): ?>
                    <div class="px-4 py-2.5 flex items-center justify-between">
                        <div>
                            <span class="block text-sm font-bold text-neutral-800"><?php echo htmlspecialchars($fullname); ?></span>
                            <span class="text-xs text-neutral-400 capitalize"><?php echo $user_role; ?></span>
                        </div>
                        
                        <?php if ($user_role === 'admin'): ?>
                            <a href="<?php echo $base_path; ?>admin/profil_admin.php" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-neutral-100 text-neutral-700 hover:bg-emerald-50 hover:text-emerald-700 border transition">
                                <i class="fa-solid fa-user-gear mr-1"></i> Profil
                            </a>
                        <?php else: ?>
                            <a href="<?php echo $base_path; ?>auth/kelola_akun.php" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-neutral-100 text-neutral-700 hover:bg-emerald-50 hover:text-emerald-700 border transition">
                                <i class="fa-solid fa-user-gear mr-1"></i> Akun
                            </a>
                        <?php endif; ?>

                    </div>

                    <a href="<?php echo $base_path; ?>auth/logout.php" class="block px-4 py-2.5 rounded-xl text-base font-semibold text-red-600 hover:text-red-500 hover:bg-red-50 hover:shadow-md hover:shadow-red-500/10">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Keluar
                    </a>
                <?php else: ?>
                    <div class="grid grid-cols-2 gap-2 p-2">
                        <a href="<?php echo $base_path; ?>auth/login.php" class="flex justify-center items-center py-2.5 px-4 rounded-xl text-base font-bold text-neutral-700 border border-neutral-200">
                            Masuk
                        </a>
                        <a href="<?php echo $base_path; ?>auth/register.php" class="flex justify-center items-center py-2.5 px-4 rounded-xl text-base font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-0.5">
                            Daftar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Script toggle mobile menu -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const icon = document.getElementById('menuIcon');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                menu.classList.add('hidden');
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        }
    </script>