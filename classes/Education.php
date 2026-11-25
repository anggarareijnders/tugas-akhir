<?php
// classes/Education.php

class Education {
    private $db;
    
    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    // Get all logistics guides
    public function getAll($search = '', $vendorId = null) {
        try {
            $query = "SELECT e.*, u.fullname as vendor_name, t.nama_paket 
                      FROM edukasi e 
                      JOIN users u ON e.vendor_id = u.id
                      LEFT JOIN paket_trip t ON e.paket_id = t.id";
            
            $conditions = [];
            $params = [];

            if ($vendorId !== null) {
                $conditions[] = "e.vendor_id = ?";
                $params[] = $vendorId;
            }

            if (!empty($search)) {
                $conditions[] = "(e.gunung LIKE ? OR e.judul LIKE ? OR e.konten LIKE ?)";
                $searchParam = "%" . $search . "%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }

            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }

            $query .= " ORDER BY e.created_at DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in Education::getAll - " . $e->getMessage());
            return [];
        }
    }

    // Get logistics guide by mountain name
    public function getByMountain($mountain) {
        try {
            $stmt = $this->db->prepare("SELECT e.*, u.fullname as vendor_name, t.nama_paket 
                                        FROM edukasi e 
                                        JOIN users u ON e.vendor_id = u.id 
                                        LEFT JOIN paket_trip t ON e.paket_id = t.id
                                        WHERE LOWER(e.gunung) = LOWER(?)");
            $stmt->execute([$mountain]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in Education::getByMountain - " . $e->getMessage());
            return [];
        }
    }

    // Get logistics guide by ID
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT e.*, u.fullname as vendor_name, t.nama_paket 
                                        FROM edukasi e 
                                        JOIN users u ON e.vendor_id = u.id 
                                        LEFT JOIN paket_trip t ON e.paket_id = t.id
                                        WHERE e.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in Education::getById - " . $e->getMessage());
            return false;
        }
    }

    // Create logistics guide
    public function create($vendorId, $paketId, $gunung, $judul, $konten, $logistik) {
        try {
            $stmt = $this->db->prepare("INSERT INTO edukasi (vendor_id, paket_id, gunung, judul, konten, logistik) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$vendorId, $paketId ? $paketId : null, $gunung, $judul, $konten, $logistik]);
        } catch (PDOException $e) {
            error_log("Error in Education::create - " . $e->getMessage());
            return false;
        }
    }

    // Update logistics guide
    public function update($id, $vendorId, $paketId, $gunung, $judul, $konten, $logistik) {
        try {
            $stmt = $this->db->prepare("UPDATE edukasi SET 
                paket_id = ?, 
                gunung = ?, 
                judul = ?, 
                konten = ?, 
                logistik = ? 
                WHERE id = ? AND (vendor_id = ? OR ? = 'admin_override')");
            return $stmt->execute([$paketId ? $paketId : null, $gunung, $judul, $konten, $logistik, $id, $vendorId, $vendorId]);
        } catch (PDOException $e) {
            error_log("Error in Education::update - " . $e->getMessage());
            return false;
        }
    }

    // Delete logistics guide
    public function delete($id, $vendorId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM edukasi WHERE id = ? AND (vendor_id = ? OR ? = 'admin_override')");
            return $stmt->execute([$id, $vendorId, $vendorId]);
        } catch (PDOException $e) {
            error_log("Error in Education::delete - " . $e->getMessage());
            return false;
        }
    }
}
?>
