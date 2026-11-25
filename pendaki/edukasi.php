<?php
// pendaki/edukasi.php
require_once '../config/database.php';
require_once '../classes/Auth.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('pendaki');

// Tangkap ID Paket dari URL
$paket_id = isset($_GET['paket_id']) ? intval($_GET['paket_id']) : 0;

$paket_info = null;
$materi_edukasi = [];
$db_error = false;
$error_msg = '';

if ($paket_id > 0) {
    try {
        // Ambil info paketnya untuk judul header
        $stmtP = $pdo->prepare("SELECT nama_paket, gunung, vendor_id FROM paket_trip WHERE id = ?");
        $stmtP->execute([$paket_id]);
        $paket_info = $stmtP->fetch(PDO::FETCH_ASSOC);

        // Ambil data dari tabel edukasi berdasarkan paket_id
        $stmt = $pdo->prepare("SELECT * FROM edukasi WHERE paket_id = ? ORDER BY id DESC");
        $stmt->execute([$paket_id]);
        $materi_edukasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $db_error = true;
        $error_msg = $e->getMessage();
    }
}

$current_page = 'edukasi';
$page_title = 'Edukasi & Barang Bawaan - MOORATrip';
$base_path = "../";

include '../includes/header.php';
?>

<!-- Background Blobs -->
<div class="fixed w-[500px] h-[500px] bg-sky-100 rounded-full blur-3xl -top-40 -right-40 opacity-40 pointer-events-none"></div>
<div class="fixed w-[400px] h-[400px] bg-emerald-50 rounded-full blur-3xl -bottom-40 -left-40 opacity-40 pointer-events-none"></div>

<section class="py-12 bg-neutral-50/50 flex-grow relative z-10 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs & Close Button -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 uppercase tracking-wider">
                <a href="../index.php" class="hover:text-forest-800 transition">Beranda</a>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <span class="text-neutral-600">Edukasi & Persiapan</span>
            </div>

            <!-- Tombol Tutup Tab agar kembali ke Moora tanpa Refresh -->
            <button onclick="window.close()" class="px-5 py-2.5 rounded-xl bg-white border border-neutral-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-neutral-700 font-bold text-xs shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-xmark"></i> Tutup Tab Ini
            </button>
        </div>

        <!-- Header Banner -->
        <div class="bg-gradient-to-br from-sky-900 via-sky-800 to-sky-900 rounded-3xl p-8 sm:p-10 text-white shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 text-white pointer-events-none">
                <i class="fa-solid fa-person-hiking text-[200px]"></i>
            </div>
            
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/30 border border-sky-400/30 backdrop-blur-md text-sky-100 font-black text-[10px] uppercase tracking-widest shadow mb-4">
                    <i class="fa-solid fa-shield-halved"></i> Keselamatan & Persiapan
                </div>

                <h1 class="text-3xl sm:text-4xl font-black text-white leading-tight mb-2">
                    Materi Edukasi & Bawaan
                </h1>
                
                <?php if ($paket_info): ?>
                    <p class="text-sky-300 font-bold text-sm sm:text-base mt-1 flex items-center gap-2">
                        <i class="fa-solid fa-box-open"></i> <?php echo htmlspecialchars($paket_info['nama_paket']); ?> (Gunung <?php echo htmlspecialchars($paket_info['gunung']); ?>)
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Content Section -->
        <div class="space-y-6">
            <?php if ($db_error): ?>
                <!-- Pesan Error jika kolom/tabel database bermasalah -->
                <div class="bg-white rounded-3xl p-10 border border-red-100 shadow-lg text-center space-y-4">
                    <div class="h-16 w-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto text-3xl">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-neutral-800">Sistem Gagal Memuat Data</h3>
                    <p class="text-neutral-500 text-sm max-w-md mx-auto">
                        Terdapat kesalahan pada query database.
                    </p>
                    <div class="p-4 bg-neutral-100 rounded-xl text-left text-xs font-mono text-neutral-600 overflow-auto">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                </div>

            <?php elseif (empty($materi_edukasi)): ?>
                <!-- Pesan jika tidak ada edukasi untuk paket ini -->
                <div class="bg-white rounded-3xl p-12 border border-neutral-100 shadow-lg text-center space-y-4">
                    <div class="h-16 w-16 bg-sky-50 text-sky-400 rounded-full flex items-center justify-center mx-auto text-3xl">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-neutral-800">Belum Ada Catatan Vendor</h3>
                    <p class="text-neutral-500 text-sm max-w-md mx-auto leading-relaxed">
                        Pihak vendor belum menambahkan materi edukasi khusus atau daftar barang pribadi wajib untuk paket trip ini.
                    </p>
                </div>

            <?php else: ?>
                
                <!-- Looping Data Edukasi yang dimasukkan Vendor -->
                <?php foreach ($materi_edukasi as $materi): ?>
                    <div class="bg-white rounded-3xl overflow-hidden border border-neutral-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                        
                        <!-- Cek Apakah Ada Judul Edukasi -->
                        <?php if(!empty($materi['judul']) || !empty($materi['konten'])): ?>
                            <div class="p-6 sm:p-8">
                                <h3 class="text-lg font-black text-sky-800 flex items-center gap-2 mb-4 border-b border-neutral-100 pb-3">
                                    <i class="fa-solid fa-graduation-cap text-sky-500"></i>
                                    <?php echo htmlspecialchars($materi['judul'] ?? 'Materi Edukasi'); ?>
                                </h3>
                                <div class="text-sm text-neutral-600 leading-relaxed text-justify">
                                    <?php echo nl2br($materi['konten'] ?? 'Tidak ada deskripsi materi.'); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- MENAMPILKAN BARANG LOGISTIK BERDASARKAN KOLOM DATBASE 'logistik' -->
                        <?php 
                        $barang = trim($materi['logistik'] ?? '');
                        if(!empty($barang)): 
                            // Pecah string logistik menjadi array berdasarkan baris baru (\n) agar bisa dijadikan daftar rapi
                            $list_barang = explode("\n", str_replace("\r", "", $barang));
                        ?>
                            <div class="bg-amber-50/50 p-6 sm:p-8 border-t border-neutral-100">
                                <h4 class="text-sm font-black text-amber-900 flex items-center gap-2 mb-3">
                                    <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> Daftar Barang Logistik (Wajib Bawa):
                                </h4>
                                <ul class="space-y-2 text-sm text-amber-800 font-medium ml-1">
                                    <?php foreach($list_barang as $item_barang): 
                                        $item_clean = trim($item_barang);
                                        if(!empty($item_clean)):
                                    ?>
                                        <li class="flex items-start gap-2">
                                            <i class="fa-solid fa-circle-check text-amber-500 mt-1 text-[10px]"></i>
                                            <span><?php echo htmlspecialchars($item_clean); ?></span>
                                        </li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>