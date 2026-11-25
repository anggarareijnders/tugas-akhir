<?php
// vendor/proses_ubah_status.php
require_once '../config/database.php';
require_once '../classes/Auth.php';

$db = new Database();
$pdo = $db->getConnection();

// Pastikan hanya vendor yang bisa mengakses file ini
Auth::requireRole('vendor');
$vendor = Auth::getUser();
$vendorId = $vendor['id'];

// Tangkap ID paket dan status yang diinginkan dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'Tersedia';

if ($id > 0) {
    // Validasi keamanan agar status hanya bisa bernilai 'Tersedia' atau 'Penuh'
    $status_valid = ($status === 'Penuh') ? 'Penuh' : 'Tersedia';
    
    // Update status di database (pastikan id paket tersebut memang milik vendor yang sedang login)
    $stmt = $pdo->prepare("UPDATE paket_trip SET status_kuota = ? WHERE id = ? AND vendor_id = ?");
    if ($stmt->execute([$status_valid, $id, $vendorId])) {
        // Jika berhasil, kembalikan ke halaman dashboard dengan pesan sukses
        header("Location: dashboard.php?status=updated");
        exit();
    }
}

// Jika gagal, kembalikan dengan pesan error
header("Location: dashboard.php?error=gagal_update");
exit();
?>