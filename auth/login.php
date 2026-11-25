<?php
// auth/login.php
require_once '../config/database.php';
require_once '../classes/Auth.php';

$db = new Database();
$pdo = $db->getConnection();
$auth = new Auth($pdo);

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    $role = Auth::getRole();
    if ($role === 'admin') {
        header("Location: ../admin/dashboard.php");
    } elseif ($role === 'vendor') {
        header("Location: ../vendor/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Semua kolom wajib diisi!";
    } else {
        // Cek status akun vendor sebelum membiarkan login
        $stmt = $pdo->prepare("SELECT role, status_akun FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $userCheck = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userCheck && $userCheck['role'] === 'vendor') {
            if ($userCheck['status_akun'] === 'pending' || $userCheck['status_akun'] === 'rejected') {
                // Arahkan ke halaman status khusus vendor jika belum disetujui / ditolak
                header("Location: status_vendor.php?username=" . urlencode($username));
                exit();
            }
        }

        // Jalankan login biasa untuk Admin, Pendaki, dan Vendor yang sudah Approved
        $loginResult = $auth->login($username, $password);
        if ($loginResult === true) {
            $role = Auth::getRole();
            if ($role === 'admin') {
                header("Location: ../admin/dashboard.php");
            } elseif ($role === 'vendor') {
                header("Location: ../vendor/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $error = $loginResult;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK MOORA Trip Gunung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { forest: { 50: '#f2f8f3', 100: '#e1efe4', 500: '#387c44', 800: '#1e4620', 900: '#163318', }, soil: { 700: '#8c6239', 800: '#5c3f21', } } } }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-forest-900 via-emerald-800 to-teal-900 min-h-screen flex flex-col justify-center items-center py-12 px-4 relative overflow-x-hidden bg-[radial-gradient(ellipse_at_center,_rgba(225,239,228,0.5)_0%,transparent_70%)]">
    <div class="fixed w-[400px] h-[400px] bg-emerald-500 rounded-full blur-3xl -top-40 -left-40 opacity-20 animate-[pulse_4s_ease-in-out_infinite] pointer-events-none"></div>
    <div class="fixed w-[500px] h-[500px] bg-teal-400 rounded-full blur-3xl -bottom-40 -right-40 opacity-15 animate-[pulse_5s_ease-in-out_infinite] pointer-events-none"></div>

    <div class="max-w-md w-full z-10 bg-white/10 backdrop-blur-xl p-8 rounded-[2rem] shadow-2xl shadow-emerald-500/10 border border-white/20">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-400 text-white text-3xl shadow-lg shadow-emerald-500/40">
                <i class="fa-solid fa-compass"></i>
            </div>
            <h2 class="mt-4 text-3xl font-black text-white tracking-tight">Selamat Datang</h2>
            <p class="text-emerald-200/80 text-sm mt-1 font-medium">Masuk untuk memulai petualangan baru Anda</p>
        </div>

        <!-- NOTIFIKASI REGISTRASI BERHASIL -->
        <?php if (isset($_GET['registered']) && $_GET['registered'] === 'success'): ?>
            <div class="mb-6 p-4 bg-emerald-500/20 border-l-4 border-emerald-400 text-emerald-200 text-sm font-semibold rounded-r-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg"></i>
                <span>Pendaftaran berhasil! Silakan masuk.</span>
            </div>
        <?php endif; ?>

        <!-- NOTIFIKASI ERROR (Login Gagal/Form Kosong) -->
        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 bg-red-500/20 border-l-4 border-red-400 text-red-200 text-sm font-semibold rounded-r-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-[10px] font-extrabold text-emerald-200/80 uppercase tracking-wider mb-2">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-300/60">
                        <i class="fa-regular fa-user"></i>
                    </span>
                    <input type="text" name="username" id="username" required 
                           class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-white/20 bg-white/10 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 font-semibold text-sm transition" 
                           placeholder="Masukkan username Anda">
                </div>
            </div>

            <div>
                <label for="password" class="block text-[10px] font-extrabold text-emerald-200/80 uppercase tracking-wider mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-300/60">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" required 
                           class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-white/20 bg-white/10 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 font-semibold text-sm transition" 
                           placeholder="Masukkan password Anda">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-2xl text-white bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 shadow-lg shadow-emerald-500/40 hover:shadow-emerald-500/60 hover:-translate-y-0.5 active:scale-95 transition-all font-bold mt-2">
                Masuk <i class="fa-solid fa-arrow-right-to-bracket ml-2"></i>
            </button>
        </form>

        <div class="mt-10 text-center border-t border-white/10 pt-8 pb-2">
            <p class="text-sm font-semibold text-emerald-200/60">
                Belum punya akun? 
                <a href="register.php" class="text-emerald-400 hover:text-emerald-300 font-extrabold hover:underline transition">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </div>
</body>
</html>