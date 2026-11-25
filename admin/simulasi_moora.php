<?php
// admin/simulasi_moora.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/Trip.php';
require_once '../classes/Moora.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('admin');

$moora = new Moora($pdo);
$tripClass = new Trip($pdo);
$dbCriteria = $moora->getCriteria();

$isExecuted = false;
$mooraResult = null;
$kategoriFilter = isset($_POST['kategori_filter']) ? $_POST['kategori_filter'] : '';
$dataSource = isset($_POST['data_source']) ? $_POST['data_source'] : 'database';

// Trigger execution of MOORA algorithm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trigger_moora'])) {
    $isExecuted = true;
    
    // Parse custom weights if provided
    $rawWeights = isset($_POST['weights']) ? $_POST['weights'] : [];
    $sumWeights = array_sum($rawWeights);
    
    $customWeights = [];
    if ($sumWeights > 0) {
        foreach ($rawWeights as $kode => $val) {
            $customWeights[$kode] = floatval($val) / $sumWeights;
        }
    }

    if ($dataSource === 'database') {
        $alternatives = $tripClass->getAll('', null, $kategoriFilter ?: null);
    } else {
        $alternatives = [];
    }

    $mooraResult = $moora->calculate($alternatives, !empty($customWeights) ? $customWeights : null);
} else {
    // Default execution on load for validation
    $alternatives = $tripClass->getAll();
    $mooraResult = $moora->calculate($alternatives);
    $isExecuted = true;
}

$current_page = 'admin_simulasi';
$page_title = 'Menjalankan Algoritma MOORA - LaTeX Render MathJax';
$base_path = "../";

include '../includes/header.php';
?>

<!-- MathJax Integration for Academic LaTeX Formula Rendering -->
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>

<section class="py-12 bg-neutral-50 flex-grow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="dashboard.php" class="hover:text-forest-800 transition">Dashboard Admin</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Menjalankan Algoritma MOORA (LaTeX MathJax)</span>
        </div>

        <!-- Page Header -->
        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl mb-8 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-neutral-100 pb-6">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 bg-emerald-100 text-emerald-800 rounded-2xl flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-800">Menjalankan Algoritma MOORA</h1>
                        <p class="text-neutral-500 text-xs sm:text-sm mt-1">
                            Antarmuka akademis dengan rendering rumus LaTeX MathJax profesional untuk kalkulasi matriks SPK MOORA.
                        </p>
                    </div>
                </div>

                <form method="POST" class="inline-block">
                    <input type="hidden" name="trigger_moora" value="1">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-forest-800 hover:bg-forest-900 text-white font-bold text-xs shadow-lg transition active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-play"></i> Pemicu Perhitungan Manual
                    </button>
                </form>
            </div>

            <!-- ACADEMIC MATHJAX LATEX FORMULAS EXPLANATION BOX -->
            <div class="p-6 rounded-2xl bg-neutral-900 text-white shadow-xl space-y-4 border border-neutral-800">
                <div class="flex items-center justify-between border-b border-neutral-800 pb-3">
                    <span class="text-xs font-black uppercase tracking-widest text-emerald-400 flex items-center gap-2">
                        <i class="fa-solid fa-square-root-variable"></i> Formulasi Matematika Metode MOORA (Multi-Objective Optimization on the basis of Ratio Analysis)
                    </span>
                    <span class="text-[10px] font-bold text-neutral-400 bg-neutral-800 px-2.5 py-1 rounded-md">MathJax v3 LaTeX</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs font-mono text-neutral-300">
                    <div class="p-4 rounded-xl bg-neutral-800/80 border border-neutral-700 space-y-2 text-center">
                        <span class="text-[10px] font-sans font-bold text-emerald-300 block">1. Matriks Keputusan</span>
                        <div class="text-base text-white py-1">
                            $$X = [x_{ij}]_{m \times n}$$
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-neutral-800/80 border border-neutral-700 space-y-2 text-center">
                        <span class="text-[10px] font-sans font-bold text-emerald-300 block">2. Normalisasi Rasio</span>
                        <div class="text-base text-white py-1">
                            $$X_{ij}^* = \frac{x_{ij}}{\sqrt{\sum_{i=1}^{m} x_{ij}^2}}$$
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-neutral-800/80 border border-neutral-700 space-y-2 text-center">
                        <span class="text-[10px] font-sans font-bold text-emerald-300 block">3. Matriks Terbobot</span>
                        <div class="text-base text-white py-1">
                            $$Y_{ij} = w_j \cdot X_{ij}^*$$
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-neutral-800/80 border border-neutral-700 space-y-2 text-center">
                        <span class="text-[10px] font-sans font-bold text-emerald-300 block">4. Nilai Optimasi</span>
                        <div class="text-base text-white py-1">
                            $$y_i = \sum_{j=1}^{g} Y_{ij} - \sum_{j=g+1}^{n} Y_{ij}$$
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parameters Filter Form -->
            <form method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <input type="hidden" name="trigger_moora" value="1">
                
                <div>
                    <label class="block text-xs font-bold text-neutral-600 uppercase tracking-wider mb-1.5">Sumber Data Paket</label>
                    <select name="data_source" class="w-full py-2.5 px-3 text-xs font-bold border border-neutral-200 rounded-xl bg-neutral-50">
                        <option value="database" <?php echo $dataSource === 'database' ? 'selected' : ''; ?>>Paket Trip Terdaftar (JOIN Vendor Database)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-neutral-600 uppercase tracking-wider mb-1.5">Filter Kategori Trip</label>
                    <select name="kategori_filter" class="w-full py-2.5 px-3 text-xs font-bold border border-neutral-200 rounded-xl bg-neutral-50">
                        <option value="">Semua Kategori (Camping & Tektok)</option>
                        <option value="Camping" <?php echo $kategoriFilter === 'Camping' ? 'selected' : ''; ?>>Camping (Menginap)</option>
                        <option value="Tektok" <?php echo $kategoriFilter === 'Tektok' ? 'selected' : ''; ?>>Tektok (Tanpa Menginap)</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                        <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter & Eksekusi
                    </button>
                </div>
            </form>
        </div>

        <?php if ($mooraResult && $mooraResult['success']): 
            $results = $mooraResult['results'];
            $steps = $mooraResult['steps'];
            $criteria = $mooraResult['criteria'];
        ?>
            <!-- STEP-BY-STEP MATRICES DISPLAY -->
            <div class="space-y-8">
                
                <!-- 1. Matriks Keputusan (X) -->
                <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-lg space-y-4">
                    <h3 class="text-lg font-extrabold text-neutral-800 flex items-center gap-2">
                        <span class="h-7 w-7 rounded-lg bg-forest-800 text-white text-xs flex items-center justify-center font-bold">1</span>
                        Matriks Keputusan Skala Ordinal \(X = [x_{ij}]_{m \times n}\)
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-neutral-100 text-neutral-700 uppercase font-bold border-b border-neutral-200">
                                    <th class="p-3">Nama Paket / Gunung</th>
                                    <th class="p-3">Vendor Dikelola</th>
                                    <th class="p-3">Kategori</th>
                                    <th class="p-3">C1 (Harga)</th>
                                    <th class="p-3">C2 (Ketinggian)</th>
                                    <th class="p-3">C3 (Medan)</th>
                                    <th class="p-3">C4 (Fasilitas)</th>
                                    <th class="p-3">C5 (Waktu)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 font-medium text-neutral-800">
                                <?php foreach ($steps['matrixX'] as $id => $row): 
                                    $info = $steps['altInfo'][$id];
                                ?>
                                    <tr class="hover:bg-neutral-50">
                                        <td class="p-3 font-bold"><?php echo htmlspecialchars($info['nama']); ?></td>
                                        <td class="p-3 font-semibold text-emerald-800"><?php echo htmlspecialchars($info['vendor']); ?></td>
                                        <td class="p-3"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold"><?php echo htmlspecialchars($info['kategori']); ?></span></td>
                                        <td class="p-3"><?php echo $row['C1']; ?></td>
                                        <td class="p-3"><?php echo $row['C2']; ?></td>
                                        <td class="p-3"><?php echo $row['C3']; ?></td>
                                        <td class="p-3"><?php echo $row['C4']; ?></td>
                                        <td class="p-3"><?php echo $row['C5']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Pembagi & Matriks Normalisasi (R) -->
                <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-lg space-y-4">
                    <h3 class="text-lg font-extrabold text-neutral-800 flex items-center gap-2">
                        <span class="h-7 w-7 rounded-lg bg-forest-800 text-white text-xs flex items-center justify-center font-bold">2</span>
                        Matriks Normalisasi Ternormalisasi \(X_{ij}^* = \frac{x_{ij}}{\sqrt{\sum x_{ij}^2}}\)
                    </h3>
                    
                    <div class="p-3 bg-neutral-50 rounded-xl text-xs font-semibold text-neutral-600 flex flex-wrap gap-4 border border-neutral-200">
                        <span><strong>Pembagi Akar Kuadrat \(\sqrt{\sum x_{ij}^2}\):</strong></span>
                        <?php foreach ($steps['dividers'] as $k => $v): ?>
                            <span><?php echo $k; ?>: <strong class="text-emerald-700"><?php echo number_format($v, 4); ?></strong></span>
                        <?php endforeach; ?>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-neutral-100 text-neutral-700 uppercase font-bold border-b border-neutral-200">
                                    <th class="p-3">Nama Paket</th>
                                    <th class="p-3">R_C1</th>
                                    <th class="p-3">R_C2</th>
                                    <th class="p-3">R_C3</th>
                                    <th class="p-3">R_C4</th>
                                    <th class="p-3">R_C5</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 font-mono text-neutral-700">
                                <?php foreach ($steps['matrixR'] as $id => $row): ?>
                                    <tr class="hover:bg-neutral-50">
                                        <td class="p-3 font-bold font-sans text-neutral-800"><?php echo htmlspecialchars($steps['altInfo'][$id]['nama']); ?></td>
                                        <td class="p-3"><?php echo number_format($row['C1'], 4); ?></td>
                                        <td class="p-3"><?php echo number_format($row['C2'], 4); ?></td>
                                        <td class="p-3"><?php echo number_format($row['C3'], 4); ?></td>
                                        <td class="p-3"><?php echo number_format($row['C4'], 4); ?></td>
                                        <td class="p-3"><?php echo number_format($row['C5'], 4); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Matriks Terbobot & Perhitungan Optimasi Yi -->
                <div class="bg-white rounded-3xl p-6 border border-neutral-100 shadow-lg space-y-4">
                    <h3 class="text-lg font-extrabold text-neutral-800 flex items-center gap-2">
                        <span class="h-7 w-7 rounded-lg bg-forest-800 text-white text-xs flex items-center justify-center font-bold">3</span>
                        Matriks Terbobot \(Y_{ij} = w_j \cdot X_{ij}^*\) & Nilai Optimasi \(y_i = \sum \text{Benefit} - \sum \text{Cost}\)
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-neutral-100 text-neutral-700 uppercase font-bold border-b border-neutral-200">
                                    <th class="p-3">Rank</th>
                                    <th class="p-3">Nama Paket</th>
                                    <th class="p-3">Vendor</th>
                                    <th class="p-3">SUM Benefit</th>
                                    <th class="p-3">SUM Cost</th>
                                    <th class="p-3">Skor Optimasi (\(y_i\))</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 font-medium">
                                <?php foreach ($results as $res): ?>
                                    <tr class="hover:bg-neutral-50">
                                        <td class="p-3 font-extrabold text-emerald-800">#<?php echo $res['rank']; ?></td>
                                        <td class="p-3 font-bold text-neutral-800"><?php echo htmlspecialchars($res['nama']); ?></td>
                                        <td class="p-3 font-semibold text-neutral-600"><?php echo htmlspecialchars($res['vendor']); ?></td>
                                        <td class="p-3 text-emerald-700 font-semibold">+<?php echo number_format($res['benefit'], 4); ?></td>
                                        <td class="p-3 text-red-600 font-semibold">-<?php echo number_format($res['cost'], 4); ?></td>
                                        <td class="p-3 font-black text-forest-800 text-sm"><?php echo number_format($res['yi'], 4); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
