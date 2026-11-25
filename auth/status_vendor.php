<?php
// auth/status_vendor.php
require_once '../config/database.php';

$db = new Database();
$pdo = $db->getConnection();

$username = isset($_GET['username']) ? trim($_GET['username']) : '';
$user = null;

if (!empty($username)) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'vendor'");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Jika username tidak ditemukan, kembalikan ke login
if (!$user) {
    header("Location: login.php");
    exit();
}

$status = $user['status_akun'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Verifikasi - SPK MOORA Trip Gunung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { forest: { 50: '#f2f8f3', 100: '#e1efe4', 500: '#387c44', 800: '#1e4620', 900: '#163318', }, soil: { 700: '#8c6239', 800: '#5c3f21', } } } } }
    </script>
</head>
<body class="bg-forest-50 min-h-screen flex flex-col justify-center items-center py-12 px-4 relative overflow-x-hidden">
    <!-- Background Blobs -->
    <div class="fixed w-[400px] h-[400px] bg-forest-100 rounded-full blur-3xl -top-40 -left-40 opacity-70 pointer-events-none"></div>
    <div class="fixed w-[500px] h-[500px] bg-emerald-50 rounded-full blur-3xl -bottom-40 -right-40 opacity-70 pointer-events-none"></div>

    <div class="max-w-md w-full z-10 bg-white/90 backdrop-blur-md p-8 sm:p-10 rounded-[2.5rem] shadow-xl border border-white/50 text-center space-y-6">
        
        <?php if ($status === 'pending'): ?>
            <!-- STATUS: PENDING -->
            <div class="h-20 w-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner animate-pulse">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="space-y-2">
                <span class="px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-800 border border-amber-200">
                    Menunggu Verifikasi
                </span>
                <h2 class="text-2xl font-black text-neutral-800 pt-2">Registrasi Berhasil</h2>
                <p class="text-neutral-600 text-sm leading-relaxed font-medium">
                    Akun Anda sedang menunggu diverifikasi oleh Admin. Silakan refresh (muat ulang) halaman ini secara berkala.
                </p>
            </div>
            <div class="pt-4 border-t border-neutral-100 flex flex-col gap-3">
                <button onclick="window.location.reload()" class="w-full py-3.5 px-4 rounded-xl text-neutral-700 bg-neutral-100 hover:bg-neutral-200 font-bold text-xs transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-arrows-rotate"></i> Refresh Halaman
                </button>
            </div>

        <?php elseif ($status === 'approved'): ?>
            <!-- STATUS: DITERIMA / ACC -->
            <div class="h-20 w-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="space-y-2">
                <span class="px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Akun Terverifikasi
                </span>
                <h2 class="text-2xl font-black text-neutral-800 pt-2">Selamat! Akun Aktif</h2>
                <p class="text-neutral-600 text-sm leading-relaxed font-medium">
                    Akun Anda berhasil diverifikasi oleh admin, Anda bisa masuk sekarang.
                </p>
            </div>
            <div class="pt-4 border-t border-neutral-100">
                <a href="login.php" class="w-full py-3.5 px-4 rounded-xl text-white bg-forest-800 hover:bg-forest-900 shadow-lg shadow-forest-800/20 active:scale-95 transition-all font-bold text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Ke Form Login
                </a>
            </div>

        <?php elseif ($status === 'rejected'): ?>
            <!-- STATUS: DITOLAK -->
            <div class="h-20 w-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="space-y-2">
                <span class="px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-800 border border-red-200">
                    Pendaftaran Ditolak
                </span>
                <h2 class="text-2xl font-black text-neutral-800 pt-2">Permohonan Ditolak</h2>
                <p class="text-neutral-600 text-sm leading-relaxed font-medium">
                    Maaf pendaftaran akun ditolak, silahkan cek kembali NIB/Sertifikasi nya agar admin dapat memverifikasi akun Anda.
                </p>
            </div>
            <div class="pt-4 border-t border-neutral-100">
                <a href="login.php" class="w-full py-3.5 px-4 rounded-xl text-white bg-neutral-800 hover:bg-neutral-900 shadow-lg active:scale-95 transition-all font-bold text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Form Login
                </a>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>