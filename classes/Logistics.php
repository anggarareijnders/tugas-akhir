<?php
// classes/Logistics.php

class Logistics {
    /**
     * Master list of essential mountaineering gear required for a safe climb.
     */
    public static function getMasterEquipment() {
        return [
            'F1'  => ['code' => 'F1',  'name' => 'Tenda Dome', 'category' => 'Tempat Tinggal'],
            'F2'  => ['code' => 'F2',  'name' => 'Tas Gunung (Carrier)', 'category' => 'Perlengkapan Utama'],
            'F3'  => ['code' => 'F3',  'name' => 'Sleeping Bag (Kantong Tidur)', 'category' => 'Kehangatan'],
            'F4'  => ['code' => 'F4',  'name' => 'Cemilan & Snack Logistik', 'category' => 'Konsumsi'],
            'F6'  => ['code' => 'F6',  'name' => 'Homestay / Basecamp', 'category' => 'Akomodasi'],
            'F8'  => ['code' => 'F8',  'name' => 'Jasa Porter Tim', 'category' => 'Layanan'],
            'F11' => ['code' => 'F11', 'name' => 'Makan Utama Selama Pendakian', 'category' => 'Konsumsi'],
            'F12' => ['code' => 'F12', 'name' => 'P3K Standar Medis', 'category' => 'Keselamatan'],
            'M1'  => ['code' => 'M1',  'name' => 'Matras Alumunium / Spon Camping', 'category' => 'Kenyamanan Tidur'],
            'M2'  => ['code' => 'M2',  'name' => 'Alat Masak Portable & Gas Mini', 'category' => 'Logistik'],
            'M3'  => ['code' => 'M3',  'name' => 'Jaket Thermal / Windbreaker', 'category' => 'Pakaian Hangat'],
            'M4'  => ['code' => 'M4',  'name' => 'Headlamp / Senter & Baterai Cadangan', 'category' => 'Penerangan'],
            'M5'  => ['code' => 'M5',  'name' => 'Jas Hujan (Raincoat Setelan)', 'category' => 'Perlindungan Hujan'],
            'M6'  => ['code' => 'M6',  'name' => 'Sepatu Gunung Grip Kuat', 'category' => 'Alas Kaki'],
            'M7'  => ['code' => 'M7',  'name' => 'Botol Air Minum Reusable (Min 2-3 Liter)', 'category' => 'Hidrasi'],
        ];
    }

    /**
     * Compare facilities provided by vendor with master equipment.
     * Returns list of self-brought items required by the climber.
     *
     * @param string|array $vendorFacilities Provided facility codes (e.g. 'F1,F3,F4,F6,F12')
     * @param string $kategori Trip category ('Camping' or 'Tektok')
     * @return array List of items climber must bring independently
     */
    public static function getSelfBroughtEquipment($vendorFacilities, $kategori = 'Camping') {
        $providedCodes = is_array($vendorFacilities) 
            ? $vendorFacilities 
            : (!empty($vendorFacilities) ? explode(',', $vendorFacilities) : []);
        
        $master = self::getMasterEquipment();
        $selfBrought = [];

        foreach ($master as $code => $item) {
            // For Tektok trips, tents, sleeping bags, mattresses are not strictly needed
            if ($kategori === 'Tektok' && in_array($code, ['F1', 'F3', 'M1'])) {
                continue;
            }

            if (!in_array($code, $providedCodes)) {
                $selfBrought[] = $item;
            }
        }

        return $selfBrought;
    }

    /**
     * Map facility code to human friendly name
     */
    public static function getFacilityName($code) {
        $master = self::getMasterEquipment();
        return isset($master[$code]) ? $master[$code]['name'] : $code;
    }
}
?>
