<?php
// admin/kelola_pengguna.php
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/User.php';

$db = new Database();
$pdo = $db->getConnection();

// Protect page
Auth::requireRole('admin');

$admin = Auth::getUser();
$userClass = new User($pdo);

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

$error = "";
$success = "";

// Tangkap pesan notifikasi dari URL yang bersih
if ($msg === 'approved') {
    $success = "Akun vendor berhasil disetujui dan diverifikasi.";
} elseif ($msg === 'rejected') {
    $success = "Akun vendor berhasil ditolak.";
} elseif ($msg === 'deleted') {
    $success = "Akun pengguna berhasil dihapus dari sistem.";
} elseif ($msg === 'error') {
    $error = "Terjadi kesalahan pada sistem saat memproses data.";
}

// Handle Approve Vendor Action
if ($action === 'approve' && $id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET status_akun = 'approved', pesan_notifikasi = 'akun anda berhasil diverifikasi oleh admin, anda bisa masuk sekarang' WHERE id = ? AND role = 'vendor'");
        $stmt->execute([$id]);
        header("Location: kelola_pengguna.php?msg=approved");
        exit();
    } catch (PDOException $e) {
        header("Location: kelola_pengguna.php?msg=error");
        exit();
    }
}

// Handle Reject Vendor Action
if ($action === 'reject' && $id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET status_akun = 'rejected', pesan_notifikasi = 'maaf pendaftaran akun ditolak, silahkan cek kembali nib/sertifikasi nya agar admin dapat memverifikasi akun anda' WHERE id = ? AND role = 'vendor'");
        $stmt->execute([$id]);
        header("Location: kelola_pengguna.php?msg=rejected");
        exit();
    } catch (PDOException $e) {
        header("Location: kelola_pengguna.php?msg=error");
        exit();
    }
}

// Handle Delete Account Action
if ($action === 'delete' && $id > 0) {
    $deleteResult = $userClass->delete($id, $admin['id']);
    if ($deleteResult === true) {
        header("Location: kelola_pengguna.php?msg=deleted");
        exit();
    } else {
        header("Location: kelola_pengguna.php?msg=error");
        exit();
    }
}

// Fetch all users directly via PDO
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY role ASC, created_at DESC");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $allUsers = [];
    $error = "Gagal mengambil data pengguna: " . $e->getMessage();
}

$current_page = 'admin_pengguna';
$page_title = 'Kelola Pengguna - Panel Admin';
$base_path = "../";

include '../includes/header.php';
?>

<section class="py-12 bg-neutral-50/60 min-h-screen relative z-10 backdrop-blur-[2px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 mb-8 uppercase tracking-wider">
            <a href="dashboard.php" class="hover:text-forest-800 transition">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-emerald-700">Kelola Pengguna & Verifikasi</span>
        </div>

        <div class="bg-white rounded-[2rem] p-8 sm:p-10 border border-neutral-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] space-y-8">
            <div class="border-b border-neutral-100 pb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden">
                <div class="flex items-center gap-4 relative z-10">
                    <div class="h-12 w-12 bg-gradient-to-br from-forest-50 to-emerald-100 text-forest-800 flex items-center justify-center rounded-2xl text-xl font-bold shadow-sm">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-emerald-800">Kelola Akun &amp; Verifikasi Vendor</h1>
                        <p class="text-neutral-500 text-xs mt-1 font-medium">Tinjau NIB, Sertifikasi, dan atur hak akses pengguna di sistem.</p>
                    </div>
                </div>
                <a href="dashboard.php" class="relative z-10 px-5 py-2.5 rounded-xl bg-neutral-100 text-neutral-600 hover:bg-neutral-200 font-bold text-xs transition">
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

            <div class="overflow-x-auto border border-neutral-100 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)]">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-neutral-50/80 border-b border-neutral-100 text-[10px] font-extrabold uppercase tracking-wider text-neutral-500">
                            <th class="py-5 px-6 text-center w-16">No</th>
                            <th class="py-5 px-6">Informasi Pengguna</th>
                            <th class="py-5 px-6">Dokumen Vendor (NIB & Sertifikat)</th>
                            <th class="py-5 px-6 text-center w-36">Status Akun</th>
                            <th class="py-5 px-6 text-center w-40">Aksi Verifikasi & Kelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 font-medium text-neutral-700">
                        <?php 
                        $no = 1;
                        foreach ($allUsers as $row): 
                            $isSelf = ($row['id'] == $admin['id']);
                            $status = $row['status_akun'] ?? 'approved';
                            $nib = $row['nib'] ?? '';
                            $sertifikat_path = $row['sertifikat_path'] ?? '';
                        ?>
                            <tr class="hover:bg-neutral-50/50 transition duration-200 <?php echo $isSelf ? 'bg-emerald-50/20' : ''; ?>">
                                <td class="py-5 px-6 text-center text-neutral-400 font-bold"><?php echo $no++; ?></td>
                                <td class="py-5 px-6">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="block text-neutral-800 font-extrabold text-base"><?php echo htmlspecialchars($row['fullname']); ?></span>
                                            <?php if ($isSelf): ?>
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-800 text-white">Anda</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="text-neutral-500"><i class="fa-regular fa-user mr-1"></i> <?php echo htmlspecialchars($row['username']); ?></span>
                                            <span class="text-neutral-300">•</span>
                                            <span class="inline-flex items-center font-extrabold uppercase tracking-wider <?php 
                                                if ($row['role'] === 'admin') echo 'text-red-600';
                                                elseif ($row['role'] === 'vendor') echo 'text-amber-600';
                                                else echo 'text-emerald-600';
                                            ?>">
                                                <?php echo $row['role']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="py-5 px-6">
                                    <?php if ($row['role'] === 'vendor'): ?>
                                        <div class="space-y-2 text-xs">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-neutral-400">NIB:</span> 
                                                <span class="font-bold text-neutral-700 bg-neutral-100 px-2 py-0.5 rounded-md border border-neutral-200"><?php echo htmlspecialchars($nib ?: 'Tidak Ada'); ?></span>
                                            </div>
                                            <div>
                                                <?php if (!empty($sertifikat_path)): ?>
                                                    <a href="../<?php echo htmlspecialchars($sertifikat_path); ?>" target="_blank" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold text-white bg-soil-700 hover:bg-soil-800 px-3 py-1.5 rounded-lg shadow-sm transition">
                                                        <i class="fa-solid fa-certificate"></i> Lihat Dokumen
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-[10px] italic text-neutral-400">Dokumen tidak diunggah</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-neutral-400 italic">- Bukan Vendor -</span>
                                    <?php endif; ?>
                                </td>

                                <td class="py-5 px-6 text-center">
                                    <?php if ($status === 'pending'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                            <i class="fa-solid fa-clock-rotate-left"></i> Menunggu
                                        </span>
                                    <?php elseif ($status === 'rejected'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                            <i class="fa-solid fa-ban"></i> Ditolak
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fa-solid fa-check-double"></i> Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="py-5 px-6">
                                    <div class="flex justify-center items-center gap-2">
                                        <?php if ($row['role'] === 'vendor' && ($status === 'pending' || $status === 'rejected')): ?>
                                            <a href="kelola_pengguna.php?action=approve&id=<?php echo $row['id']; ?>" 
                                               class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-200 transition flex items-center justify-center shadow-sm" 
                                               onclick="return confirm('Setujui dan aktifkan akun vendor ini?');" 
                                               title="Terima Vendor">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
                                            <?php if ($status !== 'rejected'): ?>
                                            <a href="kelola_pengguna.php?action=reject&id=<?php echo $row['id']; ?>" 
                                               class="h-8 w-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white border border-amber-200 transition flex items-center justify-center shadow-sm" 
                                               onclick="return confirm('Tolak pendaftaran akun vendor ini?');" 
                                               title="Tolak Vendor">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if ($isSelf): ?>
                                            <span class="text-neutral-300 text-[10px] italic font-bold ml-2">Terkunci</span>
                                        <?php else: ?>
                                            <a href="kelola_pengguna.php?action=delete&id=<?php echo $row['id']; ?>" 
                                               class="h-8 w-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-100 transition flex items-center justify-center shadow-sm ml-1" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus akun <?php echo htmlspecialchars($row['username']); ?>?');" 
                                               title="Hapus Akun Permanen">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>