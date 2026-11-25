<?php
// auth/register.php
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
// Variabel $success dihapus karena langsung di-redirect ke halaman login

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = trim($_POST['role']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($role === 'vendor') {
        $nama_agensi = trim($_POST['nama_agensi'] ?? '');
        $nama_pemilik = trim($_POST['nama_pemilik'] ?? '');
        $nomor_kontak = trim($_POST['nomor_kontak'] ?? '');
        $nib = trim($_POST['nib'] ?? '');
        
        $fullname = $nama_agensi;

        if (empty($nama_agensi) || empty($nama_pemilik) || empty($nib) || empty($username) || empty($password)) {
            $error = "Semua kolom utama vendor wajib diisi!";
        } else {
            $uploadDir = '../uploads/dokumen_vendor/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $sertifikat_path = '';
            if (isset($_FILES['sertifikat_file']) && $_FILES['sertifikat_file']['error'] === UPLOAD_ERR_OK) {
                $ext1 = strtolower(pathinfo($_FILES['sertifikat_file']['name'], PATHINFO_EXTENSION));
                $sertifikat_path = 'uploads/dokumen_vendor/sertifikat_' . time() . '_' . uniqid() . '.' . $ext1;
                move_uploaded_file($_FILES['sertifikat_file']['tmp_name'], '../' . $sertifikat_path);
            }

            // Buat akun dasar
            $registerResult = $auth->register($username, $password, $fullname, $role);
            
            if ($registerResult === true) {
                // Update data spesifik vendor dan atur status_akun ke 'pending'
                $stmt = $pdo->prepare("UPDATE users SET nama_pemilik = ?, nomor_kontak = ?, nib = ?, sertifikat_path = ?, status_akun = 'pending' WHERE username = ?");
                $stmt->execute([$nama_pemilik, $nomor_kontak, $nib, $sertifikat_path, $username]);

                // Redirect langsung ke halaman status pending vendor
                header("Location: status_vendor.php?username=" . urlencode($username));
                exit();
            } else {
                $error = $registerResult;
            }
        }
    } else {
        $fullname = trim($_POST['fullname'] ?? '');
        if (empty($fullname) || empty($username) || empty($password)) {
            $error = "Semua kolom wajib diisi!";
        } else {
            $registerResult = $auth->register($username, $password, $fullname, $role);
            if ($registerResult === true) {
                // Redirect ke halaman login dengan parameter success
                header("Location: login.php?registered=success");
                exit();
            } else {
                $error = $registerResult;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SPK MOORA Trip Gunung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { forest: { 50: '#f2f8f3', 100: '#e1efe4', 500: '#387c44', 800: '#1e4620', 900: '#163318', }, soil: { 700: '#8c6239', 800: '#5c3f21', } } } }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-forest-900 via-emerald-800 to-teal-900 min-h-screen flex flex-col justify-center items-center py-12 px-4 relative overflow-x-hidden">
    <div class="fixed w-[400px] h-[400px] bg-emerald-500 rounded-full blur-3xl -top-40 -left-40 opacity-20"></div>
    <div class="fixed w-[500px] h-[500px] bg-teal-400 rounded-full blur-3xl -bottom-40 -right-40 opacity-15"></div>

    <div class="max-w-md w-full z-10 bg-white/10 backdrop-blur-xl p-8 rounded-[2rem] shadow-2xl shadow-emerald-500/10 border border-white/20 my-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-400 text-white text-3xl shadow-lg shadow-emerald-500/40">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="mt-4 text-3xl font-black text-white">Daftar Akun</h2>
            <p class="text-emerald-200/80 text-sm mt-1 font-medium">Gabung bersama komunitas pendaki & vendor trip gunung</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 bg-red-500/20 border-l-4 border-red-400 text-red-200 text-sm font-semibold rounded-r-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" enctype="multipart/form-data" class="space-y-5">
            <div>
                <label class="block text-[10px] font-extrabold text-emerald-200/80 uppercase tracking-wider mb-2">Daftar Sebagai</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex items-center justify-center p-3 rounded-xl border border-white/20 bg-white/5 cursor-pointer hover:bg-white/10 transition [&:has(input:checked)]:border-emerald-400 [&:has(input:checked)]:bg-emerald-500/20 [&:has(input:checked)]:shadow-sm">
                        <input type="radio" name="role" value="pendaki" id="role_pendaki" checked class="sr-only">
                        <span class="text-sm font-bold text-white/80 flex items-center gap-2">
                            <i class="fa-solid fa-person-hiking text-emerald-400"></i> Pendaki
                        </span>
                    </label>
                    <label class="relative flex items-center justify-center p-3 rounded-xl border border-white/20 bg-white/5 cursor-pointer hover:bg-white/10 transition [&:has(input:checked)]:border-emerald-400 [&:has(input:checked)]:bg-emerald-500/20 [&:has(input:checked)]:shadow-sm">
                        <input type="radio" name="role" value="vendor" id="role_vendor" class="sr-only">
                        <span class="text-sm font-bold text-white/80 flex items-center gap-2">
                            <i class="fa-solid fa-campground text-amber-400"></i> Vendor Trip
                        </span>
                    </label>
                </div>
            </div>

            <div id="dynamic-form-fields" class="space-y-4 pt-2">
                <div id="fields-pendaki" class="space-y-4">
                    <div>
                        <label for="fullname" class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <i class="fa-regular fa-id-card"></i>
                            </span>
                            <input type="text" name="fullname" id="fullname" required 
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 font-semibold text-sm transition" 
                                   placeholder="Masukkan nama lengkap Anda">
                        </div>
                    </div>
                </div>

                <div id="fields-vendor" class="hidden space-y-4">
                    <div>
                        <label for="nama_agensi" class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider mb-2">Nama Agensi Travel *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <i class="fa-solid fa-store"></i>
                            </span>
                            <input type="text" name="nama_agensi" id="nama_agensi" 
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-soil-600 font-semibold text-sm transition" 
                                   placeholder="Contoh: Kuya Journey">
                        </div>
                    </div>
                    <div>
                        <label for="nama_pemilik" class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider mb-2">Nama Pemilik *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <i class="fa-solid fa-user-tie"></i>
                            </span>
                            <input type="text" name="nama_pemilik" id="nama_pemilik" 
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-soil-600 font-semibold text-sm transition" 
                                   placeholder="Nama penanggung jawab">
                        </div>
                    </div>
                    <div>
                        <label for="nomor_kontak" class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider mb-2">Nomor Kontak Whatsapp *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input type="text" name="nomor_kontak" id="nomor_kontak" 
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-soil-600 font-semibold text-sm transition" 
                                   placeholder="0812xxxxxxx">
                        </div>
                    </div>
                    <div>
                        <label for="nib" class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider mb-2">Nomor Induk Berusaha (NIB) *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <i class="fa-regular fa-id-badge"></i>
                            </span>
                            <input type="text" name="nib" id="nib" 
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-soil-600 font-semibold text-sm transition" 
                                   placeholder="Masukkan 13 digit angka NIB">
                        </div>
                    </div>

                    <div class="p-4 bg-orange-50/50 border border-orange-100 rounded-2xl space-y-4">
                        <p class="text-[10px] font-extrabold text-soil-800 uppercase tracking-widest text-center">Dokumen Verifikasi (Gambar Wajib)</p>
                        <div>
                            <label for="sertifikat_file" class="block text-[10px] font-extrabold text-neutral-600 uppercase tracking-wider mb-2">Upload Sertifikasi Biro/APGI *</label>
                            <input type="file" name="sertifikat_file" id="sertifikat_file" accept="image/jpeg, image/png, image/webp" 
                                   class="w-full px-3 py-2 border border-neutral-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-soil-600 text-xs bg-white file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-soil-100 file:text-soil-700 hover:file:bg-soil-200 transition">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="username" class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider mb-2">Username Akses *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                            <i class="fa-regular fa-user"></i>
                        </span>
                        <input type="text" name="username" id="username" required 
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 font-semibold text-sm transition" 
                               placeholder="Pilih username unik (tanpa spasi)">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-extrabold text-neutral-500 uppercase tracking-wider mb-2">Password *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required 
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 font-semibold text-sm transition" 
                               placeholder="Minimal 6 karakter rahasia">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full mt-4 py-3.5 px-4 rounded-xl text-white bg-forest-800 hover:bg-forest-900 shadow-[0_8px_20px_rgba(30,70,32,0.25)] active:scale-95 transition-all font-bold">
                Daftar Akun Sekarang <i class="fa-solid fa-arrow-right ml-1"></i>
            </button>
        </form>

        <div class="mt-8 text-center border-t border-neutral-100 pt-6">
            <p class="text-sm font-semibold text-neutral-500">
                Sudah memiliki akun? <a href="login.php" class="text-forest-800 hover:text-forest-900 font-extrabold hover:underline transition">Masuk di sini</a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const roleRadios = document.querySelectorAll('input[name="role"]');
            const sectionPendaki = document.getElementById('fields-pendaki');
            const sectionVendor = document.getElementById('fields-vendor');
            const inputPendaki = document.getElementById('fullname');
            const inputsVendor = [
                document.getElementById('nama_agensi'), document.getElementById('nama_pemilik'),
                document.getElementById('nomor_kontak'), document.getElementById('nib'), document.getElementById('sertifikat_file')
            ];

            function toggleFields() {
                const selectedRole = document.querySelector('input[name="role"]:checked').value;
                if (selectedRole === 'vendor') {
                    sectionPendaki.classList.add('hidden'); sectionVendor.classList.remove('hidden');
                    inputPendaki.removeAttribute('required');
                    inputsVendor.forEach(input => input.setAttribute('required', 'required'));
                } else {
                    sectionVendor.classList.add('hidden'); sectionPendaki.classList.remove('hidden');
                    inputsVendor.forEach(input => input.removeAttribute('required'));
                    inputPendaki.setAttribute('required', 'required');
                }
            }

            roleRadios.forEach(radio => radio.addEventListener('change', toggleFields));
            toggleFields();
        });
    </script>
</body>
</html>