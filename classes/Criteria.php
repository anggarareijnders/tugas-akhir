<?php
// classes/Criteria.php

class Criteria {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    // Get all criteria
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM kriteria ORDER BY id ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in Criteria::getAll - " . $e->getMessage());
            return [];
        }
    }

    // Get criteria details by code
    public function getByKode($kode) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM kriteria WHERE kode = ?");
            $stmt->execute([$kode]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in Criteria::getByKode - " . $e->getMessage());
            return false;
        }
    }

    // Update weights & jenis
    public function update($kode, $nama, $jenis, $bobot) {
        try {
            $stmt = $this->db->prepare("UPDATE kriteria SET nama = ?, jenis = ?, bobot = ? WHERE kode = ?");
            return $stmt->execute([$nama, $jenis, $bobot, $kode]);
        } catch (PDOException $e) {
            error_log("Error in Criteria::update - " . $e->getMessage());
            return false;
        }
    }

    // Recalculate weights dynamically so they always sum up to 1.0 (optional utility)
    public function normalizeWeights() {
        try {
            $stmt = $this->db->query("SELECT SUM(bobot) FROM kriteria");
            $sum = floatval($stmt->fetchColumn());

            if ($sum > 0 && abs($sum - 1.0) > 0.001) {
                // Fetch all and adjust
                $criteria = $this->getAll();
                $updateStmt = $this->db->prepare("UPDATE kriteria SET bobot = ? WHERE id = ?");
                foreach ($criteria as $c) {
                    $newBobot = round($c['bobot'] / $sum, 4);
                    $updateStmt->execute([$newBobot, $c['id']]);
                }
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
