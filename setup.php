<?php
// setup.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$db = new Database();
$pdo = $db->getConnection();

$message = "";
$success = true;

try {
    // 1. Create database if not exists and select it
    $pdo->exec("CREATE DATABASE IF NOT EXISTS spk_moora_trip");
    $pdo->exec("USE spk_moora_trip");
    $message .= "<li class='text-green-700 font-semibold'>✓ Database 'spk_moora_trip' berhasil dibuat/ditemukan.</li>";

    // 2. Read and execute database_schema.sql
    $sqlFile = __DIR__ . '/database_schema.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Berkas 'database_schema.sql' tidak ditemukan!");
    }

    $sqlContent = file_get_contents($sqlFile);
    $pdo->exec($sqlContent);

    $message .= "<li class='text-green-700 font-semibold'>✓ Seluruh tabel (users, kriteria, paket_trip, rute_segmentasi, kontak_darurat, laporan_terpilih, edukasi) berhasil dibuat dari database_schema.sql.</li>";
    $message .= "<li class='text-green-700 font-semibold'>✓ Data Seeder Murni Realistis (Gunung Gede via Putri oleh Kuya Journey & Gunung Pangrango via Cibodas oleh CFD Traveller) berhasil dimasukkan.</li>";

} catch (Exception $e) {
    $success = false;
    $message .= "<li class='text-red-700 font-semibold'>✗ Terjadi Kesalahan SQL: " . $e->getMessage() . "</li>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database SPK MOORA (Reset & Real Seeder)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-neutral-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl border border-neutral-100">
        <div>
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 text-emerald-800 text-3xl">
                <i class="fa-solid fa-mountain animate-bounce"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-neutral-900">
                Setup Database (Reset)
            </h2>
            <p class="mt-2 text-center text-sm text-neutral-600">
                SPK MOORA Pendakian Gunung - Real Seeder Installed
            </p>
        </div>

        <div class="mt-8 space-y-4">
            <div class="bg-neutral-100 p-4 rounded-xl max-h-60 overflow-y-auto border border-neutral-200">
                <ul class="text-xs space-y-2 list-none pl-0">
                    <?php echo $message; ?>
                </ul>
            </div>

            <?php if ($success): ?>
                <div class="rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-circle-check text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">
                                Reset & Seeding Realistis Sukses!
                            </h3>
                            <div class="mt-2 text-xs text-green-700 space-y-1">
                                <p>Tabel relasional & seeder riil Gunung Gede (Kuya Journey) & Pangrango (CFD Traveller) berhasil disiapkan.</p>
                                <p class="font-semibold mt-1">Akun Login Bawaan:</p>
                                <ul class="list-disc pl-4">
                                    <li><strong>Admin:</strong> admin / admin123</li>
                                    <li><strong>Vendor Kuya:</strong> vendor_kuya / vendor123</li>
                                    <li><strong>Vendor CFD:</strong> vendor_cfd / vendor123</li>
                                    <li><strong>Pendaki:</strong> pendaki_sejati / pendaki123</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="index.php" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-emerald-800 hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-700 transition duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fa-solid fa-arrow-right text-emerald-500 group-hover:text-emerald-400"></i>
                        </span>
                        Masuk ke Landing Page
                    </a>
                </div>
            <?php else: ?>
                <div class="rounded-md bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-circle-xmark text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Instalasi Gagal
                            </h3>
                            <div class="mt-2 text-xs text-red-700">
                                <p>Terjadi kegagalan koneksi atau query. Pastikan MySQL menyala.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="setup.php" class="w-full flex justify-center py-3 px-4 border border-emerald-800 text-sm font-semibold rounded-xl text-emerald-800 bg-white hover:bg-neutral-50 transition duration-200">
                        <i class="fa-solid fa-rotate-right mr-2 mt-1"></i> Coba Lagi
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
