<?php
// admin/profil_admin.php
require_once '../config/database.php';
require_once '../classes/Auth.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('admin');

$admin = Auth::getUser();

$error = "";
$success = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password']; // opsional

    if (empty($fullname) || empty($username)) {
        $error = "Nama Lengkap dan Username tidak boleh kosong.";
    } else {
        try {
            if (!empty($password)) {
                // Update dengan password baru
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET fullname = ?, username = ?, password = ? WHERE id = ?");
                $stmt->execute([$fullname, $username, $hashed_password, $admin['id']]);
            } else {
                // Update tanpa ganti password
                $stmt = $pdo->prepare("UPDATE users SET fullname = ?, username = ? WHERE id = ?");
                $stmt->execute([$fullname, $username, $admin['id']]);
            }
            
            // Perbarui data sesi admin (agar perubahan langsung terlihat di header)
            $_SESSION['user']['fullname'] = $fullname;
            $_SESSION['user']['username'] = $username;
            $admin['fullname'] = $fullname;
            $admin['username'] = $username;

            $success = "Profil berhasil diperbarui!";
        } catch (PDOException $e) {
            $error = "Gagal memperbarui profil. Username mungkin sudah digunakan oleh akun lain.";
        }
    }
}

$current_page = 'admin_profil';
$page_title = 'Profil Administrator - Panel Admin';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-12 bg-neutral-50 flex-grow">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-6 uppercase tracking-wider">
            <a href="dashboard.php" class="hover:text-emerald-800 transition">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-neutral-600">Profil Saya</span>
        </div>

        <div class="bg-white rounded-3xl border border-neutral-100 shadow-xl overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-neutral-100 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-extrabold text-neutral-800 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-emerald-700"></i> Pengaturan Profil
                    </h1>
                    <p class="text-neutral-500 text-xs mt-1">Perbarui informasi identitas dan keamanan akun Anda.</p>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="m-6 mb-0 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="m-6 mb-0 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded-r-xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <div class="p-6 sm:p-8">
                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label for="fullname" class="block text-sm font-semibold text-neutral-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($admin['fullname']); ?>" required
                            class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-neutral-700">
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-semibold text-neutral-700 mb-1">Username</label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required
                            class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-neutral-700 bg-neutral-50">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-neutral-700 mb-1">Password Baru (Opsional)</label>
                        <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengubah sandi"
                            class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-neutral-700">
                        <p class="text-[10px] text-neutral-400 mt-1 mt-2"><i class="fa-solid fa-info-circle"></i> Jika diisi, password lama Anda akan langsung diganti dengan yang baru.</p>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-emerald-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>