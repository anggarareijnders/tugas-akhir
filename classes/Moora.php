<?php
// classes/Moora.php

class Moora {
    private $db;
    
    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    /**
     * Get criteria list from database
     */
    public function getCriteria() {
        try {
            $stmt = $this->db->query("SELECT * FROM kriteria ORDER BY kode ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                ['kode' => 'C1', 'nama' => 'Harga Paket', 'jenis' => 'cost', 'bobot' => 0.18],
                ['kode' => 'C2', 'nama' => 'Ketinggian Gunung', 'jenis' => 'benefit', 'bobot' => 0.10],
                ['kode' => 'C3', 'nama' => 'Kesulitan Medan', 'jenis' => 'cost', 'bobot' => 0.22],
                ['kode' => 'C4', 'nama' => 'Kelengkapan Fasilitas', 'jenis' => 'benefit', 'bobot' => 0.20],
                ['kode' => 'C5', 'nama' => 'Estimasi Waktu Tempuh', 'jenis' => 'cost', 'bobot' => 0.15],
                ['kode' => 'C6', 'nama' => 'Kondisi Ideal Tubuh', 'jenis' => 'cost', 'bobot' => 0.15],
            ];
        }
    }

    /**
     * Helper to convert values to MOORA matrix values (Skala Ordinal 1-3)
     * DISESUAIKAN DENGAN TAMBAHAN KRITERIA C6 (IDEAL TUBUH)
     */
    private function convertToScale($cKode, $val) {
        switch ($cKode) {
            case 'C1': // Harga Paket
                $harga = floatval($val);
                if ($harga <= 500000) return 1.0;          // Sangat Murah
                if ($harga > 500000 && $harga <= 750000) return 2.0; // Standar
                return 3.0;                                // Mahal

            case 'C2': // Ketinggian Gunung (Mdpl)
                $ketinggian = floatval($val);
                if ($ketinggian < 2000) return 1.0;         // Rendah
                if ($ketinggian >= 2000 && $ketinggian <= 3000) return 2.0; // Sedang
                return 3.0;                                // Tinggi

            case 'C3': // Kesulitan Medan
                if (is_numeric($val)) {
                    $medan = intval($val);
                    if ($medan <= 1) return 1.0;            // Mudah
                    if ($medan == 2) return 2.0;            // Sedang
                    return 3.0;                             // Sulit
                } else {
                    $medanText = strtolower(trim($val));
                    if (strpos($medanText, 'landai') !== false || strpos($medanText, 'mudah') !== false) return 1.0;
                    if (strpos($medanText, 'bervariasi') !== false || strpos($medanText, 'sedang') !== false) return 2.0;
                    return 3.0;
                }

            case 'C4': // Kelengkapan Fasilitas (Jumlah item)
                $jmlFasilitas = intval($val);
                if ($jmlFasilitas <= 4) return 1.0;         // Kurang Lengkap
                if ($jmlFasilitas >= 5 && $jmlFasilitas <= 8) return 2.0; // Cukup Lengkap
                return 3.0;                                // Sangat Lengkap

            case 'C5': // Waktu Tempuh (Jam)
                $waktu = floatval($val);
                if ($waktu <= 5) return 1.0;                // Singkat
                if ($waktu > 5 && $waktu <= 10) return 2.0; // Sedang
                return 3.0;                                // Lama

            case 'C6': // Kondisi Ideal Tubuh (Kurus, Normal, Obesitas)
                if (is_numeric($val)) {
                    $c6 = intval($val);
                    if ($c6 <= 1) return 1.0; // Kurus
                    if ($c6 == 2) return 2.0; // Normal
                    return 3.0;               // Obesitas
                } else {
                    $tubuhText = strtolower(trim($val));
                    if (strpos($tubuhText, 'kurus') !== false) return 1.0;
                    if (strpos($tubuhText, 'normal') !== false) return 2.0;
                    return 3.0; // Obesitas / Lainnya
                }

            default:
                return floatval($val);
        }
    }

    /**
     * Run MOORA algorithm on provided trip packages and criteria.
     */
    public function calculate($alternatives, $customWeights = null, $prefFacilities = null, $physicalData = null) {
        
        // --- TAMBAHAN LAPISAN KEAMANAN ---
        // Saring dan buang paket yang statusnya "Penuh" sebelum diproses algoritma
        $availableAlternatives = [];
        foreach ($alternatives as $alt) {
            if (!isset($alt['status_kuota']) || $alt['status_kuota'] !== 'Penuh') {
                $availableAlternatives[] = $alt;
            }
        }
        $alternatives = $availableAlternatives; 
        // ---------------------------------

        // 1. Get Criteria
        $criteria = $this->getCriteria();
        $criteriaMap = [];
        foreach ($criteria as $c) {
            $criteriaMap[$c['kode']] = $c;
        }

        // Normalize custom weights if provided
        if ($customWeights && is_array($customWeights)) {
            $sumCustom = array_sum($customWeights);
            foreach ($criteria as &$c) {
                if (isset($customWeights[$c['kode']])) {
                    $c['bobot'] = floatval($customWeights[$c['kode']]) / ($sumCustom > 0 ? $sumCustom : 1);
                    $criteriaMap[$c['kode']]['bobot'] = $c['bobot'];
                }
            }
        }

        // Check Physical Risk condition (Usia > 50 or BMI >= 30)
        $physicalRisk = false;
        $bmi = 0;
        $c6ValueInput = 2.0; // Default Normal
        $physicalNotes = "";
        
        if ($physicalData) {
            $usia = isset($physicalData['usia']) ? intval($physicalData['usia']) : 0;
            $berat = isset($physicalData['berat']) ? floatval($physicalData['berat']) : 0;
            $tinggi = isset($physicalData['tinggi']) ? floatval($physicalData['tinggi']) : 165;
            
            $heightM = ($tinggi > 0) ? ($tinggi / 100) : 1.65;
            $bmi = ($heightM > 0) ? round($berat / ($heightM * $heightM), 1) : 0;

            // Penentuan Skala C6 Berdasarkan BMI / Input Tubuh
            if ($bmi < 18.5) {
                $c6ValueInput = 1.0; // Kurus
            } elseif ($bmi >= 25) {
                $c6ValueInput = 3.0; // Obesitas / Kelebihan Berat Badan
            } else {
                $c6ValueInput = 2.0; // Normal
            }

            if ($usia > 50 || $bmi >= 30) {
                $physicalRisk = true;
                $physicalNotes = "Penalti Fisik Diterapkan: ";
                if ($usia > 50 && $bmi >= 30) {
                    $physicalNotes .= "Usia (" . $usia . " thn) & Obesitas (BMI: " . $bmi . "). Medan Sulit (Level 3) mendapat penalti risiko.";
                } elseif ($usia > 50) {
                    $physicalNotes .= "Usia > 50 tahun (" . $usia . " thn). Medan Sulit (Level 3) mendapat penalti risiko.";
                } else {
                    $physicalNotes .= "Kategori Obesitas (BMI: " . $bmi . "). Medan Sulit (Level 3) mendapat penalti risiko.";
                }
            }
        }

        // Return empty results if there are no alternatives
        if (empty($alternatives)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data paket trip yang tersedia untuk dihitung (mungkin kuota sedang penuh atau tidak ada data yang cocok).',
                'results' => [],
                'steps' => [],
                'physical_risk' => $physicalRisk,
                'physical_notes' => $physicalNotes,
                'bmi' => $bmi
            ];
        }

        // 2. Build Decision Matrix (X)
        $matrixX = [];
        $altInfo = [];
        $penalizedPackages = [];

        foreach ($alternatives as $alt) {
            $id = $alt['id'];
            
            // Calculate Dynamic Facility score for C4
            $providedFacilities = !empty($alt['c4_fasilitas']) ? array_map('trim', explode(',', $alt['c4_fasilitas'])) : [];
            
            $c4Score = 0;
            if (!empty($prefFacilities) && is_array($prefFacilities)) {
                $matches = array_intersect($providedFacilities, $prefFacilities);
                $c4Score = count($matches);
            } else {
                $c4Score = count($providedFacilities);
            }

            // Convert to numeric scales (1-3) including C6
            $c1Val = floatval($this->convertToScale('C1', $alt['c1_harga']));
            $c2Val = floatval($this->convertToScale('C2', $alt['c2_ketinggian']));
            $c3Val = floatval($this->convertToScale('C3', $alt['c3_medan']));
            $c4Val = floatval($this->convertToScale('C4', $c4Score));
            $c5Val = floatval($this->convertToScale('C5', $alt['c5_waktu_tempuh']));
            $c6Val = floatval($this->convertToScale('C6', $c6ValueInput));

            // Apply Physical Risk Penalty to C3 (Kemudahan Medan) if level 3 (Sulit)
            $isPenalized = false;
            if ($physicalRisk && $c3Val == 3.0) {
                $c3Val = 5.0; 
                $isPenalized = true;
                $penalizedPackages[] = $id;
            }

            $altInfo[$id] = [
                'id' => $alt['id'],
                'nama' => isset($alt['nama_paket']) ? $alt['nama_paket'] : (isset($alt['nama']) ? $alt['nama'] : ''),
                'gunung' => $alt['gunung'],
                'kategori' => isset($alt['kategori']) ? $alt['kategori'] : 'Camping',
                'vendor' => isset($alt['vendor_name']) ? $alt['vendor_name'] : '',
                'gambar' => isset($alt['gambar']) ? $alt['gambar'] : '',
                'c1_harga' => $alt['c1_harga'],
                'c2_ketinggian' => $alt['c2_ketinggian'],
                'c3_medan' => $alt['c3_medan'],
                'c4_fasilitas_score' => $c4Score,
                'c5_waktu_tempuh' => $alt['c5_waktu_tempuh'],
                'c6_tubuh' => $c6ValueInput,
                'is_penalized' => $isPenalized,
                'detail' => $alt,
                'provided_facilities' => $providedFacilities
            ];

            $matrixX[$id] = [
                'C1' => $c1Val,
                'C2' => $c2Val,
                'C3' => $c3Val,
                'C4' => $c4Val,
                'C5' => $c5Val,
                'C6' => $c6Val
            ];
        }

        // 3. Calculate Dividers (Akar dari Jumlah Kuadrat Nilai Kriteria)
        $dividers = [
            'C1' => 0, 'C2' => 0, 'C3' => 0, 'C4' => 0, 'C5' => 0, 'C6' => 0
        ];

        foreach ($matrixX as $row) {
            foreach ($dividers as $kode => $val) {
                $dividers[$kode] += pow($row[$kode], 2);
            }
        }

        foreach ($dividers as $kode => $val) {
            $dividers[$kode] = sqrt($val);
        }

        // 4. Create Normalization Matrix (R)
        $matrixR = [];
        foreach ($matrixX as $id => $row) {
            foreach ($row as $kode => $val) {
                $div = $dividers[$kode];
                $matrixR[$id][$kode] = ($div > 0) ? ($val / $div) : 0;
            }
        }

        // 5. Create Weighted Normalization Matrix (Y)
        $matrixY = [];
        $weights = [];
        foreach ($criteria as $c) {
            $weights[$c['kode']] = floatval($c['bobot']);
        }

        $requiredKeys = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6'];
        foreach ($requiredKeys as $key) {
            if (!isset($weights[$key])) {
                $weights[$key] = (1 / count($requiredKeys));
            }
        }

        foreach ($matrixR as $id => $row) {
            foreach ($row as $kode => $val) {
                $wVal = isset($weights[$kode]) ? $weights[$kode] : 0.15;
                $matrixY[$id][$kode] = $val * $wVal;
            }
        }

        // 6. Calculate Optimization Value (y_i)
        $optimizations = [];
        foreach ($matrixY as $id => $row) {
            $sumBenefit = 0;
            $sumCost = 0;

            foreach ($row as $kode => $yVal) {
                $jenis = isset($criteriaMap[$kode]['jenis']) ? strtolower($criteriaMap[$kode]['jenis']) : 'cost';
                if ($jenis === 'benefit') {
                    $sumBenefit += $yVal;
                } else {
                    $sumCost += $yVal;
                }
            }

            $yi = $sumBenefit - $sumCost;
            
            $optimizations[$id] = [
                'id' => $id,
                'nama' => $altInfo[$id]['nama'],
                'gunung' => $altInfo[$id]['gunung'],
                'kategori' => $altInfo[$id]['kategori'],
                'vendor' => $altInfo[$id]['vendor'],
                'gambar' => $altInfo[$id]['gambar'],
                'c1_harga' => $altInfo[$id]['c1_harga'],
                'c2_ketinggian' => $altInfo[$id]['c2_ketinggian'],
                'c3_medan' => $altInfo[$id]['c3_medan'],
                'c4_fasilitas_score' => $altInfo[$id]['c4_fasilitas_score'],
                'c4_fasilitas' => isset($altInfo[$id]['detail']['c4_fasilitas']) ? $altInfo[$id]['detail']['c4_fasilitas'] : '',
                'c5_waktu_tempuh' => $altInfo[$id]['c5_waktu_tempuh'],
                'c6_tubuh' => $altInfo[$id]['c6_tubuh'],
                'is_penalized' => $altInfo[$id]['is_penalized'],
                'detail' => $altInfo[$id]['detail'],
                'provided_facilities' => $altInfo[$id]['provided_facilities'],
                'x_row' => $matrixX[$id],
                'r_row' => $matrixR[$id],
                'y_row' => $matrixY[$id],
                'benefit' => $sumBenefit,
                'cost' => $sumCost,
                'yi' => $yi
            ];
        }

        // 7. Sort by Yi descending (Ranking)
        usort($optimizations, function($a, $b) {
            if ($a['yi'] == $b['yi']) return 0;
            return ($a['yi'] > $b['yi']) ? -1 : 1;
        });

        // Add Rank property
        $rank = 1;
        foreach ($optimizations as &$opt) {
            $opt['rank'] = $rank++;
        }

        return [
            'success' => true,
            'results' => $optimizations,
            'physical_risk' => $physicalRisk,
            'physical_notes' => $physicalNotes,
            'bmi' => $bmi,
            'steps' => [
                'matrixX' => $matrixX,
                'dividers' => $dividers,
                'matrixR' => $matrixR,
                'matrixY' => $matrixY,
                'altInfo' => $altInfo
            ],
            'criteria' => $criteriaMap
        ];
    }
}
?>