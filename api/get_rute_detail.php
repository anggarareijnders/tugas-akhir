<?php
// api/get_rute_detail.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Trip.php';

$paketId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($paketId <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID Paket tidak valid.'
    ]);
    exit();
}

$db = new Database();
$pdo = $db->getConnection();
$tripClass = new Trip($pdo);

$paket = $tripClass->getById($paketId);

if (!$paket) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Paket trip tidak ditemukan.'
    ]);
    exit();
}

$segments = $tripClass->getRuteSegments($paketId);
$contacts = $tripClass->getKontakDarurat($paketId);

echo json_encode([
    'status' => 'success',
    'data' => [
        'id' => $paket['id'],
        'nama_paket' => $paket['nama_paket'],
        'gunung' => $paket['gunung'],
        'kategori' => $paket['kategori'],
        'c3_medan' => $paket['c3_medan'],
        'segments' => $segments,
        'contacts' => $contacts
    ]
]);
exit();
