<?php
// admin/kelola_kriteria.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Criteria.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('admin');

$criteriaClass = new Criteria($pdo);

$error = "";
$success = "";

// Process weights & types update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criteria'])) {
    try {
        $pdo->beginTransaction();
        
        $raw_weights = [];
        foreach ($_POST['criteria'] as $kode => $data) {
            $raw_weights[$kode] = floatval($data['bobot']);
        }
        
        $sum = array_sum($raw_weights);
        
        // Membulatkan hasil penjumlahan untuk menghindari masalah presisi desimal (floating-point)
        $sum_rounded_decimal = round($sum, 4);
        $sum_rounded_percent = round($sum, 2);

        // Validasi Ketat: Harus persis 1.0 atau 100
        if ($sum_rounded_decimal != 1.0000 && $sum_rounded_percent != 100.00) {
            throw new Exception("Error bobot tidak valid! Total keseluruhan bobot harus tepat berjumlah 1.0 atau 100. Total saat ini adalah: " . $sum);
        }

        // Save weights
        foreach ($_POST['criteria'] as $kode => $data) {
            $nama = trim($data['nama']);
            $jenis = trim($data['jenis']);
            
            // Konversi nilai ke desimal jika admin menggunakan input format puluhan/ratusan (1-100)
            $final_weight = ($sum_rounded_percent == 100.00) ? ($raw_weights[$kode] / 100) : $raw_weights[$kode];

            $criteriaClass->update($kode, $nama, $jenis, $final_weight);
        }

        $pdo->commit();
        $success = "Kriteria berhasil diperbarui! Komposisi bobot sudah tervalidasi sempurna.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

// Fetch all criteria
$criteria = $criteriaClass->getAll();

$current_page = 'admin_kriteria';
$page_title = 'Kelola Preferensi Kriteria - Panel Admin';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-12 bg-neutral-50/60 min-h-screen relative z-10 backdrop-blur-[2px]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-8 uppercase tracking-wider">
            <a href="dashboard.php" class="hover:text-forest-800 transition">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-emerald-700">Kelola Kriteria SPK</span>
        </div>

        <!-- Panel Utama Premium -->
        <div class="bg-white rounded-[2rem] p-8 sm:p-10 border border-neutral-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] space-y-8">
            <div class="border-b border-neutral-100 pb-6 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-gradient-to-br from-forest-50 to-emerald-100 text-forest-800 flex items-center justify-center rounded-2xl text-xl font-bold shadow-sm">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-emerald-800">Preferensi Default Kriteria MOORA</h1>
                        <p class="text-neutral-500 text-xs mt-1 font-medium">Mengatur bobot bawaan dan atribut parameter (Cost / Benefit) untuk algoritma.</p>
                    </div>
                </div>
                <a href="dashboard.php" class="px-5 py-2.5 rounded-xl bg-neutral-100 text-neutral-600 hover:bg-neutral-200 font-bold text-xs transition">
                    Kembali
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="p-4 bg-red-50 border border-red-200/60 text-red-800 text-sm font-semibold rounded-2xl flex items-center gap-3 animate-in fade-in zoom-in-95 duration-300">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="p-4 bg-emerald-50 border border-emerald-200/60 text-emerald-800 text-sm font-semibold rounded-2xl flex items-center gap-3 animate-in fade-in zoom-in-95 duration-300">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <form action="kelola_kriteria.php" method="POST" class="space-y-8">
                <div class="overflow-x-auto border border-neutral-100 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)]">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-neutral-50/80 border-b border-neutral-100 text-[10px] font-extrabold uppercase tracking-wider text-neutral-500">
                                <th class="py-5 px-6 w-24">Kode</th>
                                <th class="py-5 px-6">Nama Kriteria</th>
                                <th class="py-5 px-6 text-center w-52">Jenis (Atribut)</th>
                                <th class="py-5 px-6 text-center w-48">Bobot Input</th>
                                <th class="py-5 px-6 text-right w-36">Bobot Aktif (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 font-semibold text-neutral-700">
                            <?php foreach ($criteria as $c): 
                                $kode = $c['kode'];
                            ?>
                                <tr class="hover:bg-neutral-50/50 transition duration-200">
                                    <td class="py-5 px-6 text-forest-800 font-black text-base"><?php echo $kode; ?></td>
                                    <td class="py-5 px-6">
                                        <input type="text" name="criteria[<?php echo $kode; ?>][nama]" required
                                               value="<?php echo htmlspecialchars($c['nama']); ?>"
                                               class="w-full px-4 py-2.5 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm bg-white font-semibold transition">
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <select name="criteria[<?php echo $kode; ?>][jenis]" required
                                                class="w-full px-4 py-2.5 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm bg-white font-semibold cursor-pointer transition">
                                            <option value="benefit" <?php echo ($c['jenis'] === 'benefit') ? 'selected' : ''; ?>>Benefit (Maksimal)</option>
                                            <option value="cost" <?php echo ($c['jenis'] === 'cost') ? 'selected' : ''; ?>>Cost (Minimal)</option>
                                        </select>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <input type="number" name="criteria[<?php echo $kode; ?>][bobot]" required min="0.001" step="0.0001"
                                               value="<?php echo floatval($c['bobot']); ?>"
                                               class="w-full px-4 py-2.5 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm text-center font-bold text-neutral-800 transition">
                                    </td>
                                    <td class="py-5 px-6 text-right font-black text-forest-800 text-base">
                                        <?php echo number_format($c['bobot'] * 100, 2); ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="bg-amber-50/50 p-5 rounded-2xl border border-amber-200/60 flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-amber-600 text-lg mt-0.5"></i>
                    <div class="text-xs text-amber-900 space-y-1.5 leading-relaxed">
                        <p class="font-extrabold uppercase tracking-widest text-[10px]">Peringatan Validasi Pembobotan:</p>
                        <p>Total keseluruhan Bobot Input <strong>WAJIB berjumlah persis 1.0</strong> (jika Anda menggunakan desimal) atau <strong>100</strong> (jika Anda menggunakan format persen). Sistem akan membatalkan proses penyimpanan dan menampilkan pesan error jika terdapat kelebihan atau kekurangan nilai desimal sedikitpun.</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-neutral-100 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl text-white bg-forest-800 hover:bg-forest-900 shadow-lg shadow-forest-800/10 hover:shadow-xl hover:shadow-forest-900/20 transition-all font-bold text-sm flex items-center justify-center gap-2 active:scale-95 hover:-translate-y-0.5">
                        <i class="fa-regular fa-floppy-disk text-base"></i> Simpan Perubahan Kriteria
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>