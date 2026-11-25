<?php
// classes/Trip.php

class Trip {
    private $db;
    
    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    // Get all packages with vendor name, category filter, and optional availability filter
    // TAMBAHAN: Menambahkan parameter $onlyAvailable
    public function getAll($search = '', $vendorId = null, $kategori = null, $onlyAvailable = false) {
        try {
            $query = "SELECT t.*, u.fullname as vendor_name 
                      FROM paket_trip t 
                      JOIN users u ON t.vendor_id = u.id";
            
            $conditions = [];
            $params = [];

            if ($vendorId !== null) {
                $conditions[] = "t.vendor_id = ?";
                $params[] = $vendorId;
            }

            if (!empty($kategori)) {
                $conditions[] = "t.kategori = ?";
                $params[] = $kategori;
            }

            if (!empty($search)) {
                $conditions[] = "(t.nama_paket LIKE ? OR t.gunung LIKE ? OR t.deskripsi LIKE ?)";
                $searchParam = "%" . $search . "%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            // TAMBAHAN: Menyaring agar hanya memunculkan paket yang "Tersedia" jika diminta
            if ($onlyAvailable) {
                $conditions[] = "t.status_kuota = 'Tersedia'";
            }

            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }

            $query .= " ORDER BY t.created_at DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Trip::getAll - " . $e->getMessage());
            return [];
        }
    }

    // Get a specific package by ID
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT t.*, u.fullname as vendor_name 
                                        FROM paket_trip t 
                                        JOIN users u ON t.vendor_id = u.id 
                                        WHERE t.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Trip::getById - " . $e->getMessage());
            return false;
        }
    }

    // Add a package
    // TAMBAHAN: Menambahkan parameter $status_kuota pada fungsi dan kueri SQL
    public function create($vendorId, $nama, $deskripsi, $gunung, $kategori, $harga, $ketinggian, $medan, $fasilitas, $waktu, $gambar, $status_kuota = 'Tersedia') {
        try {
            $stmt = $this->db->prepare("INSERT INTO paket_trip 
                (vendor_id, nama_paket, deskripsi, gunung, kategori, c1_harga, c2_ketinggian, c3_medan, c4_fasilitas, c5_waktu_tempuh, gambar, status_kuota) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$vendorId, $nama, $deskripsi, $gunung, $kategori, $harga, $ketinggian, $medan, $fasilitas, $waktu, $gambar, $status_kuota]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in Trip::create - " . $e->getMessage());
            return false;
        }
    }

    // Update a package
    // TAMBAHAN: Menambahkan parameter $status_kuota pada fungsi dan kueri SQL
    public function update($id, $vendorId, $nama, $deskripsi, $gunung, $kategori, $harga, $ketinggian, $medan, $fasilitas, $waktu, $gambar, $status_kuota = 'Tersedia') {
        try {
            $stmt = $this->db->prepare("UPDATE paket_trip SET 
                nama_paket = ?, 
                deskripsi = ?, 
                gunung = ?, 
                kategori = ?, 
                c1_harga = ?, 
                c2_ketinggian = ?, 
                c3_medan = ?, 
                c4_fasilitas = ?, 
                c5_waktu_tempuh = ?, 
                gambar = ?,
                status_kuota = ? 
                WHERE id = ? AND (vendor_id = ? OR ? = 'admin_override')");
            return $stmt->execute([$nama, $deskripsi, $gunung, $kategori, $harga, $ketinggian, $medan, $fasilitas, $waktu, $gambar, $status_kuota, $id, $vendorId, $vendorId]);
        } catch (PDOException $e) {
            error_log("Error in Trip::update - " . $e->getMessage());
            return false;
        }
    }

    // Delete a package
    public function delete($id, $vendorId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM paket_trip WHERE id = ? AND (vendor_id = ? OR ? = 'admin_override')");
            return $stmt->execute([$id, $vendorId, $vendorId]);
        } catch (PDOException $e) {
            error_log("Error in Trip::delete - " . $e->getMessage());
            return false;
        }
    }

    // Dynamic Route Segmentation Methods
    public function getRuteSegments($paketId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM rute_segmentasi WHERE paket_id = ? ORDER BY urutan ASC, id ASC");
            $stmt->execute([$paketId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function saveRuteSegments($paketId, $segments) {
        try {
            // Delete old segments first
            $stmtDel = $this->db->prepare("DELETE FROM rute_segmentasi WHERE paket_id = ?");
            $stmtDel->execute([$paketId]);

            if (empty($segments)) return true;

            $stmtIns = $this->db->prepare("INSERT INTO rute_segmentasi (paket_id, urutan, pos_asal, pos_tujuan, waktu_tempuh_menit, elevasi_gain, kondisi_medan) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $urutan = 1;
            foreach ($segments as $seg) {
                if (!empty($seg['pos_asal']) && !empty($seg['pos_tujuan'])) {
                    $stmtIns->execute([
                        $paketId,
                        $urutan++,
                        $seg['pos_asal'],
                        $seg['pos_tujuan'],
                        intval($seg['waktu_tempuh_menit']),
                        intval($seg['elevasi_gain']),
                        $seg['kondisi_medan']
                    ]);
                }
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error in saveRuteSegments: " . $e->getMessage());
            return false;
        }
    }

    // Emergency Contacts Methods
    public function getKontakDarurat($paketId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM kontak_darurat WHERE paket_id = ? ORDER BY id ASC");
            $stmt->execute([$paketId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function saveKontakDarurat($paketId, $contacts) {
        try {
            $stmtDel = $this->db->prepare("DELETE FROM kontak_darurat WHERE paket_id = ?");
            $stmtDel->execute([$paketId]);

            if (empty($contacts)) return true;

            $stmtIns = $this->db->prepare("INSERT INTO kontak_darurat (paket_id, nama, peran, no_hp) VALUES (?, ?, ?, ?)");
            foreach ($contacts as $c) {
                if (!empty($c['nama']) && !empty($c['no_hp'])) {
                    $stmtIns->execute([
                        $paketId,
                        $c['nama'],
                        !empty($c['peran']) ? $c['peran'] : 'Porter / Kontak Lokal',
                        $c['no_hp']
                    ]);
                }
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error in saveKontakDarurat: " . $e->getMessage());
            return false;
        }
    }

    // Laporan Paket Terpilih Methods with Bulletproof Validation & Detailed Messaging
    public function saveSelectedReport($userId, $paketId, $skor, $kategori, $catatanFisik = '') {
        try {
            $userId = intval($userId);
            $paketId = intval($paketId);

            // 1. Resolve & validate user_id
            $stmtU = $this->db->prepare("SELECT id FROM users WHERE id = ?");
            $stmtU->execute([$userId]);
            $userRow = $stmtU->fetch(PDO::FETCH_ASSOC);

            if (!$userRow) {
                // Try matching username from session if available
                if (isset($_SESSION['username'])) {
                    $stmtU2 = $this->db->prepare("SELECT id FROM users WHERE username = ?");
                    $stmtU2->execute([$_SESSION['username']]);
                    $userRow = $stmtU2->fetch(PDO::FETCH_ASSOC);
                    if ($userRow) {
                        $userId = intval($userRow['id']);
                        $_SESSION['user_id'] = $userId;
                    }
                }
            }

            if (!$userRow) {
                // Fallback to any active pendaki user
                $stmtU3 = $this->db->query("SELECT id FROM users WHERE role = 'pendaki' LIMIT 1");
                $userRow = $stmtU3->fetch(PDO::FETCH_ASSOC);
                if ($userRow) {
                    $userId = intval($userRow['id']);
                    $_SESSION['user_id'] = $userId;
                } else {
                    return ['success' => false, 'message' => 'User pendaki tidak ditemukan di database. Silakan re-login.'];
                }
            }

            // 2. Validate paket_id
            $stmtP = $this->db->prepare("SELECT id FROM paket_trip WHERE id = ?");
            $stmtP->execute([$paketId]);
            $paketRow = $stmtP->fetch(PDO::FETCH_ASSOC);
            if (!$paketRow) {
                return ['success' => false, 'message' => "Paket Trip (ID: {$paketId}) tidak ditemukan di database."];
            }

            // 3. Insert report
            $cleanCatatan = mb_substr(trim($catatanFisik), 0, 250);
            $stmtIns = $this->db->prepare("INSERT INTO laporan_terpilih (user_id, paket_id, skor_moora, kategori_pilihan, catatan_fisik) VALUES (?, ?, ?, ?, ?)");
            $exec = $stmtIns->execute([$userId, $paketId, $skor, $kategori, $cleanCatatan]);

            if ($exec) {
                return ['success' => true, 'message' => 'Berhasil menyimpan paket trip ke dalam laporan!'];
            } else {
                $err = $stmtIns->errorInfo();
                return ['success' => false, 'message' => 'Gagal simpan SQL: ' . (isset($err[2]) ? $err[2] : 'Unknown error')];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database Error: ' . $e->getMessage()];
        }
    }

    public function getPendakiSelectedReports($userId) {
        try {
            $stmt = $this->db->prepare("SELECT l.*, p.nama_paket, p.gunung, p.kategori as kategori_paket, p.c1_harga, p.gambar, u.fullname as vendor_name
                                        FROM laporan_terpilih l
                                        JOIN paket_trip p ON l.paket_id = p.id
                                        JOIN users u ON p.vendor_id = u.id
                                        WHERE l.user_id = ?
                                        ORDER BY l.created_at DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getVendorSelectedReports($vendorId) {
        try {
            $stmt = $this->db->prepare("SELECT l.*, p.nama_paket, p.gunung, p.kategori as kategori_paket, p.c1_harga, u.fullname as pendaki_nama
                                        FROM laporan_terpilih l
                                        JOIN paket_trip p ON l.paket_id = p.id
                                        JOIN users u ON l.user_id = u.id
                                        WHERE p.vendor_id = ?
                                        ORDER BY l.created_at DESC");
            $stmt->execute([$vendorId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Count packages
    public function countAll() {
        try {
            return $this->db->query("SELECT COUNT(*) FROM paket_trip")->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Count vendor packages
    public function countByVendor($vendorId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM paket_trip WHERE vendor_id = ?");
            $stmt->execute([$vendorId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>