<?php
// vendor/kelola_paket.php
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

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$error = "";
$success = "";

// Define all facilities (F1 to F12)
$facilities_list = [
    'F1' => ['name' => 'Tenda Dome', 'desc' => 'Penyediaan tenda dome sebagai tempat berlindung kelompok.'],
    'F2' => ['name' => 'Tas Gunung (Carrier)', 'desc' => 'Fasilitas peminjaman tas gunung (carrier) bagi pendaki.'],
    'F3' => ['name' => 'Sleeping Bag', 'desc' => 'Penyediaan kantong tidur (sleeping bag) demi mencegah hipotermia.'],
    'F4' => ['name' => 'Simaksi (Tiket Pendakian)', 'desc' => 'Pengurusan perizinan, asuransi, dan tiket masuk kawasan pendakian.'],
    'F5' => ['name' => 'Transportasi Ojek', 'desc' => 'Fasilitas transportasi ojek untuk memangkas jarak pendaftaran ke start jalur.'],
    'F6' => ['name' => 'Homestay / Basecamp', 'desc' => 'Penyediaan tempat singgah sebelum memulai pendakian.'],
    'F7' => ['name' => 'Buah-buahan Segar', 'desc' => 'Suplai logistik buah segar sebagai penunjang vitamin dan hidrasi.'],
    'F8' => ['name' => 'Jasa Porter Tim', 'desc' => 'Jasa porter tim untuk membawa logistik kelompok dan peralatan berat.'],
    'F9' => ['name' => 'Atribut / Stiker', 'desc' => 'Pemberian suvenir atau stiker resmi kenang-kenangan dari pihak vendor.'],
   'F10' => ['name' => 'Transportasi PP (Mepo)', 'desc' => 'Fasilitas transportasi antar-jemput dari titik kumpul (Meeting Point) ke Basecamp.'],
    'F11' => ['name' => 'Makan Selama Pendakian', 'desc' => 'Makan selama pendakian dimulai dari basecamp hingga selesai.'],
    'F12' => ['name' => 'P3K Standar', 'desc' => 'Kesiapan penanganan medis pertama apabila terjadi kecelakaan di jalur.']
];

// 1. Delete Action
if ($action === 'delete' && $id > 0) {
    if ($tripClass->delete($id, $vendorId)) {
        header("Location: dashboard.php?status=deleted");
        exit();
    } else {
        $error = "Gagal menghapus paket trip.";
    }
}

// 2. Add / Edit Forms processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_paket']);
    $deskripsi = trim($_POST['deskripsi']);
    $gunung = trim($_POST['gunung']);
    $kategori = isset($_POST['kategori']) ? trim($_POST['kategori']) : 'Camping';
    
    // --- TAMBAHAN: Menangkap input status kuota ---
    $status_kuota = isset($_POST['status_kuota']) ? trim($_POST['status_kuota']) : 'Tersedia';
    
    $harga = floatval($_POST['c1_harga']);
    $ketinggian = intval($_POST['c2_ketinggian']);
    $medan = intval($_POST['c3_medan']);
    
    // Process facilities checkboxes for C4
    $checked_facilities = isset($_POST['c4_fasilitas']) ? $_POST['c4_fasilitas'] : [];
    $fasilitas_string = implode(',', $checked_facilities);

    $waktu = intval($_POST['c5_waktu_tempuh']);
    
    // Dynamic segments & contacts input parsing
    $rute_segments = isset($_POST['rute_segments']) ? $_POST['rute_segments'] : [];
    $kontak_darurat = isset($_POST['kontak_darurat']) ? $_POST['kontak_darurat'] : [];

    // --- LOGIKA UPLOAD GAMBAR ---
    $gambar = isset($_POST['old_gambar']) ? $_POST['old_gambar'] : ''; 

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/paket/';
        
        // Buat folder jika belum ada
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $targetFilePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFilePath)) {
                $gambar = 'uploads/paket/' . $fileName; // Path yang disimpan di database
            } else {
                $error = "Terjadi kesalahan saat menyimpan file gambar ke server.";
            }
        } else {
            $error = "Format file gambar tidak didukung! Hanya JPG, JPEG, PNG, atau WEBP.";
        }
    }

    if (empty($error)) {
        if (empty($nama) || empty($gunung) || empty($kategori) || $harga <= 0 || $ketinggian <= 0 || $medan < 1 || $medan > 3 || empty($fasilitas_string) || $waktu <= 0) {
            $error = "Semua input utama wajib diisi dengan benar! Pilih Kategori dan minimal 1 fasilitas.";
        } else {
            if ($action === 'add') {
                // TAMBAHAN: Menyelipkan $status_kuota ke dalam fungsi create
                $newId = $tripClass->create($vendorId, $nama, $deskripsi, $gunung, $kategori, $harga, $ketinggian, $medan, $fasilitas_string, $waktu, $gambar, $status_kuota);
                if ($newId) {
                    $tripClass->saveRuteSegments($newId, $rute_segments);
                    $tripClass->saveKontakDarurat($newId, $kontak_darurat);
                    header("Location: dashboard.php?status=added");
                    exit();
                } else {
                    $error = "Gagal menyimpan paket trip baru.";
                }
            } elseif ($action === 'edit' && $id > 0) {
                // TAMBAHAN: Menyelipkan $status_kuota ke dalam fungsi update
                if ($tripClass->update($id, $vendorId, $nama, $deskripsi, $gunung, $kategori, $harga, $ketinggian, $medan, $fasilitas_string, $waktu, $gambar, $status_kuota)) {
                    $tripClass->saveRuteSegments($id, $rute_segments);
                    $tripClass->saveKontakDarurat($id, $kontak_darurat);
                    header("Location: dashboard.php?status=updated");
                    exit();
                } else {
                    $error = "Gagal memperbarui paket trip.";
                }
            }
        }
    }
}

// 3. Fetching Trip details for Edit action
$editTrip = null;
$active_facilities = [];
$existingSegments = [];
$existingContacts = [];

if ($action === 'edit' && $id > 0) {
    $editTrip = $tripClass->getById($id);
    if (!$editTrip || $editTrip['vendor_id'] != $vendorId) {
        header("Location: dashboard.php");
        exit();
    }
    
    if (!empty($editTrip['c4_fasilitas'])) {
        $active_facilities = explode(',', $editTrip['c4_fasilitas']);
    }

    $existingSegments = $tripClass->getRuteSegments($id);
    $existingContacts = $tripClass->getKontakDarurat($id);
}

$current_page = 'vendor_paket';
$page_title = ($action === 'add' ? 'Tambah Paket' : ($action === 'edit' ? 'Edit Paket' : 'Kelola Paket')) . ' - Panel Vendor';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-12 bg-neutral-50 flex-grow">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="dashboard.php" class="hover:text-forest-800 transition">Dashboard Vendor</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600"><?php echo ($action === 'add') ? 'Tambah Paket' : (($action === 'edit') ? 'Edit Paket' : 'Kelola Paket'); ?></span>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl space-y-8">
            <div class="border-b border-neutral-100 pb-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-forest-50 text-forest-800 flex items-center justify-center rounded-xl text-lg font-bold">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-neutral-800">
                            <?php echo ($action === 'add') ? 'Tambah Paket Trip Baru' : 'Edit Paket Trip'; ?>
                        </h1>
                        <p class="text-neutral-500 text-xs mt-1">Lengkapi parameter kriteria SPK, kategori trip, segmentasi pos rute, dan kontak darurat lokal.</p>
                    </div>
                </div>
                <a href="dashboard.php" class="px-4 py-2 border border-neutral-200 rounded-xl text-neutral-600 hover:bg-neutral-50 font-bold text-xs transition">
                    Batal
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-xl flex items-center gap-3 font-semibold">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form action="kelola_paket.php?action=<?php echo $action; ?><?php echo $id > 0 ? '&id='.$id : ''; ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
                
                <!-- 1. UTAMA & KATEGORI -->
                <div class="space-y-4">
                    <h3 class="text-sm font-extrabold text-neutral-800 uppercase tracking-wider border-b pb-2">Informasi Dasar & Kategori Trip</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="nama_paket" class="block text-xs font-bold text-neutral-700 mb-1.5">Nama Paket Trip *</label>
                            <input type="text" id="nama_paket" name="nama_paket" required
                                   value="<?php echo htmlspecialchars($editTrip['nama_paket'] ?? ''); ?>"
                                   placeholder="Contoh: Eksplorasi Puncak Mahameru 3D2N"
                                   class="w-full px-4 py-2.5 text-xs font-bold border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-white">
                        </div>

                        <div>
                            <label for="gunung" class="block text-xs font-bold text-neutral-700 mb-1.5">Nama Gunung *</label>
                            <input type="text" id="gunung" name="gunung" required
                                   value="<?php echo htmlspecialchars($editTrip['gunung'] ?? ''); ?>"
                                   placeholder="Contoh: Semeru, Gede, Rinjani"
                                   class="w-full px-4 py-2.5 text-xs font-bold border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-white">
                        </div>

                        <div>
                            <label for="kategori" class="block text-xs font-bold text-neutral-700 mb-1.5">Kategori Trip (Wajib) *</label>
                            <select id="kategori" name="kategori" required
                                    class="w-full px-4 py-2.5 text-xs font-bold border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-emerald-50 text-emerald-900">
                                <option value="Camping" <?php echo (($editTrip['kategori'] ?? '') === 'Camping') ? 'selected' : ''; ?>>Camping (Menginap di Tenda)</option>
                                <option value="Tektok" <?php echo (($editTrip['kategori'] ?? '') === 'Tektok') ? 'selected' : ''; ?>>Tektok (Tanpa Menginap / 1 Hari)</option>
                            </select>
                        </div>
                        
                        <!-- TAMBAHAN: FORM INPUT STATUS KUOTA -->
                        <div class="sm:col-span-2">
                            <label for="status_kuota" class="block text-xs font-bold text-neutral-700 mb-1.5">Status Kuota Paket *</label>
                            <select id="status_kuota" name="status_kuota" required
                                    class="w-full px-4 py-2.5 text-xs font-bold border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-white">
                                <option value="Tersedia" <?php echo (($editTrip['status_kuota'] ?? 'Tersedia') === 'Tersedia') ? 'selected' : ''; ?>>Tersedia (Aktif)</option>
                                <option value="Penuh" <?php echo (($editTrip['status_kuota'] ?? '') === 'Penuh') ? 'selected' : ''; ?>>Penuh (Sold Out / Tutup)</option>
                            </select>
                            <p class="text-[10px] text-neutral-400 mt-1">Jika ditandai "Penuh", paket ini otomatis tidak akan muncul pada rekomendasi MOORA pendaki.</p>
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-xs font-bold text-neutral-700 mb-1.5">Deskripsi Singkat Paket</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                                  placeholder="Jelaskan keunggulan dan ringkasan fasilitas paket trip Anda..."
                                  class="w-full px-4 py-2.5 text-xs font-medium border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-white"><?php echo htmlspecialchars($editTrip['deskripsi'] ?? ''); ?></textarea>
                    </div>

                    <div>
                        <label for="gambar" class="block text-xs font-bold text-neutral-700 mb-1.5">Upload Foto/Gambar Paket</label>
                        
                        <!-- Menampilkan gambar lama jika sedang edit -->
                        <?php if (!empty($editTrip['gambar'])): ?>
                            <div class="mb-3">
                                <img src="../<?php echo htmlspecialchars($editTrip['gambar']); ?>" alt="Gambar Paket" class="w-32 h-24 object-cover rounded-xl border border-neutral-200 shadow-sm">
                                <input type="hidden" name="old_gambar" value="<?php echo htmlspecialchars($editTrip['gambar']); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <input type="file" id="gambar" name="gambar" accept="image/*"
                               class="w-full px-4 py-2 text-xs font-medium border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100">
                        <p class="text-[10px] text-neutral-400 mt-1">Format didukung: JPG, PNG, WEBP. Kosongkan jika tidak ingin mengubah gambar saat edit.</p>
                    </div>
                </div>

                <!-- 2. PARAMETER KRITERIA MOORA (C1 - C5) -->
                <div class="space-y-4">
                    <h3 class="text-sm font-extrabold text-neutral-800 uppercase tracking-wider border-b pb-2">Parameter Kriteria MOORA (C1 - C5)</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="c1_harga" class="block text-xs font-bold text-neutral-700 mb-1.5">C1 &bull; Harga Paket (Rp) *</label>
                            <input type="number" id="c1_harga" name="c1_harga" required min="10000" step="5000"
                                   value="<?php echo $editTrip['c1_harga'] ?? '750000'; ?>"
                                   class="w-full px-4 py-2.5 text-xs font-bold border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-white">
                        </div>

                        <div>
                            <label for="c2_ketinggian" class="block text-xs font-bold text-neutral-700 mb-1.5">C2 &bull; Ketinggian Puncak (mdpl) *</label>
                            <input type="number" id="c2_ketinggian" name="c2_ketinggian" required min="500"
                                   value="<?php echo $editTrip['c2_ketinggian'] ?? '2958'; ?>"
                                   class="w-full px-4 py-2.5 text-xs font-bold border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-white">
                        </div>

                        <div>
                            <label for="c3_medan" class="block text-xs font-bold text-neutral-700 mb-1.5">C3 &bull; Tingkat Kesulitan Medan *</label>
                            <select id="c3_medan" name="c3_medan" required
                                    class="w-full px-4 py-2.5 text-xs font-bold border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-white">
                                <option value="1" <?php echo (($editTrip['c3_medan'] ?? 2) == 1) ? 'selected' : ''; ?>>Skala 1 - Mudah (Jalur Landai & Tertata)</option>
                                <option value="2" <?php echo (($editTrip['c3_medan'] ?? 2) == 2) ? 'selected' : ''; ?>>Skala 2 - Sedang (Tanjakan Tanah & Akar)</option>
                                <option value="3" <?php echo (($editTrip['c3_medan'] ?? 2) == 3) ? 'selected' : ''; ?>>Skala 3 - Sulit (Terjal / Pasir & Batu Lepas)</option>
                            </select>
                        </div>

                        <div>
                            <label for="c5_waktu_tempuh" class="block text-xs font-bold text-neutral-700 mb-1.5">C5 &bull; Total Waktu Tempuh Ke Camp Area (Jam) *</label>
                            <input type="number" id="c5_waktu_tempuh" name="c5_waktu_tempuh" required min="1" max="48"
                                   value="<?php echo $editTrip['c5_waktu_tempuh'] ?? '6'; ?>"
                                   class="w-full px-4 py-2.5 text-xs font-bold border border-neutral-200 rounded-xl focus:ring-2 focus:ring-forest-500 bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-neutral-700 mb-2">C4 &bull; Fasilitas Paket Disediakan *</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 bg-neutral-50 p-4 rounded-2xl border border-neutral-200">
                            <?php foreach ($facilities_list as $code => $fac): 
                                $isChecked = in_array($code, $active_facilities) ? 'checked' : '';
                            ?>
                                <label class="flex items-start gap-2 p-2 rounded-lg bg-white border border-neutral-200 hover:border-forest-400 cursor-pointer text-xs transition">
                                    <input type="checkbox" name="c4_fasilitas[]" value="<?php echo $code; ?>" <?php echo $isChecked; ?>
                                           class="mt-0.5 h-3.5 w-3.5 text-forest-800 rounded border-neutral-300">
                                    <div>
                                        <span class="font-bold text-neutral-800 block"><?php echo $fac['name']; ?></span>
                                        <span class="text-[9px] text-neutral-400 block"><?php echo $code; ?></span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- 3. DYNAMIC REPEATER: SEGMENTASI JALUR & MEDAN -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <h3 class="text-sm font-extrabold text-neutral-800 uppercase tracking-wider">Dynamic Input: Segmentasi Jalur & Breakdown Pos</h3>
                            <p class="text-xs text-neutral-500">Definisikan rute perjalanan per segmen (misal: Basecamp &rarr; Pos 1).</p>
                        </div>
                        <button type="button" onclick="addRouteRow()" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 transition shadow">
                            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Segmen Rute
                        </button>
                    </div>

                    <div id="routeRowsContainer" class="space-y-3">
                        <!-- Dynamic Rows -->
                    </div>
                </div>

                <!-- 4. DYNAMIC REPEATER: KONTAK DARURAT / PORTER LOKAL -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <h3 class="text-sm font-extrabold text-neutral-800 uppercase tracking-wider">Dynamic Input: Kontak Darurat & Porter Lokal</h3>
                            <p class="text-xs text-neutral-500">Daftar kontak penting untuk mendukung keselamatan pendaki.</p>
                        </div>
                        <button type="button" onclick="addContactRow()" class="px-3 py-1.5 rounded-lg bg-forest-800 hover:bg-forest-900 text-white font-bold text-xs flex items-center gap-1.5 transition shadow">
                            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Kontak
                        </button>
                    </div>

                    <div id="contactRowsContainer" class="space-y-3">
                        <!-- Dynamic Rows -->
                    </div>
                </div>

                <!-- SUBMIT BUTTONS -->
                <div class="pt-6 border-t border-neutral-100 flex justify-end gap-3">
                    <a href="dashboard.php" class="px-6 py-3 rounded-xl border border-neutral-200 text-neutral-600 font-bold text-xs hover:bg-neutral-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-forest-800 hover:bg-forest-900 text-white font-bold text-xs shadow-lg transition active:scale-95">
                        <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Paket Trip
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Dynamic Repeater JavaScript -->
<script>
    const initialSegments = <?php echo json_encode($existingSegments); ?>;
    const initialContacts = <?php echo json_encode($existingContacts); ?>;

    let segmentIndex = 0;
    let contactIndex = 0;

    function addRouteRow(data = {}) {
        const container = document.getElementById('routeRowsContainer');
        const idx = segmentIndex++;
        
        const row = document.createElement('div');
        row.className = "p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 space-y-3 relative group";
        row.id = `route-row-${idx}`;

        row.innerHTML = `
            <div class="flex justify-between items-center border-b border-neutral-200/60 pb-2">
                <span class="text-xs font-extrabold text-emerald-800">Segmen Pos Rute #${idx + 1}</span>
                <button type="button" onclick="removeRow('route-row-${idx}')" class="text-red-500 hover:text-red-700 text-xs font-bold">
                    <i class="fa-solid fa-trash-can"></i> Hapus
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-neutral-600 mb-1">Pos Asal</label>
                    <input type="text" name="rute_segments[${idx}][pos_asal]" value="${data.pos_asal || ''}" placeholder="misal: Basecamp Putri" class="w-full p-2 border border-neutral-300 rounded-lg bg-white font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-neutral-600 mb-1">Pos Tujuan</label>
                    <input type="text" name="rute_segments[${idx}][pos_tujuan]" value="${data.pos_tujuan || ''}" placeholder="misal: Pos 1 Tanah Merah" class="w-full p-2 border border-neutral-300 rounded-lg bg-white font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-neutral-600 mb-1">Waktu Tempuh (Menit)</label>
                    <input type="number" name="rute_segments[${idx}][waktu_tempuh_menit]" value="${data.waktu_tempuh_menit || '60'}" class="w-full p-2 border border-neutral-300 rounded-lg bg-white font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-neutral-600 mb-1">Elevasi Gain (mdpl)</label>
                    <input type="number" name="rute_segments[${idx}][elevasi_gain]" value="${data.elevasi_gain || '250'}" class="w-full p-2 border border-neutral-300 rounded-lg bg-white font-semibold">
                </div>
                <div class="sm:col-span-4">
                    <label class="block font-bold text-neutral-600 mb-1">Deskripsi Kondisi Medan</label>
                    <input type="text" name="rute_segments[${idx}][kondisi_medan]" value="${data.kondisi_medan || ''}" placeholder="misal: Jalur tanah padat berlandai & lebat hutan cemara" class="w-full p-2 border border-neutral-300 rounded-lg bg-white font-medium">
                </div>
            </div>
        `;
        container.appendChild(row);
    }

    function addContactRow(data = {}) {
        const container = document.getElementById('contactRowsContainer');
        const idx = contactIndex++;

        const row = document.createElement('div');
        row.className = "p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 space-y-3 relative group";
        row.id = `contact-row-${idx}`;

        row.innerHTML = `
            <div class="flex justify-between items-center border-b border-neutral-200/60 pb-2">
                <span class="text-xs font-extrabold text-forest-800">Kontak Darurat #${idx + 1}</span>
                <button type="button" onclick="removeRow('contact-row-${idx}')" class="text-red-500 hover:text-red-700 text-xs font-bold">
                    <i class="fa-solid fa-trash-can"></i> Hapus
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-neutral-600 mb-1">Nama Kontak</label>
                    <input type="text" name="kontak_darurat[${idx}][nama]" value="${data.nama || ''}" placeholder="misal: Pak Slamet" class="w-full p-2 border border-neutral-300 rounded-lg bg-white font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-neutral-600 mb-1">Peran / Jabatan</label>
                    <input type="text" name="kontak_darurat[${idx}][peran]" value="${data.peran || ''}" placeholder="misal: Chief Porter / Medis P3K" class="w-full p-2 border border-neutral-300 rounded-lg bg-white font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-neutral-600 mb-1">Nomor Telepon / HP</label>
                    <input type="text" name="kontak_darurat[${idx}][no_hp]" value="${data.no_hp || ''}" placeholder="081234567890" class="w-full p-2 border border-neutral-300 rounded-lg bg-white font-semibold">
                </div>
            </div>
        `;
        container.appendChild(row);
    }

    function removeRow(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    // Init rows on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        if (initialSegments && initialSegments.length > 0) {
            initialSegments.forEach(s => addRouteRow(s));
        } else {
            addRouteRow({ pos_asal: 'Basecamp', pos_tujuan: 'Pos 1', waktu_tempuh_menit: 60, elevasi_gain: 250, kondisi_medan: 'Jalur tanah padat landai' });
        }

        if (initialContacts && initialContacts.length > 0) {
            initialContacts.forEach(c => addContactRow(c));
        } else {
            addContactRow({ nama: 'Ketua Basecamp', peran: 'Informasi & SAR', no_hp: '0812-3456-7890' });
        }
    });
</script>

<?php include '../includes/footer.php'; ?>