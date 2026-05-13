<?php

// database/seeders/IndustrySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Industry;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            'Teknologi Informasi',
            'Startup',
            'Fintech',
            'E-Commerce',
            'Software House / Agency', // Tambahan: Karena banyak anak IT kerja di sini
            'Telekomunikasi',
            'Perbankan',
            'Asuransi',
            'Investasi & Modal Ventura', // Tambahan
            'Kesehatan & Farmasi',
            'Pendidikan & EdTech',
            'Manufaktur / Pabrik',
            'Otomotif', // Tambahan
            'Retail / FMCG',
            'Logistik & Supply Chain',
            'Transportasi & Ride Hailing',
            'Konstruksi & Teknik',
            'Properti & Real Estate',
            'Media, Penyiaran & Hiburan',
            'Desain & Industri Kreatif',
            'Periklanan & Pemasaran', // Tambahan
            'Pariwisata & Perhotelan',
            'Kuliner (Food & Beverage)',
            'Event Organizer & MICE', // Tambahan
            'Pertambangan, Minyak & Gas',
            'Energi Terbarukan', // Tambahan: Sektor masa depan
            'Pertanian, Peternakan & Perikanan',
            'Konsultan Manajemen & Bisnis',
            'Hukum & Legal',
            'Pemerintahan / BUMN',
            'Layanan Keamanan', // Tambahan
            'Layanan Kebersihan & Fasilitas', // Tambahan
            'NGO / Organisasi Nirlaba',
            'Olahraga & Kebugaran', // Tambahan (Gym, Club, dll)
            'Kecantikan & Perawatan Tubuh', // Tambahan (Salon, Skin Care)
            'Lainnya', // Wajib ada
        ];

        foreach ($industries as $industry) {
            Industry::firstOrCreate([
                'name' => $industry,
            ]);
        }
    }
}