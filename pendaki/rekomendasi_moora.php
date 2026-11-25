<?php
// pendaki/rekomendasi_moora.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Trip.php';
require_once '../classes/Moora.php';
require_once '../classes/Education.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('pendaki');

// Redirect if weights not posted (Mencegah akses langsung via URL)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['weights'])) {
    header("Location: preferensi_moora.php");
    exit();
}

$rawWeights = $_POST['weights'];
$sumWeights = array_sum($rawWeights);

// Normalize weights to sum up to 1.0
$customWeights = [];
foreach ($rawWeights as $kode => $val) {
    $customWeights[$kode] = floatval($val) / ($sumWeights > 0 ? $sumWeights : 1);
}

// Parse selected facilities & category pre-filter
$prefFacilities = isset($_POST['pref_facilities']) ? $_POST['pref_facilities'] : [];
$rawKategori = isset($_POST['kategori_trip']) ? trim($_POST['kategori_trip']) : '';
$kategoriTrip = (!empty($rawKategori) && $rawKategori !== 'Semua') ? $rawKategori : null;

// Parse physical condition inputs
$usia = isset($_POST['usia']) ? intval($_POST['usia']) : 28;
$berat = isset($_POST['berat_badan']) ? floatval($_POST['berat_badan']) : 65;
$tinggi = isset($_POST['tinggi_badan']) ? floatval($_POST['tinggi_badan']) : 168;
$physicalData = [
    'usia' => $usia,
    'berat' => $berat,
    'tinggi' => $tinggi
];

// Fetch active trip packages filtered by category
$tripClass = new Trip($pdo);
$alternatives = $tripClass->getAll('', null, $kategoriTrip);

// Calculate MOORA with physical penalty integration
$moora = new Moora($pdo);
$mooraResult = $moora->calculate($alternatives, $customWeights, $prefFacilities, $physicalData);

$current_page = 'rekomendasi';
$page_title = 'Hasil Rekomendasi MOORA - MOORATrip';
$base_path = "../";

include '../includes/header.php';
?>

<!-- Nature Background blobs -->
<div class="fixed w-[500px] h-[500px] bg-forest-100 rounded-full blur-3xl -top-40 -right-40 opacity-40 pointer-events-none"></div>
<div class="fixed w-[400px] h-[400px] bg-emerald-50 rounded-full blur-3xl -bottom-40 -left-40 opacity-40 pointer-events-none"></div>

<section class="py-12 bg-neutral-50/50 flex-grow relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="../index.php" class="hover:text-forest-800 transition">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a href="preferensi_moora.php" class="hover:text-forest-800 transition">Preferensi SPK</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Hasil Rekomendasi</span>
        </div>

        <?php if (!$mooraResult['success']): ?>
            <div class="bg-white rounded-3xl p-12 border border-neutral-100 shadow-xl text-center space-y-4 max-w-xl mx-auto">
                <div class="h-16 w-16 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center mx-auto text-3xl">
                    <i class="fa-solid fa-triangle-exclamation animate-bounce"></i>
                </div>
                <h2 class="text-xl font-extrabold text-neutral-800">Tidak Ada Paket Ditemukan</h2>
                <p class="text-neutral-600 text-sm leading-relaxed">
                    Sistem MOORA tidak menemukan alternatif paket trip yang cocok untuk Kategori <strong><?php echo htmlspecialchars($kategoriTrip); ?></strong>. Silakan coba ubah preferensi atau kategori trip Anda.
                </p>
                <div class="pt-2">
                    <a href="preferensi_moora.php" class="px-6 py-3 rounded-xl bg-forest-800 hover:bg-forest-900 text-white font-bold text-xs inline-flex items-center gap-2 shadow">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Preferensi
                    </a>
                </div>
            </div>
        <?php else: 
            $results = $mooraResult['results'];
            $bestAlt = $results[0]; // #1 Ranked alternative
            
            // LOGIKA KECOCOKAN FISIK & MEDAN
            $bmi_val = $mooraResult['bmi'];
            if ($bmi_val < 18.5) { $status_fisik = "Underweight"; }
            elseif ($bmi_val <= 24.9) { $status_fisik = "Ideal"; }
            elseif ($bmi_val <= 29.9) { $status_fisik = "Overweight"; }
            else { $status_fisik = "Obesitas"; }

            $alasan_medan = "";
            if ($bestAlt['c3_medan'] == 1) {
                $alasan_medan = "Tingkat kesulitan jalur pada paket ini tergolong <strong>Mudah</strong>. Sangat aman, direkomendasikan, dan sesuai dengan kapasitas fisik Anda saat ini untuk meminimalisir risiko kelelahan.";
            } elseif ($bestAlt['c3_medan'] == 2) {
                $alasan_medan = "Tingkat kesulitan jalur tergolong <strong>Sedang</strong>. Memberikan keseimbangan yang pas antara tantangan alam namun tetap aman bagi daya tahan fisik Anda.";
            } else {
                $alasan_medan = "Walaupun jalurnya <strong>Sulit</strong>, sistem menilai paket ini memiliki kelengkapan fasilitas yang sangat mumpuni untuk mendampingi fisik Anda di medan ekstrem.";
            }
        ?>

            <!-- Summary Header Banner -->
            <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl mb-8 space-y-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 border-b border-neutral-100 pb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-widest">
                                Kategori: <?php echo htmlspecialchars($kategoriTrip); ?>
                            </span>
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-forest-50 text-forest-800 uppercase tracking-widest">
                                Profil: Usia <?php echo $usia; ?> thn | BMI <?php echo $mooraResult['bmi']; ?>
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-neutral-800">
                            Hasil Rekomendasi SPK MOORA
                        </h1>
                        <p class="text-neutral-500 text-xs sm:text-sm mt-1">
                            Diurutkan berdasarkan nilai optimasi tertinggi \(Y_i = \sum \text{Benefit} - \sum \text{Cost}\).
                        </p>
                    </div>

                    <a href="preferensi_moora.php" class="px-4 py-2.5 rounded-xl border border-neutral-200 hover:bg-neutral-50 text-neutral-700 font-bold text-xs flex items-center gap-2 transition">
                        <i class="fa-solid fa-sliders"></i> Ubah Preferensi
                    </a>
                </div>
            </div>

            <!-- TOP 1 WINNER HIGHLIGHT CARD -->
            <div class="bg-gradient-to-r from-forest-900 via-forest-850 to-forest-800 rounded-3xl p-8 text-white shadow-2xl mb-12 relative overflow-hidden">
                <div class="absolute -right-12 -bottom-12 opacity-10 text-white pointer-events-none">
                    <i class="fa-solid fa-trophy text-[220px]"></i>
                </div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-7 space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400 text-forest-950 font-black text-xs uppercase tracking-widest shadow">
                            <i class="fa-solid fa-crown text-amber-900"></i> Rekomendasi #1 Terbaik
                        </div>

                        <div>
                            <span class="text-xs font-bold text-emerald-300 uppercase tracking-widest block mb-1">
                                Gunung <?php echo htmlspecialchars($bestAlt['gunung']); ?> &bull; <?php echo htmlspecialchars($bestAlt['kategori']); ?>
                            </span>
                            <h2 class="text-2xl sm:text-4xl font-black text-white leading-tight">
                                <?php echo htmlspecialchars($bestAlt['nama']); ?>
                            </h2>
                            <p class="text-emerald-100/80 text-xs sm:text-sm mt-2 font-medium">
                                Penyelenggara: <strong><?php echo htmlspecialchars($bestAlt['vendor']); ?></strong>
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-xs">
                            <div class="px-4 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/10">
                                <span class="text-[10px] text-emerald-300 block uppercase font-bold">Skor Optimasi Yi</span>
                                <span class="text-lg font-black text-white"><?php echo number_format($bestAlt['yi'], 4); ?></span>
                            </div>
                            <div class="px-4 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/10">
                                <span class="text-[10px] text-emerald-300 block uppercase font-bold">Harga Paket</span>
                                <span class="text-lg font-black text-white">Rp <?php echo number_format($bestAlt['c1_harga'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="px-4 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/10">
                                <span class="text-[10px] text-emerald-300 block uppercase font-bold">Ketinggian</span>
                                <span class="text-lg font-black text-white"><?php echo number_format($bestAlt['c2_ketinggian']); ?> mdpl</span>
                            </div>
                        </div>

                        <!-- KENAPA PAKET INI COCOK -->
                        <div class="p-4 rounded-2xl bg-white/5 border border-emerald-500/20">
                            <h4 class="text-emerald-300 font-extrabold text-xs mb-1.5 flex items-center gap-2">
                                <i class="fa-solid fa-heart-pulse"></i> Kenapa Paket Ini Paling Cocok?
                            </h4>
                            <p class="text-emerald-50/80 text-[11px] leading-relaxed">
                                Kalkulasi MOORA menyatakan paket ini paling ideal untuk <strong>BMI Anda (<?php echo $status_fisik; ?>)</strong>. <?php echo $alasan_medan; ?>
                            </p>
                        </div>

                        <!-- UI FASILITAS & ANALISIS GAP CERDAS -->
                        <div class="p-5 rounded-2xl bg-black/30 border border-white/10 backdrop-blur-sm mt-4">
                            <h4 class="text-white font-extrabold text-sm mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-emerald-400"></i> Analisis Cerdas Fasilitas & Solusi
                            </h4>
                            
                            <ul class="space-y-3 text-xs leading-relaxed">
                                <?php 
                                // Master Data Fasilitas (F1 - F12)
                                $master_fasilitas = [
                                    'F1' => ['name' => 'Tenda Dome', 'keyword' => 'Tenda', 'solusi' => 'Paket ini tidak menanggung Tenda. Anda WAJIB membawa tenda mandiri. Penyewaan alat outdoor terdekat bisa menghubungi Admin Vendor via WhatsApp.'],
                                    'F2' => ['name' => 'Tas Gunung (Carrier)', 'keyword' => 'Tas', 'solusi' => 'Vendor tidak meminjamkan tas. Silakan sewa tas carrier (40L-60L) di rental outdoor area Mepo/Basecamp.'],
                                    'F3' => ['name' => 'Sleeping Bag', 'keyword' => 'Sleeping', 'solusi' => 'Anda WAJIB membawa Sleeping Bag pribadi. Dapat disewa di pos pendaftaran Basecamp (±Rp 25.000/malam).'],
                                    'F4' => ['name' => 'Simaksi (Tiket Pendakian)', 'keyword' => 'Simaksi', 'solusi' => 'Siapkan KTP asli, surat kesehatan, dan uang tunai untuk mengurus perizinan Simaksi mandiri setibanya di Basecamp.'],
                                    'F5' => ['name' => 'Transportasi Ojek', 'keyword' => 'Ojek', 'solusi' => 'Jalan menuju pos awal ditempuh via jalan kaki. Jika ingin menggunakan ojek warga setempat, siapkan uang tunai ±Rp 30.000/jalan.'],
                                    'F6' => ['name' => 'Homestay / Basecamp', 'keyword' => 'Homestay', 'solusi' => 'Paket tidak termasuk biaya penginapan. Anda bisa menyewa kamar homestay warga di sekitar basecamp.'],
                                    'F7' => ['name' => 'Buah-buahan Segar', 'keyword' => 'Buah', 'solusi' => 'Bawa logistik buah-buahan segar (pisang, apel) secara mandiri untuk asupan vitamin ekstra di jalur.'],
                                    'F8' => ['name' => 'Jasa Porter Tim', 'keyword' => 'Porter', 'solusi' => 'Tidak termasuk di paket. Anda bisa menyewa porter warga lokal di Basecamp (kisaran Rp 250k - Rp 350k/hari).'],
                                    'F9' => ['name' => 'Atribut / Stiker', 'keyword' => 'Atribut', 'solusi' => 'Vendor tidak memberikan merchandise khusus. Anda dapat membelinya di warung sekitar basecamp.'],
                                    'F10' => ['name' => 'Transportasi PP (Mepo)', 'keyword' => 'Transportasi PP', 'solusi' => 'Anda harus menuju Basecamp secara mandiri (menggunakan kendaraan pribadi atau Elf carteran).'],
                                    'F11' => ['name' => 'Makan Selama Pendakian', 'keyword' => 'Makan', 'solusi' => 'Vendor tidak menanggung konsumsi berat. Siapkan logistik bahan makanan dan alat masak kelompok Anda.'],
                                    'F12' => ['name' => 'P3K Standar', 'keyword' => 'P3K', 'solusi' => 'Anda diwajibkan membawa obat-obatan pribadi dan kotak P3K dasar secara mandiri.']
                                ];

                                // EKSTRAKSI STRING FASILITAS YANG ANTI-GAGAL
                                $raw_db = isset($bestAlt['detail']['c4_fasilitas']) ? $bestAlt['detail']['c4_fasilitas'] : '';
                                $db_fasilitas_str = is_array($raw_db) ? implode(',', $raw_db) : (string)$raw_db;
                                $db_fasilitas_str_lower = strtolower($db_fasilitas_str);

                                $pref_arr = (array)$prefFacilities;
                                $has_items_to_show = false;

                                foreach($master_fasilitas as $key => $item) {
                                    $key_lower = strtolower($key);
                                    $keyword_lower = strtolower($item['keyword']);
                                    $name_lower = strtolower($item['name']);

                                    // Cek fasilitas dengan huruf kecil (Case Insensitive Manual)
                                    $is_provided = (
                                        strpos($db_fasilitas_str_lower, $key_lower) !== false || 
                                        strpos($db_fasilitas_str_lower, $keyword_lower) !== false ||
                                        strpos($db_fasilitas_str_lower, $name_lower) !== false
                                    );
                                    
                                    // Cek apakah pendaki meminta fasilitas ini
                                    $is_requested = false;
                                    foreach($pref_arr as $pref) {
                                        $pref_lower = strtolower($pref);
                                        if (strpos($pref_lower, $key_lower) !== false || strpos($pref_lower, $keyword_lower) !== false || strpos($pref_lower, $name_lower) !== false) {
                                            $is_requested = true;
                                            break;
                                        }
                                    }

                                    // LOGIKA TAMPILAN SESUAI PERMINTAAN
                                    if ($is_requested && $is_provided) {
                                        $has_items_to_show = true;
                                        // Diminta & Ada = Centang Hijau Terang
                                        echo '<li class="flex items-start gap-3 text-emerald-100">';
                                        echo '<i class="fa-solid fa-circle-check text-emerald-400 mt-0.5 text-base drop-shadow-md"></i>';
                                        echo '<span><strong>' . $item['name'] . ':</strong> Sesuai preferensi Anda dan TERSEDIA di paket ini.</span>';
                                        echo '</li>';
                                    } elseif ($is_requested && !$is_provided) {
                                        $has_items_to_show = true;
                                        // Diminta & TIDAK Ada = Lampu Kuning (Solusi)
                                        echo '<li class="flex items-start gap-3 text-amber-50">';
                                        echo '<i class="fa-solid fa-lightbulb text-amber-400 mt-0.5 text-base drop-shadow-md animate-pulse"></i>';
                                        echo '<span><strong>' . $item['name'] . ' (Preferensi Anda):</strong> ' . $item['solusi'] . '</span>';
                                        echo '</li>';
                                    } elseif (!$is_requested && $is_provided) {
                                        $has_items_to_show = true;
                                        // Tidak Diminta & Ada = Centang Hijau Pudar (Bonus)
                                        echo '<li class="flex items-start gap-3 text-emerald-100/70">';
                                        echo '<i class="fa-solid fa-check text-emerald-500/70 mt-0.5 text-sm"></i>';
                                        echo '<span><strong>' . $item['name'] . ':</strong> Fasilitas bawaan paket (Tersedia).</span>';
                                        echo '</li>';
                                    }
                                }

                                if (!$has_items_to_show) {
                                    echo '<li class="text-neutral-400 italic">Tidak ada detail analisis fasilitas untuk ditampilkan.</li>';
                                }
                                ?>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <form action="laporan_terpilih.php" method="POST" class="inline-block">
                                <input type="hidden" name="action" value="save_selected">
                                <input type="hidden" name="paket_id" value="<?php echo $bestAlt['id']; ?>">
                                <input type="hidden" name="skor_moora" value="<?php echo $bestAlt['yi']; ?>">
                                <input type="hidden" name="kategori_pilihan" value="<?php echo htmlspecialchars($bestAlt['kategori']); ?>">
                                <input type="hidden" name="catatan_fisik" value="<?php echo htmlspecialchars($mooraResult['physical_notes']); ?>">
                                <button type="submit" class="px-6 py-3.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-forest-950 font-black text-xs shadow-lg transition active:scale-95 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check text-sm"></i> Pilih Paket Ini (Simpan ke Laporan)
                                </button>
                            </form>

                            <button onclick="openRouteModal(<?php echo $bestAlt['id']; ?>)" class="px-5 py-3.5 rounded-xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs backdrop-blur-md border border-white/20 transition flex items-center gap-2">
                                <i class="fa-solid fa-route"></i> Detail Rute & Pos
                            </button>

                            <!-- PERUBAHAN: Target="_blank" agar membuka Tab Baru, dan URL mengirim paket_id -->
                            <a href="edukasi.php?paket_id=<?php echo $bestAlt['id']; ?>" target="_blank" class="px-5 py-3.5 rounded-xl bg-sky-500/20 hover:bg-sky-500/30 text-white font-bold text-xs backdrop-blur-md border border-sky-400/30 transition flex items-center gap-2">
                                <i class="fa-solid fa-book-open-reader"></i> Lihat Edukasi
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-5 h-full">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl border-4 border-white/20 group h-full">
                            <img src="<?php echo htmlspecialchars($bestAlt['gambar'] ? $base_path . $bestAlt['gambar'] : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80'); ?>"
                                 alt="<?php echo htmlspecialchars($bestAlt['nama']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-700 aspect-[4/3] lg:aspect-auto">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ALL ALTERNATIVES RANKING LIST -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-extrabold text-neutral-800 flex items-center gap-2">
                        <i class="fa-solid fa-list-ol text-forest-700"></i> Seluruh Peringkat Rekomendasi Paket Trip
                    </h3>
                    <span class="text-xs text-neutral-400 font-semibold"><?php echo count($results); ?> Paket Terhitung</span>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <?php foreach ($results as $alt): 
                        $medanLabels = [1 => 'Mudah', 2 => 'Sedang', 3 => 'Sulit'];
                        $medanBadge = [
                            1 => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            2 => 'bg-amber-100 text-amber-800 border-amber-200',
                            3 => 'bg-red-100 text-red-800 border-red-200'
                        ];
                        $isPenalized = isset($alt['is_penalized']) && $alt['is_penalized'];
                    ?>
                        <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-lg hover:border-emerald-200 transition flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            
                            <div class="flex items-start gap-4 flex-grow">
                                <div class="h-12 w-12 rounded-2xl <?php echo $alt['rank'] == 1 ? 'bg-amber-400 text-forest-950 font-black' : 'bg-neutral-100 text-neutral-700 font-bold'; ?> flex items-center justify-center text-lg flex-shrink-0 shadow">
                                    #<?php echo $alt['rank']; ?>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                            <?php echo htmlspecialchars($alt['kategori']); ?>
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-neutral-100 text-neutral-600">
                                            Gunung <?php echo htmlspecialchars($alt['gunung']); ?>
                                        </span>
                                        <button onclick="openRouteModal(<?php echo $alt['id']; ?>)" 
                                                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border <?php echo $medanBadge[$alt['c3_medan']]; ?> hover:opacity-80 transition inline-flex items-center gap-1">
                                            <i class="fa-solid fa-mountain"></i> Medan: <?php echo $medanLabels[$alt['c3_medan']]; ?> 
                                            <i class="fa-solid fa-chevron-down text-[8px] ml-1"></i>
                                        </button>

                                        <?php if ($isPenalized): ?>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-red-500 text-white animate-pulse">
                                                Penalti Fisik
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <h4 class="font-extrabold text-neutral-800 text-lg leading-snug">
                                        <?php echo htmlspecialchars($alt['nama']); ?>
                                    </h4>

                                    <div class="flex flex-wrap items-center gap-4 text-xs text-neutral-500 font-medium">
                                        <span><i class="fa-solid fa-store mr-1 text-neutral-400"></i> <?php echo htmlspecialchars($alt['vendor']); ?></span>
                                        <span><i class="fa-solid fa-clock mr-1 text-neutral-400"></i> Waktu: <?php echo $alt['c5_waktu_tempuh']; ?> Jam</span>
                                        <span><i class="fa-solid fa-box-open mr-1 text-neutral-400"></i> Fasilitas Cocok: <strong><?php echo $alt['c4_fasilitas_score']; ?> Item</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full md:w-auto border-t md:border-t-0 border-neutral-100 pt-4 md:pt-0 justify-between">
                                <div class="text-left sm:text-right">
                                    <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest block">Skor MOORA (Yi)</span>
                                    <span class="text-xl font-black text-forest-800"><?php echo number_format($alt['yi'], 4); ?></span>
                                    <span class="text-[10px] text-neutral-400 block font-semibold">Harga: Rp <?php echo number_format($alt['c1_harga'], 0, ',', '.'); ?></span>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                                    <button onclick="openRouteModal(<?php echo $alt['id']; ?>)" class="px-3.5 py-2.5 rounded-xl border border-neutral-200 hover:bg-neutral-50 text-neutral-700 font-bold text-xs transition flex-1 sm:flex-none text-center">
                                        <i class="fa-solid fa-route mr-1"></i> Quick Pos
                                    </button>

                                    <!-- PERUBAHAN: Target="_blank" agar membuka Tab Baru, dan URL mengirim paket_id -->
                                    <a href="edukasi.php?paket_id=<?php echo $alt['id']; ?>" target="_blank" class="px-3.5 py-2.5 rounded-xl border border-sky-200 hover:bg-sky-50 text-sky-700 font-bold text-xs transition flex-1 sm:flex-none text-center">
                                        <i class="fa-solid fa-book-open-reader mr-1"></i> Edukasi
                                    </a>

                                    <form action="laporan_terpilih.php" method="POST" class="inline-block flex-1 sm:flex-none">
                                        <input type="hidden" name="action" value="save_selected">
                                        <input type="hidden" name="paket_id" value="<?php echo $alt['id']; ?>">
                                        <input type="hidden" name="skor_moora" value="<?php echo $alt['yi']; ?>">
                                        <input type="hidden" name="kategori_pilihan" value="<?php echo htmlspecialchars($alt['kategori']); ?>">
                                        <input type="hidden" name="catatan_fisik" value="<?php echo htmlspecialchars($mooraResult['physical_notes']); ?>">
                                        <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-forest-800 hover:bg-forest-900 text-white font-bold text-xs shadow transition active:scale-95">
                                            Pilih Paket Ini
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- INTERACTIVE ROUTE DETAIL MODAL -->
            <div id="routeModal" class="fixed inset-0 bg-neutral-900/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
                <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl border border-neutral-100 flex flex-col animate-in fade-in zoom-in duration-200">
                    <div class="p-6 border-b border-neutral-100 bg-neutral-50 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-emerald-100 text-emerald-800 rounded-xl flex items-center justify-center text-lg font-bold">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <div>
                                <h3 id="modalTitle" class="font-extrabold text-neutral-800 text-base">Detail Breakdown Rute & Pos</h3>
                                <p id="modalSub" class="text-xs text-neutral-500">Segmentasi jalur, elevasi gain, dan kontak darurat lokal.</p>
                            </div>
                        </div>
                        <button onclick="closeRouteModal()" class="h-8 w-8 rounded-full bg-neutral-200 hover:bg-neutral-300 text-neutral-600 flex items-center justify-center text-xs font-bold transition">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="p-6 overflow-y-auto space-y-6">
                        <div>
                            <h4 class="text-xs font-extrabold text-neutral-400 uppercase tracking-wider mb-3">Segmentasi Rute Per Pos:</h4>
                            <div id="segmentsContainer" class="space-y-3"></div>
                        </div>
                        <div class="pt-4 border-t border-neutral-100">
                            <h4 class="text-xs font-extrabold text-neutral-400 uppercase tracking-wider mb-3">Kontak Darurat & Porter Lokal:</h4>
                            <div id="contactsContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
                        </div>
                    </div>

                    <div class="p-4 border-t border-neutral-100 bg-neutral-50 text-right">
                        <button onclick="closeRouteModal()" class="px-5 py-2.5 rounded-xl bg-neutral-800 text-white text-xs font-bold hover:bg-neutral-900 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            <script>
                async function openRouteModal(paketId) {
                    const modal = document.getElementById('routeModal');
                    const segContainer = document.getElementById('segmentsContainer');
                    const contContainer = document.getElementById('contactsContainer');

                    document.getElementById('modalTitle').innerText = "Memuat Detail Rute...";
                    document.getElementById('modalSub').innerText = "Menghubungi server via Fetch API AJAX...";

                    segContainer.innerHTML = `<div class="p-6 text-center text-xs text-emerald-800 font-semibold space-y-2"><i class="fa-solid fa-spinner animate-spin text-2xl"></i><p>Mengambil data rute segmentasi dari database...</p></div>`;
                    contContainer.innerHTML = '';
                    modal.classList.remove('hidden');

                    try {
                        const response = await fetch('../api/get_rute_detail.php?id=' + paketId);
                        const result = await response.json();

                        if (result.status === 'success') {
                            const data = result.data;
                            document.getElementById('modalTitle').innerText = data.nama_paket;
                            document.getElementById('modalSub').innerText = "Gunung " + data.gunung + " (" + data.kategori + ") • Breakdown Segmentasi Pos & Kontak";

                            if (!data.segments || data.segments.length === 0) {
                                segContainer.innerHTML = `<div class="p-4 rounded-xl bg-neutral-50 text-center text-xs text-neutral-400 italic">Belum ada detail rute segmentasi dari vendor untuk paket ini.</div>`;
                            } else {
                                let html = '';
                                data.segments.forEach((s, idx) => {
                                    html += `<div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 space-y-2"><div class="flex justify-between items-center text-xs font-extrabold text-neutral-800"><span class="flex items-center gap-2"><span class="h-5 w-5 rounded-full bg-forest-800 text-white text-[10px] flex items-center justify-center font-bold">${idx + 1}</span>${s.pos_asal} &rarr; ${s.pos_tujuan}</span><span class="px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-bold">${s.waktu_tempuh_menit} Menit &bull; +${s.elevasi_gain} mdpl</span></div><p class="text-xs text-neutral-600 pl-7 leading-relaxed font-medium"><strong class="text-neutral-700">Kondisi Medan:</strong> ${s.kondisi_medan}</p></div>`;
                                });
                                segContainer.innerHTML = html;
                            }

                            if (!data.contacts || data.contacts.length === 0) {
                                contContainer.innerHTML = `<div class="col-span-2 p-4 rounded-xl bg-neutral-50 text-center text-xs text-neutral-400 italic">Tidak ada kontak darurat lokal terdaftar.</div>`;
                            } else {
                                let html = '';
                                data.contacts.forEach(c => {
                                    html += `<div class="p-3 rounded-xl bg-emerald-50/80 border border-emerald-200 flex items-center gap-3"><div class="h-9 w-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm font-bold"><i class="fa-solid fa-phone"></i></div><div class="space-y-0.5"><span class="text-xs font-extrabold text-neutral-800 block">${c.nama}</span><span class="text-[10px] text-emerald-800 font-bold block">${c.peran} &bull; ${c.no_hp}</span></div></div>`;
                                });
                                contContainer.innerHTML = html;
                            }
                        } else {
                            segContainer.innerHTML = `<div class="p-4 bg-red-50 text-red-700 text-xs rounded-xl">${result.message}</div>`;
                        }
                    } catch (err) {
                        segContainer.innerHTML = `<div class="p-4 bg-red-50 text-red-700 text-xs rounded-xl">Gagal memuat data rute via AJAX: ${err.message}</div>`;
                    }
                }

                function closeRouteModal() {
                    document.getElementById('routeModal').classList.add('hidden');
                }
            </script>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>