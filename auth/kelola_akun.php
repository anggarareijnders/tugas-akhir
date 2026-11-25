<?php
// auth/kelola_akun.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/User.php';

$db = new Database();
$pdo = $db->getConnection();

// Require login
Auth::requireLogin();

$user = Auth::getUser();
$userId = $user['id'];
$userClass = new User($pdo);

$error = "";
$success = "";

// Fetch current user details from DB to populate
$userData = $userClass->getById($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($fullname)) {
        $error = "Nama Lengkap wajib diisi.";
    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $error = "Kata sandi baru minimal 6 karakter.";
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error = "Konfirmasi kata sandi baru tidak cocok.";
    } else {
        $updatePass = !empty($new_password) ? $new_password : null;
        if ($userClass->updateProfile($userId, $fullname, $updatePass)) {
            $success = "Profil akun Anda berhasil diperbarui!";
            // Update session data
            $_SESSION['fullname'] = $fullname;
            
            // Re-fetch userData
            $userData = $userClass->getById($userId);
        } else {
            $error = "Gagal memperbarui profil akun.";
        }
    }
}

$current_page = 'kelola_akun';
$page_title = 'Kelola Akun Saya - MOORATrip';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-12 bg-neutral-50 flex-grow">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="../index.php" class="hover:text-forest-800 transition">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Kelola Akun</span>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-xl space-y-8">
            <div class="border-b border-neutral-100 pb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 bg-forest-50 text-forest-800 flex items-center justify-center rounded-xl text-lg font-bold">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-neutral-800">Kelola Akun</h1>
                </div>
                <p class="text-neutral-500 text-sm leading-relaxed">
                    Perbarui data nama lengkap atau ubah kata sandi akun Anda di bawah ini.
                </p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded-r-xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <form action="kelola_akun.php" method="POST" class="space-y-6">
                <!-- Username (Readonly) -->
                <div>
                    <label class="block text-xs font-semibold text-neutral-450 uppercase tracking-wider mb-2">Username (Tidak Dapat Diubah)</label>
                    <input type="text" value="<?php echo htmlspecialchars($userData['username']); ?>" readonly 
                           class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-neutral-50 text-neutral-500 text-sm font-semibold focus:outline-none cursor-not-allowed">
                </div>

                <!-- Hak Akses / Role (Readonly) -->
                <div>
                    <label class="block text-xs font-semibold text-neutral-450 uppercase tracking-wider mb-2">Hak Akses / Role</label>
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-forest-50 text-forest-800 border border-forest-150">
                        <?php echo htmlspecialchars($userData['role']); ?>
                    </span>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="fullname" class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="fullname" id="fullname" required 
                           value="<?php echo htmlspecialchars($userData['fullname']); ?>"
                           placeholder="Contoh: Giri Wira"
                           class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm">
                </div>

                <div class="border-t border-neutral-100 pt-6 space-y-4">
                    <h3 class="text-sm font-bold text-neutral-850"><i class="fa-solid fa-key text-neutral-450 mr-1.5"></i> Ganti Kata Sandi (Opsional)</h3>
                    <p class="text-xs text-neutral-400">Kosongkan kolom sandi di bawah jika Anda tidak berniat mengubah kata sandi Anda saat ini.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="new_password" class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                            <input type="password" name="new_password" id="new_password" 
                                   placeholder="Minimal 6 karakter"
                                   class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-xs font-semibold text-neutral-600 uppercase tracking-wider mb-2">Konfirmasi Sandi Baru</label>
                            <input type="password" name="confirm_password" id="confirm_password" 
                                   placeholder="Ulangi kata sandi baru"
                                   class="w-full px-4 py-3 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:ring-2 focus:ring-forest-500 text-sm">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-neutral-100 flex justify-between items-center">
                    <a href="<?php 
                        if ($userData['role'] === 'admin') echo '../admin/dashboard.php';
                        elseif ($userData['role'] === 'vendor') echo '../vendor/dashboard.php';
                        else echo '../index.php';
                    ?>" class="px-5 py-2.5 border rounded-xl text-neutral-600 hover:bg-neutral-50 font-bold text-xs transition">
                        Kembali
                    </a>
                    
                    <button type="submit" class="px-6 py-3 rounded-xl text-white bg-forest-800 hover:bg-forest-900 font-bold text-xs transition shadow active:scale-95">
                        Perbarui Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
