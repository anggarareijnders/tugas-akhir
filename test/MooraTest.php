<?php
// 1. BARIS PALING WAJIB: Memanggil autoload Composer
require_once __DIR__ . '/../composer_libs/autoload.php';

// 2. Memanggil library utama PHPUnit
use PHPUnit\Framework\TestCase;

// 3. Panggil file mesin MOORA milikmu
require_once __DIR__ . '/../MooraEngine.php'; 

class MooraTest extends TestCase
{
    private $moora;

    // Fungsi ini dijalankan otomatis sebelum setiap pengujian dimulai
    protected function setUp(): void
    {
        // Instansiasi class mesin algoritma
        $this->moora = new MooraEngine(); 
    }

    // ---------------------------------------------------------
    // Pengujian JALUR 1: Bagaimana jika data alternatif kosong?
    // ---------------------------------------------------------
    public function testDataKosongMengembalikanFalse()
    {
        $alternatifKosong = []; // Simulasi tidak ada data paket
        $kriteria = [
            ['kode' => 'C1', 'bobot' => 0.3, 'jenis' => 'Cost'],
        ];

        // Eksekusi fungsi hitung
        $hasil = $this->moora->calculate($alternatifKosong, $kriteria);

        // Validasi: Kita berharap hasilnya adalah FALSE karena data kosong
        $this->assertFalse($hasil);
    }

    // ---------------------------------------------------------
    // Pengujian JALUR NORMAL: Menguji perhitungan pembagi matematis
    // ---------------------------------------------------------
    public function testHitungPembagiBerjalanBenar()
    {
        // 1. Arrange (Persiapan Data Dummy)
        $alternatif = [
            ['id_paket' => 1, 'c1_harga' => 300000, 'c2_ketinggian' => 2000],
            ['id_paket' => 2, 'c1_harga' => 450000, 'c2_ketinggian' => 3000],
        ];

        // 2. Act (Eksekusi fungsi yang mau diuji)
        $pembagi = $this->moora->hitungPembagi($alternatif);

        // 3. Assert (Validasi Hasil)
        $this->assertIsArray($pembagi); 
        $this->assertNotEmpty($pembagi);
        $this->assertArrayHasKey('c1_harga', $pembagi);
    }
}