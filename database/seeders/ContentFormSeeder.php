<?php

namespace Database\Seeders;

use App\Models\ContentForm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContentForm::create([
            'type' => 'review',
            'name' => 'Review Kantor Default',
            'is_active' => true,
            'version' => 1,
            'schema' => [
                [
                    'type' => 'radio-group',
                    'required' => true,
                    'label' => 'Sistem kerja di kantor ini seperti apa?',
                    'name' => 'work_system',
                    'access' => false,
                    'inline' => true,
                    'values' => [
                        ['label' => 'WFO', 'value' => 'WFO'],
                        ['label' => 'WFH', 'value' => 'WFH'],
                        ['label' => 'Hybrid', 'value' => 'Hybrid'],
                    ],
                ],
                [
                    'type' => 'time',
                    'label' => 'Jam masuk kerja',
                    'name' => 'work_start_time',
                    'access' => false,
                ],
                [
                    'type' => 'time',
                    'label' => 'Jam pulang kerja',
                    'name' => 'work_end_time',
                    'access' => false,
                ],
                [
                    'type' => 'checkbox-group',
                    'label' => 'Hari kerja',
                    'name' => 'work_days',
                    'access' => false,
                    'values' => [
                        ['label' => 'Senin', 'value' => 'senin'],
                        ['label' => 'Selasa', 'value' => 'selasa'],
                        ['label' => 'Rabu', 'value' => 'rabu'],
                        ['label' => 'Kamis', 'value' => 'kamis'],
                        ['label' => 'Jumat', 'value' => 'jumat'],
                        ['label' => 'Sabtu', 'value' => 'sabtu'],
                        ['label' => 'Minggu', 'value' => 'minggu'],
                    ],
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Apakah ada makan siang?',
                    'name' => 'has_lunch_break',
                    'access' => false,
                    'inline' => true,
                    'values' => [
                        ['label' => 'Ada', 'value' => 'yes'],
                        ['label' => 'Tidak ada', 'value' => 'no'],
                    ],
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Apakah gaji dibayar tepat waktu?',
                    'name' => 'on_time_salary',
                    'inline' => true,
                    'values' => [
                        ['label' => 'Selalu tepat waktu', 'value' => 'always'],
                        ['label' => 'Sering terlambat', 'value' => 'sometimes'],
                        ['label' => 'Tidak menentu', 'value' => 'unpredictable'],
                    ],
                ],
                [
                    'type' => 'checkbox-group',
                    'label' => 'Bank Payroll Utama',
                    'name' => 'payroll_bank',
                    'other' => 'true',
                    'values' => [
                        ['label' => 'BCA', 'value' => 'bca'],
                        ['label' => 'Mandiri', 'value' => 'mandiri'],
                        ['label' => 'BNI', 'value' => 'bni'],
                        ['label' => 'BRI', 'value' => 'bri'],
                    ],
                ],
                [
                    'type' => 'radio-group', // Atau 'radio-group' jika ingin ditampilkan semua
                    'label' => 'Rentang Gaji Bulanan',
                    'name' => 'salary_range',
                    'description' => 'Tenang, data ini hanya untuk statistik anonim.',
                    'values' => [
                        ['label' => '< Rp 3 Juta', 'value' => 'under_3m'],
                        ['label' => 'Rp 3 Juta - Rp 4 Juta', 'value' => '3m_4m'],
                        ['label' => 'Rp 4 Juta - Rp 5 Juta', 'value' => '4m_5m'],
                        ['label' => 'Rp 5 Juta - Rp 6 Juta', 'value' => '5m_6m'],
                        ['label' => 'Rp 6 Juta - Rp 7 Juta', 'value' => '6m_7m'],
                        ['label' => 'Rp 7 Juta - Rp 8 Juta', 'value' => '7m_8m'],
                        ['label' => 'Rp 8 Juta - Rp 9 Juta', 'value' => '8m_9m'],
                        ['label' => '> Rp 9 Juta', 'value' => 'above_9m'],
                        ['label' => 'Rahasia / Enggan Menjawab', 'value' => 'private'],
                    ],
                ],
                [
                    'type' => 'checkbox-group',
                    'label' => 'Komponen Gaji',
                    'name' => 'salary_components',
                    'other' => 'true',
                    'values' => [
                        ['label' => 'Ada Gaji Pokok Tetap', 'value' => 'base_salary'],
                        ['label' => 'Ada Uang Makan & Transport', 'value' => 'allowance'],
                        ['label' => 'Ada Insentif / Bonus / Komisi', 'value' => 'incentive'],
                        ['label' => 'Ada THR Full (1 Bulan Gaji)', 'value' => 'thr_full'],
                        ['label' => 'Ada Bonus Akhir Tahun / Performance', 'value' => 'bonus_annual'],
                    ],
                ],
                [
                    'type' => 'checkbox-group',
                    'label' => 'Benefit & Fasilitas Nyata',
                    'name' => 'real_benefits',
                    'other' => 'true',
                    'values' => [
                        ['label' => 'Parkir Gratis/Subsidi', 'value' => 'free_parking'],
                        ['label' => 'Kopi & Snack Gratis', 'value' => 'free_snacks'],
                        ['label' => 'BPJS Kesehatan & Ketenagakerjaan', 'value' => 'bpjs'],
                        ['label' => 'Asuransi Swasta tambahan', 'value' => 'private_insurance'],
                        ['label' => 'Cuti Bersama TIDAK potong cuti tahunan', 'value' => 'holiday_bonus'],
                    ],
                ],
                [
                    'type' => 'checkbox-group',
                    'label' => 'Fasilitas di Area Kantor',
                    'name' => 'office_facilities',
                    'other' => 'true',
                    'values' => [
                        ['label' => 'Musholla Layak', 'value' => 'prayer_room'],
                        ['label' => 'Kantin Karyawan Murah', 'value' => 'canteen'],
                        ['label' => 'Pantry (Kopi/Teh Gratis)', 'value' => 'pantry'],
                        ['label' => 'Ruang Laktasi / Menyusui', 'value' => 'nursing_room'],
                        ['label' => 'Gym / Area Relax', 'value' => 'entertainment'],
                    ],
                ],
                [
                    'type' => 'checkbox-group',
                    'label' => 'Potongan Wajib Karyawan',
                    'name' => 'salary_deductions',
                    'other' => 'true',
                    'values' => [
                        ['label' => 'BPJS Kesehatan & Ketenagakerjaan', 'value' => 'bpjs'],
                        ['label' => 'Tabungan Wajib / Koperasi', 'value' => 'koperasi'],
                        ['label' => 'Denda Terlambat (per menit/jam)', 'value' => 'late_fine'],
                        ['label' => 'Pajak PPh21 (Ditanggung Karyawan)', 'value' => 'tax_pph21'],
                    ],
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Kebijakan Keterlambatan',
                    'name' => 'late_policy',
                    'other' => 'true',
                    'values' => [
                        ['label' => 'Sangat Ketat (Potong Gaji)', 'value' => 'strict'],
                        ['label' => 'Toleransi Jam Kerja (Flexible)', 'value' => 'flexible'],
                        ['label' => 'Hanya Teguran Lisan', 'value' => 'verbal'],
                    ],
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Kemudahan Izin / Cuti',
                    'name' => 'leave_difficulty',
                    'other' => 'true',
                    'values' => [
                        ['label' => 'Sangat Mudah', 'value' => 'very_easy'],
                        ['label' => 'Sedang (Butuh alasan kuat)', 'value' => 'medium'],
                        ['label' => 'Dipasulit / Harus cari pengganti', 'value' => 'hard'],
                    ],
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Work-life balance',
                    'name' => 'work_life_balance',
                    'access' => false,
                    'inline' => true,
                    'values' => [
                        ['label' => '1', 'value' => 1],
                        ['label' => '2', 'value' => 2],
                        ['label' => '3', 'value' => 3],
                        ['label' => '4', 'value' => 4],
                        ['label' => '5', 'value' => 5],
                    ],
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Lingkungan kerja',
                    'name' => 'work_environment',
                    'access' => false,
                    'inline' => true,
                    'values' => [
                        ['label' => '1', 'value' => 1],
                        ['label' => '2', 'value' => 2],
                        ['label' => '3', 'value' => 3],
                        ['label' => '4', 'value' => 4],
                        ['label' => '5', 'value' => 5],
                    ],
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Manajemen',
                    'name' => 'management',
                    'access' => false,
                    'inline' => true,
                    'values' => [
                        ['label' => '1', 'value' => 1],
                        ['label' => '2', 'value' => 2],
                        ['label' => '3', 'value' => 3],
                        ['label' => '4', 'value' => 4],
                        ['label' => '5', 'value' => 5],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'required' => true,
                    'label' => 'Ceritakan pengalaman kerja kamu',
                    'name' => 'experience',
                    'access' => false,
                    'rows' => 4,
                ],
                [
                    'type' => 'textarea',
                    'label' => 'Hal positif dari kantor ini',
                    'name' => 'positive_notes',
                    'access' => false,
                    'rows' => 3,
                ],
            ],
        ]);

        ContentForm::create([
            'type' => 'menfess',
            'name' => 'Menfess Default',
            'is_active' => true,
            'version' => 1,
            'schema' => [
                [
                    'type' => 'textarea',
                    'required' => true,
                    'label' => 'Tulis menfess kamu',
                    'name' => 'message',
                    'rows' => 4,
                ],
                [
                    'type' => 'text',
                    'required' => false,
                    'label' => 'Divisi/Departemen (opsional)',
                    'name' => 'department',
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Mood kamu sekarang',
                    'name' => 'mood',
                    'inline' => true,
                    'values' => [
                        ['label' => '😊 Senang', 'value' => 'happy'],
                        ['label' => '😐 Biasa', 'value' => 'neutral'],
                        ['label' => '😔 Sedih', 'value' => 'sad'],
                        ['label' => '😤 Kesal', 'value' => 'angry'],
                    ],
                ],
                
            ]
        ]);

        ContentForm::create([
            'type' => 'qna',
            'name' => 'qna Default',
            'is_active' => true,
            'version' => 1,
            'schema' => [
                [
                    'type' => 'textarea',
                    'required' => true,
                    'label' => 'Detail pertanyaanmu',
                    'name' => 'content',
                    'rows' => 3,
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Kategori pertanyaan',
                    'name' => 'question_category',
                    'values' => [
                        ['label' => 'Gaji & Benefit', 'value' => 'salary'],
                        ['label' => 'Kultur Kerja', 'value' => 'culture'],
                        ['label' => 'Proses Rekrutmen', 'value' => 'recruitment'],
                        ['label' => 'Jenjang Karir', 'value' => 'career'],
                        ['label' => 'Lainnya', 'value' => 'other'],
                    ],
                ], 
            ]
        ]);

        ContentForm::create([
            'type' => 'cerita_magang',
            'name' => 'cerita magang Default',
            'is_active' => true,
            'version' => 1,
            'schema' => [
                [
                    'type' => 'text',
                    'required' => true,
                    'label' => 'Judul cerita',
                    'name' => 'title',
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Durasi magang',
                    'name' => 'internship_duration',
                    'values' => [
                        ['label' => '< 1 Bulan', 'value' => 'under_1m'],
                        ['label' => '1-3 Bulan', 'value' => '1m_3m'],
                        ['label' => '3-6 Bulan', 'value' => '3m_6m'],
                        ['label' => '> 6 Bulan', 'value' => 'above_6m'],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'required' => true,
                    'label' => 'Ceritakan pengalaman magangmu',
                    'name' => 'story',
                    'rows' => 4,
                ],
                [
                    'type' => 'radio-group',
                    'label' => 'Rekomendasikan tempat magang ini?',
                    'name' => 'recommend',
                    'inline' => true,
                    'values' => [
                        ['label' => '👍 Ya', 'value' => 'yes'],
                        ['label' => '👎 Tidak', 'value' => 'no'],
                        ['label' => '🤔 Tergantung', 'value' => 'maybe'],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => 'Apa yang kamu pelajari?',
                    'name' => 'lessons',
                    'rows' => 3,
                ],
            ],
        ]);
    }
}
