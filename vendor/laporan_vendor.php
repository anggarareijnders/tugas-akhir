<?php
// vendor/laporan_vendor.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Trip.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('vendor');

$vendor = Auth::getUser();
$vendorId = $vendor['id'];
$tripClass = new Trip($pdo);

// Pure SQL JOIN query for vendor selected package statistics
$query = "SELECT l.id as laporan_id, l.skor_moora, l.kategori_pilihan, l.catatan_fisik, l.created_at as tanggal_pilih,
                 p.id as paket_id, p.nama_paket, p.gunung, p.kategori as kategori_paket, p.c1_harga,
                 u.fullname as pendaki_nama, u.username as pendaki_username
          FROM laporan_terpilih l
          INNER JOIN paket_trip p ON l.paket_id = p.id
          INNER JOIN users u ON l.user_id = u.id
          WHERE p.vendor_id = ?
          ORDER BY l.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$vendorId]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalSelections = count($reports);

$current_page = 'vendor_laporan';
$page_title = 'Laporan Statistik Paket Terpilih - Panel Vendor';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-12 bg-neutral-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="dashboard.php" class="hover:text-forest-800 transition">Dashboard Vendor</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Laporan Paket Terpilih (SQL JOIN)</span>
        </div>

        <!-- Header Banner -->
        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 bg-forest-50 text-forest-800 flex items-center justify-center rounded-xl text-lg font-bold">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-neutral-800">Laporan Statistik Paket Terpilih</h1>
                </div>
                <p class="text-neutral-500 text-sm">
                    Menampilkan hasil SQL JOIN antar tabel <code>laporan_terpilih</code>, <code>paket_trip</code>, dan <code>users</code>.
                </p>
            </div>

            <div class="px-5 py-3 rounded-2xl bg-forest-800 text-white font-extrabold text-xs shadow flex items-center gap-3">
                <span class="text-emerald-300 text-lg"><i class="fa-solid fa-clipboard-check"></i></span>
                <div>
                    <span class="block text-[10px] text-emerald-200 uppercase tracking-wider">Total Terpilih MOORA</span>
                    <span class="text-base font-black"><?php echo $totalSelections; ?> Pemilihan</span>
                </div>
            </div>
        </div>

        <!-- Table List -->
        <div class="bg-white rounded-3xl border border-neutral-100 shadow-xl overflow-hidden">
            <div class="p-6 border-b border-neutral-100 flex justify-between items-center">
                <h3 class="font-extrabold text-neutral-800 text-base">Daftar Rekomendasi Terpilih Milik Anda</h3>
                <span class="text-xs font-bold text-neutral-400">PDO MySQL Query JOIN</span>
            </div>

            <?php if (empty($reports)): ?>
                <div class="p-12 text-center space-y-3">
                    <div class="h-14 w-14 bg-neutral-100 text-neutral-400 rounded-full flex items-center justify-center mx-auto text-xl">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                    <h4 class="font-bold text-neutral-800 text-sm">Belum Ada Pemilihan Paket</h4>
                    <p class="text-neutral-400 text-xs max-w-sm mx-auto">
                        Belum ada pendaki yang menyimpan paket trip Anda.
                    </p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-neutral-100 text-neutral-700 uppercase font-extrabold border-b border-neutral-200">
                                <th class="p-4">Waktu Pemilihan</th>
                                <th class="p-4">Nama Paket Trip</th>
                                <th class="p-4">Gunung</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Nama Pendaki</th>
                                <th class="p-4 text-right">Harga Paket</th>
                                <th class="p-4 text-center">Skor MOORA (Yi)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 font-medium text-neutral-800">
                            <?php foreach ($reports as $r): ?>
                                <tr class="hover:bg-neutral-50">
                                    <td class="p-4 text-neutral-500 font-semibold"><?php echo date('d M Y, H:i', strtotime($r['tanggal_pilih'])); ?></td>
                                    <td class="p-4 font-bold text-neutral-800"><?php echo htmlspecialchars($r['nama_paket']); ?></td>
                                    <td class="p-4">Gunung <?php echo htmlspecialchars($r['gunung']); ?></td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full font-extrabold text-[10px] bg-emerald-100 text-emerald-800">
                                            <?php echo htmlspecialchars($r['kategori_pilihan']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4 font-bold text-emerald-900"><?php echo htmlspecialchars($r['pendaki_nama']); ?></td>
                                    <td class="p-4 text-right font-black">Rp <?php echo number_format($r['c1_harga'], 0, ',', '.'); ?></td>
                                    <td class="p-4 text-center font-black text-forest-800 text-sm"><?php echo number_format($r['skor_moora'], 4); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
