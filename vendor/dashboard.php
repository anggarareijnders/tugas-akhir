<?php
// vendor/dashboard.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Trip.php';
require_once '../classes/Education.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('vendor');

$vendor = Auth::getUser();
$vendorId = $vendor['id'];

$tripClass = new Trip($pdo);
$eduClass = new Education($pdo);

// Get vendor specific data
$myTrips = $tripClass->getAll('', $vendorId);
$myEdus = $eduClass->getAll('', $vendorId);
$myReports = $tripClass->getVendorSelectedReports($vendorId);

$totalTrips = count($myTrips);
$totalEdus = count($myEdus);
$totalReports = count($myReports);

$current_page = 'vendor_dashboard';
$page_title = 'Vendor Dashboard - MOORATrip';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-12 bg-neutral-50 flex-grow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- POP-UP / ALERT NOTIFIKASI AKSI KELOLA PAKET & EDUKASI -->
        <?php if (isset($_GET['status']) || isset($_GET['error'])): ?>
            <div id="alert-popup" class="mb-6 p-4 <?php echo isset($_GET['error']) ? 'bg-red-50 border-red-500 text-red-800' : 'bg-emerald-50 border-emerald-500 text-emerald-800'; ?> border-l-4 rounded-r-xl flex items-center justify-between shadow-sm transition-all duration-300">
                <div class="flex items-center gap-3">
                    <i class="fa-solid <?php echo isset($_GET['error']) ? 'fa-circle-xmark text-red-600' : 'fa-circle-check text-emerald-600'; ?> text-lg"></i>
                    <span class="text-sm font-bold">
                        <?php 
                            if (isset($_GET['status'])) {
                                $status = $_GET['status'];
                                // Status untuk Paket Trip
                                if ($status === 'added') echo "Berhasil! Paket trip baru sukses ditambahkan ke sistem.";
                                elseif ($status === 'updated') echo "Berhasil! Data atau status paket trip sukses diperbarui.";
                                elseif ($status === 'deleted') echo "Berhasil! Paket trip telah dihapus dari sistem.";
                                
                                // Status untuk Edukasi Logistik
                                elseif ($status === 'edu_added') echo "Berhasil! Panduan edukasi logistik sukses ditambahkan.";
                                elseif ($status === 'edu_updated') echo "Berhasil! Panduan edukasi logistik sukses diperbarui.";
                                elseif ($status === 'edu_deleted') echo "Berhasil! Panduan edukasi logistik telah dihapus dari sistem.";
                            } elseif (isset($_GET['error'])) {
                                $error = $_GET['error'];
                                if ($error === 'gagal_update') echo "Gagal! Terjadi kesalahan saat mengubah status kuota paket.";
                            }
                        ?>
                    </span>
                </div>
                <button onclick="document.getElementById('alert-popup').remove()" class="<?php echo isset($_GET['error']) ? 'text-red-700 hover:text-red-900' : 'text-emerald-700 hover:text-emerald-900'; ?> font-bold text-xs p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Script otomatis menghilangkan pop-up setelah 4 detik -->
            <script>
                setTimeout(() => {
                    const alert = document.getElementById('alert-popup');
                    if (alert) alert.remove();
                }, 4000);
            </script>
        <?php endif; ?>

        <!-- Welcome Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-neutral-800">Panel Vendor</h1>
                <p class="text-neutral-500 text-sm mt-1">Selamat datang kembali, <strong class="text-neutral-700"><?php echo htmlspecialchars($vendor['fullname']); ?></strong>. Kelola paket trip, segmentasi pos, dan pantau statistik pemesanan.</p>
            </div>
            <div class="flex flex-wrap gap-3 w-full sm:w-auto">
                <a href="laporan_terpilih.php" class="px-4 py-2.5 rounded-xl text-emerald-800 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 font-bold text-xs flex items-center justify-center gap-1.5 transition">
                    <i class="fa-solid fa-chart-line"></i> Statistik Terpilih (<?php echo $totalReports; ?>)
                </a>
                <a href="kelola_paket.php?action=add" class="px-4 py-2.5 rounded-xl text-white bg-forest-800 hover:bg-forest-900 shadow font-bold text-xs flex items-center justify-center gap-1.5 transition">
                    <i class="fa-solid fa-plus"></i> Tambah Paket
                </a>
                <a href="kelola_edukasi.php?action=add" class="px-4 py-2.5 rounded-xl text-forest-800 bg-white border border-forest-850 hover:bg-forest-50 font-bold text-xs flex items-center justify-center gap-1.5 transition">
                    <i class="fa-solid fa-plus"></i> Tambah Edukasi
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <!-- Stat 1 -->
            <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-forest-50 text-forest-800 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-neutral-400 uppercase tracking-wider">Paket Trip Aktif</span>
                    <span class="text-2xl font-black text-neutral-800"><?php echo $totalTrips; ?></span>
                </div>
            </div>

            <!-- Stat 2 -->
            <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-neutral-400 uppercase tracking-wider">Direkomendasikan & Terpilih</span>
                    <span class="text-2xl font-black text-emerald-800"><?php echo $totalReports; ?></span>
                </div>
            </div>

            <!-- Stat 3 -->
            <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-soil-50 text-soil-700 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-neutral-400 uppercase tracking-wider">Artikel Edukasi Logistik</span>
                    <span class="text-2xl font-black text-neutral-800"><?php echo $totalEdus; ?></span>
                </div>
            </div>
        </div>

        <!-- Vendor Packages Grid -->
        <div class="bg-white rounded-3xl border border-neutral-100 shadow-xl overflow-hidden mb-10">
            <div class="p-6 border-b border-neutral-100 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-neutral-800">Daftar Paket Trip Anda</h2>
                    <p class="text-neutral-500 text-xs">Paket trip pendakian aktif (Camping & Tektok) yang dinilai oleh algoritma MOORA.</p>
                </div>
                <a href="kelola_paket.php" class="text-xs font-extrabold text-forest-800 hover:underline">Kelola Semua <i class="fa-solid fa-angle-right text-[10px]"></i></a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-neutral-50 border-b border-neutral-100 text-[10px] font-extrabold uppercase tracking-wider text-neutral-400">
                            <th class="py-4 px-6">Paket Trip & Status</th>
                            <th class="py-4 px-6">Kategori</th>
                            <th class="py-4 px-6">Gunung</th>
                            <th class="py-4 px-6 text-right">Harga Paket (C1)</th>
                            <th class="py-4 px-6 text-right">Ketinggian (C2)</th>
                            <th class="py-4 px-6 text-center">Medan (C3)</th>
                            <th class="py-4 px-6 text-center">Fasilitas (C4)</th>
                            <th class="py-4 px-6 text-center">Waktu (C5)</th>
                            <th class="py-4 px-6 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        <?php if (empty($myTrips)): ?>
                            <tr>
                                <td colspan="9" class="py-8 text-center text-neutral-450">Belum ada paket trip yang diunggah.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($myTrips as $trip): ?>
                                <tr class="hover:bg-neutral-50/50 transition">
                                    <td class="py-4 px-6">
                                        <span class="font-bold text-neutral-800 block mb-1"><?php echo htmlspecialchars($trip['nama_paket']); ?></span>
                                        <?php if (isset($trip['status_kuota']) && $trip['status_kuota'] === 'Penuh'): ?>
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700 uppercase"><i class="fa-solid fa-ban mr-1"></i>Penuh / Sold Out</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-700 uppercase"><i class="fa-solid fa-check mr-1"></i>Tersedia</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                            <?php echo htmlspecialchars($trip['kategori'] ?? 'Camping'); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-neutral-600 font-medium"><?php echo htmlspecialchars($trip['gunung']); ?></td>
                                    <td class="py-4 px-6 text-right font-semibold text-neutral-800">Rp <?php echo number_format($trip['c1_harga'], 0, ',', '.'); ?></td>
                                    <td class="py-4 px-6 text-right font-semibold text-neutral-800"><?php echo number_format($trip['c2_ketinggian']); ?> mdpl</td>
                                    <td class="py-4 px-6 text-center text-neutral-700 font-medium">
                                        <?php 
                                            if ($trip['c3_medan'] == 1) echo 'Mudah';
                                            elseif ($trip['c3_medan'] == 2) echo 'Sedang';
                                            else echo 'Sulit';
                                        ?>
                                    </td>
                                    <td class="py-4 px-6 text-center text-neutral-700 font-semibold" title="<?php echo htmlspecialchars($trip['c4_fasilitas']); ?>">
                                        <?php echo count(explode(',', $trip['c4_fasilitas'])); ?> Item
                                    </td>
                                    <td class="py-4 px-6 text-center text-neutral-700 font-medium"><?php echo $trip['c5_waktu_tempuh']; ?> Jam</td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex gap-1.5 justify-center">
                                            
                                            <!-- TAMBAHAN: Tombol Toggle Ubah Status Kuota -->
                                            <?php
                                            $toggle_status = (isset($trip['status_kuota']) && $trip['status_kuota'] == 'Tersedia') ? 'Penuh' : 'Tersedia';
                                            $btn_bg = (isset($trip['status_kuota']) && $trip['status_kuota'] == 'Tersedia') ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100';
                                            $btn_icon = (isset($trip['status_kuota']) && $trip['status_kuota'] == 'Tersedia') ? 'fa-ban' : 'fa-check';
                                            $btn_text = (isset($trip['status_kuota']) && $trip['status_kuota'] == 'Tersedia') ? 'Tandai Penuh' : 'Buka Kuota';
                                            ?>
                                            <a href="proses_ubah_status.php?id=<?php echo $trip['id']; ?>&status=<?php echo $toggle_status; ?>" 
                                               onclick="return confirm('Apakah Anda yakin ingin mengubah status kuota paket ini menjadi <?php echo $toggle_status; ?>?');"
                                               class="p-2 rounded-lg <?php echo $btn_bg; ?> transition" title="<?php echo $btn_text; ?>">
                                                <i class="fa-solid <?php echo $btn_icon; ?>"></i>
                                            </a>

                                            <!-- Tombol Edit & Hapus Lama -->
                                            <a href="kelola_paket.php?action=edit&id=<?php echo $trip['id']; ?>" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Edit">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a href="kelola_paket.php?action=delete&id=<?php echo $trip['id']; ?>" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus paket trip ini?');" title="Hapus">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Vendor Education Grid -->
        <div class="bg-white rounded-3xl border border-neutral-100 shadow-xl overflow-hidden">
            <div class="p-6 border-b border-neutral-100 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-neutral-800">Daftar Panduan Edukasi & Logistik</h2>
                    <p class="text-neutral-500 text-xs">Artikel edukasi persiapan logistik pendakian yang terhubung ke gunung tertentu.</p>
                </div>
                <a href="kelola_edukasi.php" class="text-xs font-extrabold text-forest-800 hover:underline">Kelola Semua <i class="fa-solid fa-angle-right text-[10px]"></i></a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-neutral-50 border-b border-neutral-100 text-[10px] font-extrabold uppercase tracking-wider text-neutral-400">
                            <th class="py-4 px-6">Judul Panduan</th>
                            <th class="py-4 px-6">Gunung</th>
                            <th class="py-4 px-6">Terkait Paket</th>
                            <th class="py-4 px-6">Tanggal Upload</th>
                            <th class="py-4 px-6 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        <?php if (empty($myEdus)): ?>
                            <tr>
                                <td colspan="5" class="py-8 text-center text-neutral-450">Belum ada artikel edukasi yang diunggah.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($myEdus as $edu): ?>
                                <tr class="hover:bg-neutral-50/50 transition">
                                    <td class="py-4 px-6 font-bold text-neutral-800"><?php echo htmlspecialchars($edu['judul']); ?></td>
                                    <td class="py-4 px-6 text-neutral-600 font-medium"><?php echo htmlspecialchars($edu['gunung']); ?></td>
                                    <td class="py-4 px-6 text-neutral-500">
                                        <?php echo !empty($edu['nama_paket']) ? htmlspecialchars($edu['nama_paket']) : '<span class="text-neutral-300">-</span>'; ?>
                                    </td>
                                    <td class="py-4 px-6 text-neutral-400 text-xs"><?php echo date("d M Y H:i", strtotime($edu['created_at'])); ?></td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex gap-2 justify-center">
                                            <a href="kelola_edukasi.php?action=edit&id=<?php echo $edu['id']; ?>" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Edit">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a href="kelola_edukasi.php?action=delete&id=<?php echo $edu['id']; ?>" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus artikel edukasi ini?');" title="Hapus">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>