<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Satker;
use App\Models\Kendaraan;
use App\Models\Senjata;
use App\Models\Amunisi;
use App\Models\Alsus;
use App\Models\Alsintor;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure we have sufficient Satkers
        $satkerNames = [
            'Polres Metro Jakarta Selatan',
            'Polres Metro Jakarta Pusat',
            'Polres Metro Jakarta Timur',
            'Polres Metro Jakarta Utara',
            'Polres Metro Jakarta Barat',
            'Polda Metro Jaya',
            'Biro Logistik Polda Metro',
            'Satbrimob Polda Metro',
            'Ditlantas Polda Metro'
        ];

        foreach ($satkerNames as $name) {
            Satker::firstOrCreate(['nama_satker' => $name]);
        }

        $satkerIds = Satker::pluck('id')->toArray();
        $kondisi = ['Baik', 'Rusak Ringan', 'Rusak Berat'];
        $bbm = ['Pertalite', 'Pertamax', 'Pertamina Dex', 'Listrik'];
        $status_penyimpanan = ['Gudang', 'Personel', 'Dipinjamkan'];
        $laras = ['Panjang', 'Pendek'];

        // 2. Dummy Kendaraan (50)
        $kendaraanJenis = ['Toyota Avanza', 'Honda Vario 125', 'Mitsubishi Pajero Sport', 'Isuzu Elf', 'Yamaha NMAX', 'Toyota Hilux', 'Mazda 6 Polantas'];
        for ($i = 1; $i <= 50; $i++) {
            Kendaraan::create([
                'satker_id' => $satkerIds[array_rand($satkerIds)],
                'jenis_kendaraan' => $kendaraanJenis[array_rand($kendaraanJenis)],
                'nup' => 'NUP-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'no_rangka' => 'MHR' . strtoupper(substr(md5(uniqid()), 0, 14)),
                'nopol' => 'POL ' . rand(1000, 9999) . ' ' . strtoupper(substr(md5(uniqid()), 0, 3)),
                'kondisi' => $kondisi[array_rand($kondisi)],
                'bahan_bakar' => $bbm[array_rand($bbm)],
                'penanggung_jawab' => 'Petugas ' . $i,
                'nrp' => 'NRP' . rand(70000000, 99999999),
                'keterangan' => 'Unit Kendaraan Operasional Dummy ' . $i,
                'no_mesin' => 'ENG-' . rand(100000, 999999),
                'tahun_pembuatan' => rand(2015, 2024),
                'jenis_roda' => rand(0, 1) ? 'R2' : 'R4'
            ]);
        }

        // 3. Dummy Senjata (50)
        $senpiJenis = ['Pistol HS-9', 'Glock 17', 'Revolver S&W', 'SS1-V1', 'V2 Sabhara', 'Steyr AUG', 'Sig Sauer P226'];
        for ($i = 1; $i <= 50; $i++) {
            Senjata::create([
                'satker_id' => $satkerIds[array_rand($satkerIds)],
                'jenis_senpi' => $senpiJenis[array_rand($senpiJenis)],
                'laras' => $laras[array_rand($laras)],
                'nup' => 'SEN-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'no_senpi' => 'WPN-' . rand(100000, 999999),
                'kondisi' => $kondisi[array_rand($kondisi)],
                'penanggung_jawab' => 'Personel ' . $i,
                'nrp' => 'NRP' . rand(70000000, 99999999),
                'status_penyimpanan' => $status_penyimpanan[array_rand($status_penyimpanan)],
                'masa_berlaku_simsa' => date('Y-m-d', strtotime('+' . rand(1, 24) . ' months')),
                'jenis_amunisi_dibawa' => '9mm',
                'jumlah_amunisi_dibawa' => rand(10, 50),
                'keterangan' => 'Senjata Inventaris Dummy ' . $i
            ]);
        }

        // 4. Dummy Amunisi (50)
        $amunisiJenis = ['9mm Parabellum', '5.56mm NATO', '.38 Special', '7.62mm NATO', '.45 ACP', 'Peluru Karet'];
        for ($i = 1; $i <= 50; $i++) {
            Amunisi::create([
                'satker_id' => $satkerIds[array_rand($satkerIds)],
                'jenis_amunisi' => $amunisiJenis[array_rand($amunisiJenis)],
                'jumlah' => rand(500, 5000),
                'status_penyimpanan' => rand(0, 1) ? 'Gudang' : 'Personel',
                'keterangan' => 'Stok Amunisi Dummy ' . $i
            ]);
        }

        // 5. Dummy Alsus (50)
        $alsusJenis = ['Rompi Anti Peluru', 'Helm Taktis', 'Gas Air Mata', 'Tameng PHH', 'Borgol', 'Senter Lalin'];
        for ($i = 1; $i <= 50; $i++) {
            Alsus::create([
                'satker_id' => $satkerIds[array_rand($satkerIds)],
                'jenis_barang' => $alsusJenis[array_rand($alsusJenis)],
                'nup' => 'ALS-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'kondisi' => $kondisi[array_rand($kondisi)],
                'keterangan' => 'Alat Khusus Dummy ' . $i
            ]);
        }

        // 6. Dummy Alsintor (50)
        $alsintorJenis = ['Laptop Core i7', 'Printer Laserjet', 'AC Split', 'Meja Kerja', 'Lemari Arsip', 'HT Motorolla'];
        for ($i = 1; $i <= 50; $i++) {
            Alsintor::create([
                'satker_id' => $satkerIds[array_rand($satkerIds)],
                'jenis_barang' => $alsintorJenis[array_rand($alsintorJenis)],
                'nup' => 'AST-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'kondisi' => $kondisi[array_rand($kondisi)],
                'keterangan' => 'Alat Inventaris Kantor Dummy ' . $i
            ]);
        }
    }
}
