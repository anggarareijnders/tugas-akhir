<?php
// vendor/kelola_edukasi.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Education.php';
require_once '../classes/Trip.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('vendor');

$vendor = Auth::getUser();
$vendorId = $vendor['id'];
$eduClass = new Education($pdo);
$tripClass = new Trip($pdo);

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$error = "";
$success = "";

// Get vendor's packages for dropdown selection
$myTrips = $tripClass->getAll('', $vendorId);

// 1. Delete Action
if ($action === 'delete' && $id > 0) {
    if ($eduClass->delete($id, $vendorId)) {
        header("Location: dashboard.php?status=edu_deleted");
        exit();
    } else {
        $error = "Gagal menghapus panduan edukasi.";
    }
}

// 2. Add / Edit Forms processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paketId = isset($_POST['paket_id']) ? intval($_POST['paket_id']) : 0;
    $gunung = trim($_POST['gunung']);
    $judul = trim($_POST['judul']);
    $konten = trim($_POST['konten']);
    $logistik = trim($_POST['logistik']);

    if (empty($gunung) || empty($judul) || empty($konten) || empty($logistik)) {
        $error = "Semua kolom (kecuali Paket Trip terkait) wajib diisi!";
    } else {
        if ($action === 'add') {
            if ($eduClass->create($vendorId, $paketId, $gunung, $judul, $konten, $logistik)) {
                header("Location: dashboard.php?status=edu_added");
                exit();
            } else {
                $error = "Gagal menyimpan panduan edukasi.";
            }
        } elseif ($action === 'edit' && $id > 0) {
            if ($eduClass->update($id, $vendorId, $paketId, $gunung, $judul, $konten, $logistik)) {
                header("Location: dashboard.php?status=edu_updated");
                exit();
            } else {
                $error = "Gagal memperbarui panduan edukasi.";
            }
        }
    }
}

// 3. Fetching details for Edit action
$editEdu = null;
if ($action === 'edit' && $id > 0) {
    $editEdu = $eduClass->getById($id);
    // Double check ownership
    if (!$editEdu || $editEdu['vendor_id'] != $vendorId) {
        header("Location: dashboard.php");
        exit();
    }
}

$current_page = 'vendor_edukasi';
$page_title = ($action === 'add' ? 'Tambah Edukasi' : ($action === 'edit' ? 'Edit Edukasi' : 'Kelola Edukasi')) . ' - Panel Vendor';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-12 bg-neutral-50 flex-grow">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="dashboard.php" class="hover:text-forest-800 transition">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600"><?php echo ($action === 'add') ? 'Tambah Edukasi' : (($action === 'edit') ? 'Edit Edukasi' : 'Kelola Edukasi'); ?></span>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl space-y-8">
            <div class="border-b border-neutral-100 pb-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-forest-50 text-forest-800 flex items-center justify-center rounded-xl text-lg font-bold">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-neutral-800"><?php echo ($action === 'add') ? 'Tambah Edukasi & Logistik' : 'Edit Edukasi & Logistik'; ?></h1>
                        <p class="text-neutral-500 text-xs mt-1">Buat panduan logistik pendakian yang interaktif bagi calon pendaki.</p>
                    </div>
                </div>
                <a href="dashboard.php" class="px-4 py-2 border rounded-xl text-neutral-600 hover:bg-neutral-50 font-bold text-xs transition">
                    Kembali
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="kelola_edukasi.php?action=<?php echo $action; ?><?php echo ($action === 'edit') ? '&id=' . $id : ''; ?>" method="POST" class="space-y-6">
                
                <!-- Gunung & Terkait Paket -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="gunung" class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">Gunung Tujuan</label>
                        <input type="text" name="gunung" id="gunung" required 
                               value="<?php echo $editEdu ? htmlspecialchars($editEdu['gunung']) : ''; ?>"
                               placeholder="Contoh: Semeru"
                               class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm">
                    </div>
                    
                    <div>
                        <label for="paket_id" class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">Tautkan ke Paket Trip Anda (Opsional)</label>
                        <select name="paket_id" id="paket_id" 
                                class="w-full px-3 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm">
                            <option value="0">-- Tidak Ditautkan ke Paket Tertentu --</option>
                            <?php foreach ($myTrips as $trip): ?>
                                <option value="<?php echo $trip['id']; ?>" <?php echo ($editEdu && $editEdu['paket_id'] == $trip['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($trip['nama_paket']); ?> (Gunung <?php echo htmlspecialchars($trip['gunung']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Judul -->
                <div>
                    <label for="judul" class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">Judul Artikel Panduan</label>
                    <input type="text" name="judul" id="judul" required 
                           value="<?php echo $editEdu ? htmlspecialchars($editEdu['judul']) : ''; ?>"
                           placeholder="Contoh: Panduan Logistik & Fisik Mendaki Gunung Semeru"
                           class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm">
                </div>

                <!-- Konten Panduan / Persiapan Fisik -->
                <div>
                    <label for="konten" class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">Konten / Artikel Persiapan Fisik & Medan</label>
                    <textarea name="konten" id="konten" rows="8" required
                              placeholder="Tuliskan info iklim gunung, tips latihan fisik, jalur yang disarankan, hal penting..."
                              class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm"><?php echo $editEdu ? htmlspecialchars($editEdu['konten']) : ''; ?></textarea>
                </div>

                <!-- Logistik / Equipment List -->
                <div class="p-5 rounded-2xl bg-neutral-50 border border-neutral-100">
                    <label for="logistik" class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-1">Daftar Barang Logistik (Wajib Bawa)</label>
                    <span class="block text-[10px] text-neutral-400 font-medium mb-3">Tuliskan nama barang baris demi baris (satu baris untuk satu barang) agar otomatis ter-generate sebagai checklist interaktif.</span>
                    
                    <textarea name="logistik" id="logistik" rows="6" required
                              placeholder="Jaket Tebal (Down Jacket)&#10;Sleeping Bag Thermal&#10;Trekking Pole&#10;Masker & Kacamata Pasir&#10;Headlamp"
                              class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm"><?php echo $editEdu ? htmlspecialchars($editEdu['logistik']) : ''; ?></textarea>
                </div>

                <div class="pt-4 border-t border-neutral-100 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl text-white bg-forest-800 hover:bg-forest-900 shadow-lg shadow-forest-800/10 hover:shadow-xl hover:shadow-forest-900/20 transition font-bold text-sm flex items-center justify-center gap-2 active:scale-95">
                        <i class="fa-regular fa-floppy-disk text-base"></i> Simpan Data Edukasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>