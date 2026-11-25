<?php
// vendor/laporan_terpilih.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Trip.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('vendor');

$vendor = Auth::getUser();
$vendorId = $vendor['id'];
$tripClass = new Trip($pdo);

// Get selected package reports for this vendor
$reports = $tripClass->getVendorSelectedReports($vendorId);

// Calculate summary stats
$totalSelections = count($reports);
$packageCounts = [];
foreach ($reports as $r) {
    $pName = $r['nama_paket'];
    if (!isset($packageCounts[$pName])) {
        $packageCounts[$pName] = 0;
    }
    $packageCounts[$pName]++;
}

$current_page = 'vendor_laporan';
$page_title = 'Statistik Paket Terpilih - Panel Vendor';
$base_path = "../";

include __DIR__ . '/../includes/header.php';
?>

<section class="py-12 bg-neutral-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="dashboard.php" class="hover:text-forest-800 transition">Dashboard Vendor</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Statistik Rekomendasi Terpilih</span>
        </div>

        <!-- Header Banner -->
        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 bg-forest-50 text-forest-800 flex items-center justify-center rounded-xl text-lg font-bold">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-neutral-800">Laporan Paket Terpilih Pendaki</h1>
                </div>
                <p class="text-neutral-500 text-sm">
                    Pantau statistik kinerja paket trip Anda yang berhasil masuk hasil optimasi MOORA dan dipilih oleh para Pendaki.
                </p>
            </div>

            <div class="px-5 py-3 rounded-2xl bg-forest-800 text-white font-extrabold text-xs shadow flex items-center gap-3">
                <span class="text-emerald-300 text-lg"><i class="fa-solid fa-square-check"></i></span>
                <div>
                    <span class="block text-[10px] text-emerald-200 uppercase tracking-wider">Total Terpilih</span>
                    <span class="text-base font-black"><?php echo $totalSelections; ?> Pemilihan</span>
                </div>
            </div>
        </div>

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-md flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <span class="text-xs text-neutral-400 font-bold uppercase tracking-wider block">Total Rekomendasi Terpilih</span>
                    <span class="text-2xl font-black text-neutral-800"><?php echo $totalSelections; ?> Paket</span>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-md flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <div>
                    <span class="text-xs text-neutral-400 font-bold uppercase tracking-wider block">Paket Terfavorit</span>
                    <span class="text-sm font-black text-neutral-800 line-clamp-1">
                        <?php 
                        if (!empty($packageCounts)) {
                            arsort($packageCounts);
                            echo htmlspecialchars(array_key_first($packageCounts)) . " (" . reset($packageCounts) . "x)";
                        } else {
                            echo "-";
                        }
                        ?>
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-md flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-forest-100 text-forest-800 flex items-center justify-center text-xl font-bold">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <span class="text-xs text-neutral-400 font-bold uppercase tracking-wider block">Total Pendaki Unik</span>
                    <span class="text-2xl font-black text-neutral-800">
                        <?php echo count(array_unique(array_column($reports, 'user_id'))); ?> Pendaki
                    </span>
                </div>
            </div>
        </div>

        <!-- Table list of selected reports -->
        <div class="bg-white rounded-3xl border border-neutral-100 shadow-xl overflow-hidden">
            <div class="p-6 border-b border-neutral-100 flex justify-between items-center">
                <h3 class="font-extrabold text-neutral-800 text-base">Riwayat Pemilihan Paket Oleh Pendaki</h3>
                <span class="text-xs font-semibold text-neutral-400">Diperbarui Real-time</span>
            </div>

            <?php if (empty($reports)): ?>
                <div class="p-12 text-center space-y-3">
                    <div class="h-14 w-14 bg-neutral-100 text-neutral-400 rounded-full flex items-center justify-center mx-auto text-xl">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                    <h4 class="font-bold text-neutral-800 text-sm">Belum Ada Pemilihan Paket</h4>
                    <p class="text-neutral-400 text-xs max-w-sm mx-auto">
                        Belum ada pendaki yang memilih paket trip Anda dari rekomendasi MOORA. Pastikan informasi fasilitas & rute Anda lengkap.
                    </p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-neutral-100 text-neutral-700 uppercase font-extrabold border-b border-neutral-200">
                                <th class="p-4">Tanggal Pilih</th>
                                <th class="p-4">Nama Paket Trip</th>
                                <th class="p-4">Gunung</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Pendaki Pemilih</th>
                                <th class="p-4">Skor MOORA (Yi)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 font-medium text-neutral-800">
                            <?php foreach ($reports as $r): ?>
                                <tr class="hover:bg-neutral-50">
                                    <td class="p-4 text-neutral-500 font-semibold"><?php echo date('d M Y, H:i', strtotime($r['created_at'])); ?></td>
                                    <td class="p-4 font-bold text-neutral-800"><?php echo htmlspecialchars($r['nama_paket']); ?></td>
                                    <td class="p-4">Gunung <?php echo htmlspecialchars($r['gunung']); ?></td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full font-extrabold text-[10px] bg-emerald-100 text-emerald-800">
                                            <?php echo htmlspecialchars($r['kategori_pilihan']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4 font-bold text-emerald-900"><?php echo htmlspecialchars($r['pendaki_nama']); ?></td>
                                    <td class="p-4 font-black text-forest-800 text-sm"><?php echo number_format($r['skor_moora'], 4); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
