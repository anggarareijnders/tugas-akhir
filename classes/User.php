<?php
// classes/User.php

class User {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    // Get all users
    public function getAll() {
        try {
            // PERBAIKAN: Menggunakan SELECT * agar kolom NIB, sertifikat, dan status terbawa semua
            $stmt = $this->db->query("SELECT * FROM users ORDER BY role ASC, created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in User::getAll - " . $e->getMessage());
            return [];
        }
    }

    // Get a specific user
    public function getById($id) {
        try {
            // PERBAIKAN: Menggunakan SELECT * agar data vendor lengkap saat dipanggil spesifik
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in User::getById - " . $e->getMessage());
            return false;
        }
    }

    // Delete user
    public function delete($id, $currentAdminId) {
        try {
            // Prevent self-deletion
            if ($id == $currentAdminId) {
                return "Anda tidak dapat menghapus akun Anda sendiri!";
            }
            
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in User::delete - " . $e->getMessage());
            return "Gagal menghapus user: " . $e->getMessage();
        }
    }

    // Count statistics
    public function getStats() {
        try {
            $total_users = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $total_vendors = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'vendor'")->fetchColumn();
            $total_pendaki = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'pendaki'")->fetchColumn();
            
            return [
                'total_users' => $total_users,
                'total_vendors' => $total_vendors,
                'total_pendaki' => $total_pendaki
            ];
        } catch (PDOException $e) {
            return [
                'total_users' => 0,
                'total_vendors' => 0,
                'total_pendaki' => 0
            ];
        }
    }

    // Update user profile
    public function updateProfile($id, $fullname, $password = null) {
        try {
            if ($password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("UPDATE users SET fullname = ?, password = ? WHERE id = ?");
                return $stmt->execute([$fullname, $hashedPassword, $id]);
            } else {
                $stmt = $this->db->prepare("UPDATE users SET fullname = ? WHERE id = ?");
                return $stmt->execute([$fullname, $id]);
            }
        } catch (PDOException $e) {
            error_log("Error in User::updateProfile - " . $e->getMessage());
            return false;
        }
    }
}
?>