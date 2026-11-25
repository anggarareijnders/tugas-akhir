<?php
// pendaki/preferensi_moora.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Moora.php';

$db = new Database();
$pdo =$db->getConnection();

// Protect page
Auth::requireRole('pendaki');

$mooraClass = new Moora($pdo);
$defaultCriteria =$mooraClass->getCriteria();

// Mengambil data paket trip termurah dari database vendor
$cheapestTrip = null;
try {
    // Sesuaikan nama tabel 'paket_trip' dan kolom dengan database aktual Anda
    $stmtCheapest =$pdo->query("SELECT nama_paket, harga FROM paket_trip ORDER BY harga ASC LIMIT 1");
    if ($stmtCheapest) {
        $cheapestTrip =$stmtCheapest->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // Abaikan jika tabel belum ada atau error agar halaman tidak crash
}

$facilities_list = [
    'F1' => ['name' => 'Tenda Dome', 'desc' => 'Penyediaan tenda dome sebagai tempat berlindung kelompok.'],
    'F2' => ['name' => 'Tas Gunung (Carrier)', 'desc' => 'Fasilitas peminjaman tas gunung (carrier) bagi pendaki.'],
    'F3' => ['name' => 'Sleeping Bag', 'desc' => 'Penyediaan kantong tidur (sleeping bag) demi mencegah hipotermia.'],
    'F4' => ['name' => 'Simaksi (Tiket)', 'desc' => 'Pengurusan perizinan, asuransi, dan tiket masuk.'],
    'F5' => ['name' => 'Transportasi Ojek', 'desc' => 'Fasilitas ojek untuk memangkas jarak ke start jalur.'],
    'F6' => ['name' => 'Homestay / Basecamp', 'desc' => 'Penyediaan tempat singgah sebelum pendakian.'],
    'F7' => ['name' => 'Buah-buahan Segar', 'desc' => 'Suplai logistik buah segar sebagai vitamin.'],
    'F8' => ['name' => 'Jasa Porter Tim', 'desc' => 'Porter untuk membawa logistik dan alat berat.'],
    'F9' => ['name' => 'Atribut / Stiker', 'desc' => 'Suvenir kenang-kenangan dari pihak vendor.'],
    'F10' => ['name' => 'Transportasi Mepo', 'desc' => 'Antar-jemput dari titik kumpul (Meeting Point).'],
    'F11' => ['name' => 'Makan Selama Pendakian', 'desc' => 'Makan selama di gunung hingga kembali.'],
    'F12' => ['name' => 'P3K Standar', 'desc' => 'Kesiapan penanganan medis pertama di jalur.']
];

$current_page = 'preferensi';
$page_title = 'Atur Preferensi Aktual - MOORATrip';$base_path = "../";

include '../includes/header.php';
?>

<!-- Loading Simulation Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-forest-900/95 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="text-center space-y-6 max-w-md w-full px-6">
        <div class="relative mx-auto h-24 w-24">
            <div class="absolute inset-0 rounded-full border-4 border-emerald-500/20 border-t-emerald-400 animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center text-white text-3xl">
                <i class="fa-solid fa-person-hiking animate-bounce text-emerald-300"></i>
            </div>
        </div>
        <div class="space-y-2">
            <h3 class="text-xl font-bold text-white tracking-wide">Menyiapkan Data</h3>
            <p id="loadingMessage" class="text-emerald-300 text-sm h-6 font-semibold">Menganalisis kondisi medis Anda...</p>
        </div>
        <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
            <div id="loadingBar" class="bg-emerald-400 h-full w-0 transition-all duration-300 ease-out"></div>
        </div>
    </div>
</div>

<section class="py-12 bg-neutral-50 flex-grow relative z-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="../index.php" class="hover:text-forest-800 transition">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Pencarian Spesifik SPK</span>
        </div>

        <div class="bg-white rounded-[2rem] p-8 sm:p-10 border border-neutral-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] space-y-8">
            <div class="border-b border-neutral-100 pb-6 flex items-center gap-4">
                <div class="h-14 w-14 bg-gradient-to-br from-forest-50 to-emerald-100 text-forest-800 flex items-center justify-center rounded-2xl text-2xl font-bold shadow-sm">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-emerald-800">Pencarian Paket Trip Spesifik</h1>
                    <p class="text-neutral-500 text-xs mt-1 font-medium leading-relaxed">
                        Masukkan kondisi medis, budget harga, target mdpl, dan tingkat kesulitan agar sistem merekomendasikan paket yang aman dan sesuai.
                    </p>
                </div>
            </div>

            <!-- Kotak Peringatan Validasi -->
            <div id="errorValidation" class="hidden p-4 bg-red-50 border border-red-200/60 text-red-800 text-sm font-semibold rounded-2xl flex items-center gap-3 animate-in fade-in zoom-in-95 duration-300">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
                <span>Pastikan semua preferensi & riwayat penyakit diisi agar perhitungan MOORA akurat!</span>
            </div>

            <form action="preview_kandidat.php" method="POST" onsubmit="handleFormSubmit(event)" class="space-y-8">
                
                <!-- 1. FILTER KATEGORI TRIP -->
                <div class="p-6 rounded-2xl bg-emerald-50/50 border border-emerald-100 space-y-4">
                    <div class="flex items-center gap-3 border-b border-emerald-100 pb-3">
                        <div class="h-8 w-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">1</div>
                        <div>
                            <h3 class="font-extrabold text-emerald-900 text-base">Filter Kategori Trip Pendakian</h3>
                            <p class="text-emerald-700 text-[11px] font-medium">Fokuskan algoritma pada jenis pendakian tertentu.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                        <label class="relative flex items-center p-4 rounded-xl border border-emerald-200 bg-white cursor-pointer hover:border-emerald-500 transition shadow-sm group [&:has(input:checked)]:ring-2 [&:has(input:checked)]:ring-emerald-500">
                            <input type="radio" name="kategori_trip" value="" checked class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-neutral-300">
                            <div class="ml-3"><span class="block text-sm font-extrabold text-neutral-800 group-hover:text-emerald-700">Semua Kategori</span></div>
                        </label>
                        <label class="relative flex items-center p-4 rounded-xl border border-emerald-200 bg-white cursor-pointer hover:border-emerald-500 transition shadow-sm group [&:has(input:checked)]:ring-2 [&:has(input:checked)]:ring-emerald-500">
                            <input type="radio" name="kategori_trip" value="Camping" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-neutral-300">
                            <div class="ml-3"><span class="block text-sm font-extrabold text-neutral-800 group-hover:text-emerald-700">Camping (Menginap)</span></div>
                        </label>
                        <label class="relative flex items-center p-4 rounded-xl border border-emerald-200 bg-white cursor-pointer hover:border-emerald-500 transition shadow-sm group [&:has(input:checked)]:ring-2 [&:has(input:checked)]:ring-emerald-500">
                            <input type="radio" name="kategori_trip" value="Tektok" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-neutral-300">
                            <div class="ml-3"><span class="block text-sm font-extrabold text-neutral-800 group-hover:text-emerald-700">Tektok (1 Hari)</span></div>
                        </label>
                    </div>
                </div>

                <!-- 2. KONDISI FISIK (BMI) -->
                <div class="p-6 rounded-2xl bg-amber-50/50 border border-amber-100 space-y-4">
                    <div class="flex items-center gap-3 border-b border-amber-100 pb-3">
                        <div class="h-8 w-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">2</div>
                        <div>
                            <h3 class="font-extrabold text-amber-900 text-base">Input Kondisi Fisik Pendaki</h3>
                            <p class="text-amber-800 text-[11px] font-medium">Kalkulasi Index Massa Tubuh (BMI) sebagai acuan kelayakan.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                        <div>
                            <label class="block text-[10px] font-extrabold text-amber-900 uppercase tracking-wider mb-2">Usia</label>
                            <input type="number" name="usia" value="25" min="10" max="90" required class="w-full px-4 py-3 text-sm font-bold border border-amber-200 rounded-xl bg-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-amber-900 uppercase tracking-wider mb-2">Berat Badan (Kg)</label>
                            <input type="number" id="input-berat" name="berat_badan" value="65" min="20" required class="w-full px-4 py-3 text-sm font-bold border border-amber-200 rounded-xl bg-white" oninput="calculateBMIPreview()">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-amber-900 uppercase tracking-wider mb-2">Tinggi Badan (Cm)</label>
                            <input type="number" id="input-tinggi" name="tinggi_badan" value="168" min="100" required class="w-full px-4 py-3 text-sm font-bold border border-amber-200 rounded-xl bg-white" oninput="calculateBMIPreview()">
                        </div>
                    </div>
                    <div id="bmiPreviewBox" class="p-4 bg-white rounded-xl border border-amber-200 text-xs flex flex-col sm:flex-row items-center justify-between gap-2 text-amber-900 shadow-sm mt-2">
                        <span class="font-bold"><i class="fa-solid fa-heart-pulse text-amber-500 mr-1.5 text-base"></i> Indeks Massa Tubuh (BMI): <strong id="bmiVal" class="text-base text-amber-700 ml-1">23.0</strong></span>
                        <span id="bmiBadge" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">Ideal / Normal</span>
                    </div>
                </div>

                <!-- 3. KONDISI MEDIS & RIWAYAT PENYAKIT (BARU) -->
                <div class="p-6 rounded-2xl bg-rose-50/50 border border-rose-100 space-y-4">
                    <div class="flex items-center gap-3 border-b border-rose-100 pb-3">
                        <div class="h-8 w-8 rounded-lg bg-rose-600 text-white flex items-center justify-center text-sm font-bold shadow-sm"><i class="fa-solid fa-notes-medical"></i></div>
                        <div>
                            <h3 class="font-extrabold text-rose-900 text-base">Riwayat Penyakit Pribadi</h3>
                            <p class="text-rose-700 text-[11px] font-medium">Sistem cerdas akan menganalisis resiko dan memberikan anjuran logistik obat.</p>
                        </div>
                    </div>
                    <div class="pt-2">
                        <select id="riwayat_penyakit" name="riwayat_penyakit" onchange="checkPenyakit()" required class="w-full px-4 py-3 text-sm font-bold text-rose-900 border border-rose-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 bg-white transition cursor-pointer">
                            <option value="" disabled selected>-- Pilih Kondisi Medis Anda --</option>
                            <option value="tidak_ada">Tidak Ada Riwayat Penyakit (Sehat Walafiat)</option>
                            <option value="asma">Asma / Sesak Napas (Ringan - Sedang)</option>
                            <option value="maag">Maag Kronis / Asam Lambung Akut</option>
                            <option value="alergi_dingin">Alergi Suhu Dingin Ekstrem (Hipotermia Ringan)</option>
                            <option value="jantung">Penyakit Jantung / Masalah Kardiovaskular</option>
                            <option value="epilepsi">Epilepsi / Sering Kejang</option>
                            <option value="patah_tulang">Cedera / Patah Tulang Sendi Belum Sembuh Total</option>
                        </select>
                    </div>

                    <!-- Alert Penyakit Berat -->
                    <div id="medicalWarningBox" class="hidden mt-4 p-4 bg-red-100 border-l-4 border-red-600 rounded-r-xl shadow-sm animate-in fade-in duration-300">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-triangle-exclamation text-red-600 text-2xl mt-0.5"></i>
                            <div>
                                <h4 class="text-red-800 font-black text-sm uppercase tracking-wider">Peringatan Medis Keras!</h4>
                                <p class="text-red-700 text-xs mt-1 font-semibold leading-relaxed">Berdasarkan algoritma keselamatan kami, riwayat penyakit Anda masuk dalam kategori <strong class="text-red-900">RESIKO BERAT</strong>. Sangat <strong>TIDAK DISARANKAN</strong> untuk melakukan pendakian gunung karena mengancam keselamatan nyawa di ketinggian. Proses pencarian paket trip dihentikan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Rekomendasi Obat Penyakit Ringan -->
                    <div id="medicineRecommendationBox" class="hidden mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl shadow-sm animate-in fade-in duration-300">
                        <h4 class="text-blue-900 font-black text-xs uppercase tracking-wider mb-2 border-b border-blue-200 pb-2"><i class="fa-solid fa-kit-medical mr-1.5 text-blue-600"></i> Rekomendasi Obat Pribadi Wajib Bawa:</h4>
                        <ul id="medicineList" class="text-blue-800 text-xs font-semibold space-y-2 pl-1">
                            <!-- Diisi oleh Javascript -->
                        </ul>
                        <p class="text-[10px] text-blue-600 font-medium mt-3 italic">*Data riwayat medis ini juga akan masuk dalam perhitungan sistem untuk mencarikan gunung dengan medan teraman untuk Anda.</p>
                    </div>
                </div>

                <!-- 4. INPUT TARGET SPESIFIK (C1, C2, C3, C5) -->
                <div class="p-6 rounded-2xl bg-neutral-50/50 border border-neutral-100 space-y-6">
                    <div class="flex items-center gap-3 border-b border-neutral-100 pb-3">
                        <div class="h-8 w-8 rounded-lg bg-forest-800 text-white flex items-center justify-center text-sm font-bold shadow-sm">4</div>
                        <div>
                            <h3 class="font-extrabold text-neutral-800 text-base">Input Target Kriteria Aktual</h3>
                            <p class="text-neutral-500 text-[11px] font-medium">Sistem otomatis mengonversi angka yang Anda ketik sesuai bobot SPK.</p>
                        </div>
                    </div>

                    <input type="hidden" name="weights[C1]" value="">
                    <input type="hidden" name="weights[C2]" value="">
                    <input type="hidden" name="weights[C3]" value="">
                    <input type="hidden" name="weights[C4]" value="3">
                    <input type="hidden" name="weights[C5]" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <!-- C1: Harga -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider">C1 - Budget / Maksimal Harga Trip</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-500 font-black">Rp</span>
                                <!-- Minimum diubah menjadi 0 agar bisa mengetik bebas, step 1000 -->
                                <input type="number" name="target_c1" min="0" step="1000" placeholder="Contoh: 80000" class="w-full pl-12 pr-4 py-3 text-sm font-bold text-forest-900 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-forest-500 bg-white transition shadow-sm" oninput="updateIndikatorC1()" required>
                            </div>
                            
                            <!-- INFORMASI PAKET TERMURAH DI DASHBOARD -->
                            <?php if ($cheapestTrip): ?>
                            <div class="mt-2 p-2.5 bg-emerald-50 border border-emerald-100 rounded-xl flex items-start gap-2 shadow-sm">
                                <i class="fa-solid fa-tags text-emerald-500 mt-0.5"></i>
                                <div>
                                    <p class="text-[10px] font-extrabold text-emerald-900 uppercase tracking-wider">Info Paket Termurah Saat Ini</p>
                                    <p class="text-[11px] text-emerald-700 font-medium mt-0.5 leading-snug">
                                        Paket trip paling murah di sistem adalah <strong><?= htmlspecialchars($cheapestTrip['nama_paket']) ?></strong> dengan harga <strong>Rp <?= number_format($cheapestTrip['harga'], 0, ',', '.') ?></strong>. Anda bisa menginputkan budget di angka tersebut atau di bawahnya.
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>

                        </div>

                        <!-- C2: Ketinggian -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider">C2 - Target Ketinggian Gunung</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400"><i class="fa-solid fa-mountain-sun"></i></span>
                                <input type="number" name="target_c2" min="1000" max="4000" placeholder="Misal: 3000" class="w-full pl-11 pr-14 py-3 text-sm font-bold text-forest-900 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-forest-500 bg-white transition shadow-sm" required>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-neutral-400 text-[10px] font-extrabold">MDPL</span>
                            </div>
                        </div>

                        <!-- C5: Waktu Tempuh -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider">C5 - Batas Waktu Tempuh</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400"><i class="fa-regular fa-clock"></i></span>
                                <input type="number" name="target_c5" min="1" max="24" placeholder="Misal: 8" class="w-full pl-11 pr-14 py-3 text-sm font-bold text-forest-900 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-forest-500 bg-white transition shadow-sm" required>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-neutral-400 text-[10px] font-extrabold">JAM</span>
                            </div>
                        </div>

                        <!-- C3: Medan -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider">C3 - Preferensi Kesulitan Medan</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400"><i class="fa-solid fa-route"></i></span>
                                <select name="target_c3" onchange="updateIndikatorC3()" required class="w-full pl-11 pr-4 py-3 text-sm font-bold text-forest-900 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-forest-500 bg-white transition shadow-sm appearance-none cursor-pointer">
                                    <option value="" disabled selected>Pilih Tingkat Kesulitan...</option>
                                    <option value="1">Jalur landai, trek datar dominan (Aman untuk pemula)</option>
                                    <option value="2">Jalur bervariasi, tanjakan akar/tanah</option>
                                    <option value="3">Didominasi tanjakan terjal / tebing</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-neutral-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. FASILITAS (C4) -->
                <div class="p-6 rounded-2xl bg-neutral-50/50 border border-neutral-100 space-y-4">
                    <div class="flex items-center gap-3 border-b border-neutral-100 pb-3">
                        <div class="h-8 w-8 rounded-lg bg-forest-800 text-white flex items-center justify-center text-sm font-bold shadow-sm">5</div>
                        <div>
                            <h3 class="font-extrabold text-neutral-800 text-base">C4 &bull; Target Kelengkapan Fasilitas</h3>
                            <p class="text-neutral-500 text-[11px] font-medium">Centang fasilitas pendukung yang wajib ada di paket trip.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 pt-2">
                        <?php 
                        $default_checked = ['F4', 'F12'];
                        foreach ($facilities_list as$code => $fac):$isChecked = in_array($code,$default_checked) ? 'checked' : '';
                        ?>
                            <label class="flex items-start gap-3 p-3 rounded-xl border border-neutral-200 bg-white hover:border-emerald-400 transition cursor-pointer text-xs group shadow-sm [&:has(input:checked)]:border-emerald-500 [&:has(input:checked)]:bg-emerald-50/30">
                                <input type="checkbox" name="pref_facilities[]" value="<?php echo $code; ?>" <?php echo $isChecked; ?> onchange="updateIndikatorC4()"
                                       class="mt-0.5 h-4 w-4 text-emerald-600 rounded border-neutral-300 focus:ring-emerald-500">
                                <div class="space-y-0.5">
                                    <span class="font-bold text-neutral-800 group-hover:text-emerald-800 block leading-tight"><?php echo $fac['name']; ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-6 border-t border-neutral-100 flex flex-col sm:flex-row justify-between items-center gap-6">
                    <div class="text-xs text-neutral-500 text-center sm:text-left flex items-start gap-2.5 max-w-sm">
                        <i class="fa-solid fa-circle-info text-forest-600 text-base mt-0.5"></i>
                        <span class="leading-relaxed">Sistem cerdas akan menganalisis harga, ketinggian, serta menyesuaikan dengan riwayat kesehatan Anda.</span>
                    </div>

                    <button type="submit" id="btnSubmitForm" class="w-full sm:w-auto px-10 py-4 rounded-2xl text-white bg-gradient-to-r from-forest-800 to-forest-900 shadow-lg shadow-forest-800/25 hover:shadow-xl hover:shadow-forest-900/30 transition-all font-bold text-sm flex items-center justify-center gap-2 active:scale-95">
                        <i class="fa-solid fa-robot"></i> Proses Rekomendasi MOORA
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    // 1. DATA PENYAKIT & OBAT
    const penyakitData = {
        'asma': { 
            kategori: 'ringan', 
            obat: ['Inhaler / Ventolin pribadi', 'Jaket polar ekstra tebal', 'Obat pereda asma oral (jika ada)', 'Oksigen kaleng (Oxycan)'] 
        },
        'maag': { 
            kategori: 'ringan', 
            obat: ['Antasida / Promag / Mylanta', 'Obat penekan asam lambung (Omeprazole)', 'Camilan karbohidrat/biskuit di jalur', 'Tolak Angin cair'] 
        },
        'alergi_dingin': { 
            kategori: 'ringan', 
            obat: ['Obat Antihistamin (Cetirizine / CTM)', 'Krim/Minyak penghangat tubuh', 'Pakaian berlapis (Layering system)', 'Sleeping bag polar + thermal'] 
        },
        'jantung': { kategori: 'berat' },
        'epilepsi': { kategori: 'berat' },
        'patah_tulang': { kategori: 'berat' }
    };

    function checkPenyakit() {
        const val = document.getElementById('riwayat_penyakit').value;
        const warningBox = document.getElementById('medicalWarningBox');
        const obatBox = document.getElementById('medicineRecommendationBox');
        const btnSubmit = document.getElementById('btnSubmitForm');
        const obatList = document.getElementById('medicineList');

        // Reset display
        warningBox.classList.add('hidden');
        obatBox.classList.add('hidden');
        btnSubmit.disabled = false;
        btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed', 'from-gray-500', 'to-gray-600');
        btnSubmit.classList.add('from-forest-800', 'to-forest-900');
        btnSubmit.innerHTML = '<i class="fa-solid fa-robot"></i> Proses Rekomendasi MOORA';

        if (!val || val === 'tidak_ada') return;

        const data = penyakitData[val];
        
        // JIKA PENYAKIT BERAT (Blokir form submit)
        if (data.kategori === 'berat') {
            warningBox.classList.remove('hidden');
            btnSubmit.disabled = true; // Matikan tombol
            btnSubmit.classList.remove('from-forest-800', 'to-forest-900', 'hover:shadow-xl');
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed', 'from-gray-500', 'to-gray-600');
            btnSubmit.innerHTML = '<i class="fa-solid fa-ban"></i> Pendakian Dilarang';
        } 
        // JIKA PENYAKIT RINGAN (Tampilkan List Obat)
        else if (data.kategori === 'ringan') {
            obatBox.classList.remove('hidden');
            obatList.innerHTML = '';
            data.obat.forEach(item => {
                const li = document.createElement('li');
                li.className = "flex items-center gap-2";
                li.innerHTML = `<i class="fa-solid fa-circle-check text-blue-500 text-[10px]"></i> <span>${item}</span>`;
                obatList.appendChild(li);
            });
        }
    }

    // 2. FUNGSI BMI
    function calculateBMIPreview() {
        const berat = parseFloat(document.getElementById('input-berat').value) || 0;
        const tinggi = parseFloat(document.getElementById('input-tinggi').value) || 0;
        const bmiValEl = document.getElementById('bmiVal');
        const bmiBadge = document.getElementById('bmiBadge');

        if (berat > 0 && tinggi > 0) {
            const hM = tinggi / 100;
            const bmi = (berat / (hM * hM)).toFixed(1);
            bmiValEl.innerText = bmi;

            if (bmi < 18.5) {
                bmiBadge.innerText = 'Kurus';
                bmiBadge.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800';
            } else if (bmi <= 24.9) {
                bmiBadge.innerText = 'Ideal / Normal';
                bmiBadge.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800';
            } else if (bmi <= 29.9) {
                bmiBadge.innerText = 'Kelebihan Berat';
                bmiBadge.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800';
            } else {
                bmiBadge.innerText = 'Obesitas (Risiko)';
                bmiBadge.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-800 animate-pulse';
            }
        }
    }

    // 3. UPDATE INDIKATOR SPK C1-C4 (Konversi Otomatis)
    function updateIndikatorC1() {
        const val = parseFloat(document.querySelector('input[name="target_c1"]').value);
        const hidden = document.querySelector('input[name="weights[C1]"]');
        if (isNaN(val)) { hidden.value = ""; return; }
        
        if (val < 500000) hidden.value = 1;
        else if (val >= 500000 && val <= 750000) hidden.value = 2;
        else hidden.value = 3;
    }

    function updateIndikatorC3() {
        const val = document.querySelector('select[name="target_c3"]').value;
        const hidden = document.querySelector('input[name="weights[C3]"]');
        hidden.value = val || "";
    }

    function updateIndikatorC4() {
        const checkboxes = document.querySelectorAll('input[name="pref_facilities[]"]:checked');
        const hidden = document.querySelector('input[name="weights[C4]"]');
        if (checkboxes.length <= 4) hidden.value = 1;
        else if (checkboxes.length <= 8) hidden.value = 2;
        else hidden.value = 3;
    }

    document.addEventListener("DOMContentLoaded", () => {
        calculateBMIPreview();
        updateIndikatorC1();
        updateIndikatorC4(); 
    });

    // 4. LOADING SIMULATION & SUBMIT
    const loadingMessages = [
        "Menganalisis riwayat medis Anda...",
        "Memproses target harga (C1)...",
        "Menyesuaikan target MDPL & Medan (C2 & C3)...",
        "Menghitung kalkulasi Moora matriks...",
        "Menemukan kandidat paket trip paling cocok..."
    ];

    function handleFormSubmit(event) {
        event.preventDefault();
        
        // --- VALIDASI FORM ---
        const targetC1 = document.querySelector('input[name="target_c1"]').value;
        const targetC2 = document.querySelector('input[name="target_c2"]').value;
        const targetC3 = document.querySelector('select[name="target_c3"]').value;
        const targetC5 = document.querySelector('input[name="target_c5"]').value;
        const medis = document.querySelector('select[name="riwayat_penyakit"]').value;
        const errorBox = document.getElementById('errorValidation');

        if (!targetC1 || !targetC2 || !targetC3 || !targetC5 || !medis) {
            errorBox.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return; 
        }

        errorBox.classList.add('hidden');
        const form = event.target;
        const overlay = document.getElementById('loadingOverlay');
        const progressBar = document.getElementById('loadingBar');
        const messageEl = document.getElementById('loadingMessage');

        overlay.classList.remove('hidden');

        let progress = 0;
        let messageIndex = 0;

        const interval = setInterval(() => {
            progress += 1.5; 
            if (progress > 100) progress = 100;
            progressBar.style.width = `${progress}%`;

            if (Math.floor(progress) % 20 === 0 && messageIndex < loadingMessages.length) {
                messageEl.innerText = loadingMessages[messageIndex];
                messageIndex++;
            }

            if (progress === 100) {
                clearInterval(interval);
                setTimeout(() => { form.submit(); }, 400);
            }
        }, 25); 
    }
</script>

<?php include '../includes/footer.php'; ?>