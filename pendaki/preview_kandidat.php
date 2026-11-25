<?php
// pendaki/preview_kandidat.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Trip.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('pendaki');

// Mencegah akses langsung via URL (harus dari form submit)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: preferensi_moora.php");
    exit();
}

// Tangkap filter dasar
$rawKategori = isset($_POST['kategori_trip']) ? trim($_POST['kategori_trip']) : '';
$kategoriTrip = (!empty($rawKategori) && $rawKategori !== 'Semua') ? $rawKategori : null;
$targetHarga = isset($_POST['target_c1']) ? floatval($_POST['target_c1']) : 0;

// Ambil data paket trip dari database (Filter Kategori)
$tripClass = new Trip($pdo);
$kandidatAwal = $tripClass->getAll('', null, $kategoriTrip);

// Lakukan filter toleransi harga (Opsional agar tidak terlalu melenceng jauh dari budget)
// Misal: Tampilkan paket yang harganya maksimal 50% di atas target budget pendaki
$kandidatFiltered = [];
foreach ($kandidatAwal as $trip) {
    // Kita pastikan harga ada, kalau tidak ada anggap 0
    $harga = isset($trip['c1_harga']) ? floatval($trip['c1_harga']) : 0;
    
    if ($targetHarga > 0) {
        if ($harga <= ($targetHarga * 1.5)) {
            $kandidatFiltered[] = $trip;
        }
    } else {
        $kandidatFiltered[] = $trip;
    }
}

// Jika filter harga membuat data kosong (budget terlalu kecil), kembalikan semua data berdasarkan kategori agar MOORA tetap bisa memilih yang "mendekati"
if (count($kandidatFiltered) === 0) {
    $kandidatFiltered = $kandidatAwal;
}

$current_page = 'preferensi'; // Tetap aktif di menu preferensi
$page_title = 'Kandidat Paket Trip - MOORATrip';
$base_path = "../";

include '../includes/header.php';
?>

<!-- Loading Simulation Overlay untuk MOORA -->
<div id="loadingOverlayMoora" class="fixed inset-0 bg-forest-900/95 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="text-center space-y-6 max-w-md w-full px-6">
        <div class="relative mx-auto h-24 w-24">
            <div class="absolute inset-0 rounded-full border-4 border-emerald-500/20 border-t-emerald-400 animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center text-white text-3xl">
                <i class="fa-solid fa-microchip animate-pulse text-emerald-300"></i>
            </div>
        </div>
        <div class="space-y-2">
            <h3 class="text-xl font-bold text-white tracking-wide">Menghitung Matriks MOORA</h3>
            <p id="loadingMessageMoora" class="text-emerald-300 text-sm h-6 font-semibold">Menginisialisasi bobot preferensi...</p>
        </div>
        <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
            <div id="loadingBarMoora" class="bg-emerald-400 h-full w-0 transition-all duration-300 ease-out"></div>
        </div>
    </div>
</div>

<!-- Background Blobs -->
<div class="fixed w-[400px] h-[400px] bg-emerald-100 rounded-full blur-3xl -top-20 -left-20 opacity-40 pointer-events-none"></div>

<section class="py-12 bg-neutral-50/50 flex-grow relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="../index.php" class="hover:text-forest-800 transition">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a href="preferensi_moora.php" class="hover:text-forest-800 transition">Preferensi SPK</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Preview Kandidat</span>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl mb-8 space-y-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-neutral-100 pb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-neutral-800">
                        Kandidat Paket Ditemukan
                    </h1>
                    <p class="text-neutral-500 text-sm mt-1">
                        Sistem menemukan <strong><?php echo count($kandidatFiltered); ?> paket trip</strong> awal yang relevan dengan filter kategori (<?php echo $kategoriTrip ? htmlspecialchars($kategoriTrip) : 'Semua'; ?>) dan jangkauan budget Anda.
                    </p>
                </div>
                <div class="px-4 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation animate-pulse"></i> Belum diurutkan oleh MOORA
                </div>
            </div>

            <?php if (count($kandidatFiltered) == 0): ?>
                <div class="py-12 text-center space-y-4">
                    <div class="text-neutral-300 text-6xl"><i class="fa-solid fa-box-open"></i></div>
                    <h3 class="text-lg font-bold text-neutral-700">Tidak ada paket yang sesuai kriteria dasar.</h3>
                    <p class="text-sm text-neutral-500">Silakan kembali dan ubah filter kategori trip Anda.</p>
                    <a href="preferensi_moora.php" class="inline-block mt-4 px-6 py-2.5 bg-neutral-800 text-white rounded-xl text-xs font-bold hover:bg-neutral-900 transition">Kembali ke Preferensi</a>
                </div>
            <?php else: ?>
                
                <!-- GRID KANDIDAT PAKET -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                    <?php foreach($kandidatFiltered as $trip): 
                        // Mencegah error 'undefined index' dengan menggunakan fallback (??)
                        $namaPaket = $trip['nama_paket'] ?? $trip['nama'] ?? 'Paket Trip';
                        $gambarPaket = $trip['gambar'] ?? null;
                        $kategoriPaket = $trip['kategori'] ?? 'Kategori Umum';
                        $gunungPaket = $trip['gunung'] ?? 'Gunung';
                        $vendorPaket = $trip['vendor'] ?? $trip['nama_vendor'] ?? 'Vendor';
                        $hargaPaket = $trip['c1_harga'] ?? 0;
                        $ketinggianPaket = $trip['c2_ketinggian'] ?? 0;
                    ?>
                        <div class="rounded-2xl border border-neutral-100 bg-white overflow-hidden shadow-sm hover:shadow-md hover:border-emerald-200 transition group flex flex-col">
                            <div class="h-40 overflow-hidden relative">
                                <img src="<?php echo htmlspecialchars($gambarPaket ? $base_path . $gambarPaket : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80'); ?>" 
                                     alt="<?php echo htmlspecialchars($namaPaket); ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <div class="absolute top-3 right-3 px-2 py-1 bg-white/90 backdrop-blur text-[10px] font-black rounded-lg text-forest-800 shadow-sm uppercase">
                                    <?php echo htmlspecialchars($kategoriPaket); ?>
                                </div>
                            </div>
                            <div class="p-5 flex-grow flex flex-col">
                                <div class="text-[10px] font-extrabold text-neutral-400 uppercase tracking-widest mb-1">
                                    Gunung <?php echo htmlspecialchars($gunungPaket); ?>
                                </div>
                                <h3 class="text-base font-extrabold text-neutral-800 leading-tight mb-2">
                                    <?php echo htmlspecialchars($namaPaket); ?>
                                </h3>
                                <div class="text-xs text-neutral-500 mb-4 flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-store text-emerald-600"></i> <?php echo htmlspecialchars($vendorPaket); ?>
                                </div>
                                
                                <div class="mt-auto pt-4 border-t border-neutral-100 flex items-end justify-between">
                                    <div>
                                        <span class="block text-[10px] text-neutral-400 font-bold uppercase">Harga Paket</span>
                                        <span class="block text-sm font-black text-forest-800">Rp <?php echo number_format($hargaPaket, 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-[10px] text-neutral-400 font-bold uppercase">Ketinggian</span>
                                        <span class="block text-sm font-black text-neutral-700"><?php echo number_format($ketinggianPaket); ?> mdpl</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div>

        <?php if (count($kandidatFiltered) > 0): ?>
            <!-- FORM PENGIRIMAN DATA KE MOORA -->
            <div class="bg-gradient-to-br from-forest-900 to-forest-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="absolute -right-10 -bottom-10 opacity-10 text-white pointer-events-none">
                    <i class="fa-solid fa-microchip text-[150px]"></i>
                </div>
                
                <div class="relative z-10 text-center sm:text-left text-white max-w-xl">
                    <h3 class="text-lg font-black mb-2 flex items-center justify-center sm:justify-start gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-400"></i> Lanjutkan Analisis Cerdas
                    </h3>
                    <p class="text-xs text-emerald-50/80 leading-relaxed font-medium">
                        Kandidat paket di atas akan dihitung menggunakan algoritma matriks <strong>MOORA (Multi-Objective Optimization on the basis of Ratio Analysis)</strong>. Sistem akan menilai benefit dan cost, lalu memberikan rekomendasi #1 yang paling sesuai dengan preferensi & fisik Anda.
                    </p>
                </div>

                <div class="relative z-10 w-full sm:w-auto shrink-0">
                    <form action="rekomendasi_moora.php" method="POST" onsubmit="handleMooraSubmit(event)">
                        <!-- MENG-GENERATE ULANG SEMUA POST DATA DARI HALAMAN SEBELUMNYA MENJADI HIDDEN INPUT -->
                        <?php
                            foreach ($_POST as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $sub_key => $sub_value) {
                                        echo '<input type="hidden" name="'.htmlspecialchars($key).'['.htmlspecialchars($sub_key).']" value="'.htmlspecialchars($sub_value).'">';
                                    }
                                } else {
                                    echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
                                }
                            }
                        ?>
                        
                        <button type="submit" class="w-full sm:w-auto px-8 py-4 rounded-xl text-forest-950 bg-amber-400 hover:bg-amber-300 shadow-[0_0_20px_rgba(251,191,36,0.3)] hover:shadow-[0_0_25px_rgba(251,191,36,0.5)] transition-all font-black text-sm flex items-center justify-center gap-2 active:scale-95">
                            <i class="fa-solid fa-calculator"></i> Mulai Pencarian Cerdas
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<script>
    const mooraMessages = [
        "Membangun matriks keputusan...",
        "Menghitung nilai normalisasi matriks...",
        "Menerapkan bobot preferensi Anda...",
        "Menghitung nilai optimasi Benefit & Cost...",
        "Menganalisis kecocokan fisik & fasilitas...",
        "Menentukan peringkat (ranking) paket..."
    ];

    function handleMooraSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const overlay = document.getElementById('loadingOverlayMoora');
        const msgEl = document.getElementById('loadingMessageMoora');
        const barEl = document.getElementById('loadingBarMoora');

        overlay.classList.remove('hidden');

        let step = 0;
        const duration = 2500; // 2.5 Detik animasi
        const intervalTime = duration / mooraMessages.length;

        const timer = setInterval(() => {
            if (step < mooraMessages.length) {
                msgEl.innerText = mooraMessages[step];
                barEl.style.width = `${((step + 1) / mooraMessages.length) * 100}%`;
                step++;
            } else {
                clearInterval(timer);
                form.submit();
            }
        }, intervalTime);
    }
</script>

<?php include '../includes/footer.php'; ?>