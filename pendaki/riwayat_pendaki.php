<?php
// pendaki/riwayat_pendaki.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Trip.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('pendaki');

$user = Auth::getUser();
$tripClass = new Trip($pdo);

$successMsg = '';
$errorMsg = '';

// Handle save package action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_selected') {
    $paketId = isset($_POST['paket_id']) ? intval($_POST['paket_id']) : 0;
    $skorMoora = isset($_POST['skor_moora']) ? floatval($_POST['skor_moora']) : 0;
    $kategori = !empty($_POST['kategori_pilihan']) ? $_POST['kategori_pilihan'] : 'Camping';
    $catatanFisik = isset($_POST['catatan_fisik']) ? trim($_POST['catatan_fisik']) : '';

    if ($paketId > 0 && isset($user['id'])) {
        $res = $tripClass->saveSelectedReport($user['id'], $paketId, $skorMoora, $kategori, $catatanFisik);
        if (is_array($res)) {
            if ($res['success']) {
                $successMsg = $res['message'];
            } else {
                $errorMsg = $res['message'];
            }
        } elseif ($res) {
            $successMsg = "Paket trip berhasil disimpan ke dalam Riwayat Paket Terpilih Anda!";
        } else {
            $errorMsg = "Gagal menyimpan paket trip ke dalam riwayat. Terjadi kesalahan pada database.";
        }
    } else {
        $errorMsg = "Data paket trip tidak valid atau Anda belum login sebagai Pendaki.";
    }
}

// Get user selected reports
$reports = $tripClass->getPendakiSelectedReports($user['id']);

$current_page = 'riwayat_pendaki';
$page_title = 'Riwayat Paket Terpilih - MOORATrip';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-12 bg-neutral-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="../index.php" class="hover:text-forest-800 transition">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Riwayat Paket Terpilih</span>
        </div>

        <!-- Header Title -->
        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 bg-emerald-100 text-emerald-800 flex items-center justify-center rounded-xl text-lg font-bold">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-neutral-800">Riwayat Paket Terpilih Pendaki</h1>
                </div>
                <p class="text-neutral-500 text-sm">
                    Daftar paket trip final yang telah Anda pilih dari hasil kalkulasi algoritma SPK MOORA.
                </p>
            </div>

            <a href="preferensi_moora.php" class="px-5 py-3 rounded-xl bg-forest-800 hover:bg-forest-900 text-white font-bold text-xs flex items-center gap-2 transition shadow">
                <i class="fa-solid fa-plus"></i> Hitung SPK Baru
            </a>
        </div>

        <?php if (!empty($successMsg)): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                <span><?php echo htmlspecialchars($successMsg); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm font-semibold flex items-center gap-3">
                <i class="fa-solid fa-circle-xmark text-red-600 text-xl"></i>
                <span><?php echo htmlspecialchars($errorMsg); ?></span>
            </div>
        <?php endif; ?>

        <!-- Reports Grid -->
        <?php if (empty($reports)): ?>
            <div class="bg-white rounded-3xl p-12 border border-neutral-100 shadow-sm text-center space-y-4">
                <div class="h-16 w-16 bg-neutral-100 text-neutral-400 rounded-full flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3 class="text-lg font-bold text-neutral-800">Belum Ada Riwayat Terpilih</h3>
                <p class="text-neutral-500 text-xs max-w-md mx-auto">
                    Anda belum menyimpan rekomendasi paket trip dari SPK MOORA. Silakan jalankan perhitungan preferensi untuk memilih paket ideal Anda.
                </p>
                <a href="preferensi_moora.php" class="inline-block px-6 py-2.5 rounded-xl bg-forest-800 text-white text-xs font-bold shadow hover:bg-forest-900 transition">
                    Mulai Hitung SPK
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($reports as $r): 
                    $segments = $tripClass->getRuteSegments($r['paket_id']);
                    $contacts = $tripClass->getKontakDarurat($r['paket_id']);
                ?>
                    <div class="bg-white rounded-3xl border border-neutral-100 shadow-lg overflow-hidden flex flex-col justify-between hover:border-emerald-200 transition group">
                        <div>
                            <div class="relative h-44 overflow-hidden bg-neutral-200">
                                <img src="<?php echo htmlspecialchars($r['gambar'] ? $base_path . $r['gambar'] : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80'); ?>" 
                                alt="<?php echo htmlspecialchars($r['nama_paket']); ?>" 
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <div class="absolute top-3 left-3">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-600 text-white shadow uppercase tracking-wider">
                                        <?php echo htmlspecialchars($r['kategori_pilihan']); ?>
                                    </span>
                                </div>
                                <div class="absolute bottom-3 right-3">
                                    <span class="px-3 py-1 rounded-xl text-xs font-black bg-forest-900/90 text-emerald-300 backdrop-blur-sm border border-emerald-500/30">
                                        Skor Yi: <?php echo number_format($r['skor_moora'], 4); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="p-6 space-y-4">
                                <div>
                                    <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest block">
                                        Gunung <?php echo htmlspecialchars($r['gunung']); ?>
                                    </span>
                                    <h3 class="font-extrabold text-neutral-800 text-base leading-snug">
                                        <?php echo htmlspecialchars($r['nama_paket']); ?>
                                    </h3>
                                    <p class="text-xs text-neutral-500 font-semibold mt-1">
                                        Vendor: <?php echo htmlspecialchars($r['vendor_name']); ?>
                                    </p>
                                </div>

                                <div class="p-3 rounded-2xl bg-neutral-50 border border-neutral-100 flex justify-between items-center text-xs">
                                    <span class="text-neutral-500 font-medium">Harga:</span>
                                    <span class="font-black text-forest-800 text-sm">Rp <?php echo number_format($r['c1_harga'], 0, ',', '.'); ?></span>
                                </div>

                                <?php if (!empty($r['catatan_fisik'])): ?>
                                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 flex items-start gap-2">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
                                        <span><?php echo htmlspecialchars($r['catatan_fisik']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Emergency contacts summary -->
                                <?php if (!empty($contacts)): ?>
                                    <div class="pt-2 border-t border-neutral-100 space-y-1">
                                        <span class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-wider block">Kontak Darurat / Porter:</span>
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach ($contacts as $c): ?>
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-[10px] font-bold text-emerald-800 border border-emerald-200">
                                                    <?php echo htmlspecialchars($c['nama']); ?> (<?php echo htmlspecialchars($c['no_hp']); ?>)
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="p-6 pt-0 border-t border-neutral-100 text-right">
                            <span class="text-[10px] text-neutral-400 font-semibold block pt-2">
                                Disimpan: <?php echo date('d M Y, H:i', strtotime($r['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
