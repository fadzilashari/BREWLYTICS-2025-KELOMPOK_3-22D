<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerNames = [
            'Budi Santoso', 'Siti Aminah', 'Andi Wijaya', 'Dewi Lestari', 'Agus Prayitno',
            'Rina Marlina', 'Eko Saputra', 'Ani Suryani', 'Iwan Setiawan', 'Maya Indah',
            'Rizky Pratama', 'Linda Sari', 'Dedi Kurniawan', 'Novi Rahmawati', 'Hendra Gunawan',
            'Putri Ayu', 'Fajar Ramadhan', 'Siska Amelia', 'Bambang Hermawan', 'Diana Fitri',
            'Aditya Nugraha', 'Yanti Susanti', 'Rahmat Hidayat', 'Lusi Lestari', 'Denny Wahyudi',
            'Ratna Sari', 'Taufik Hidayat', 'Nina Herawati', 'Arif Munandar', 'Sari Wahyuni'
        ];

        $descriptions = [
            'Kopinya enak banget, rasa gulanya pas dan susunya creamy. Tempatnya juga asik buat nongkrong.',
            'Baristanya ramah-ramah, pelayanannya cepat meskipun lagi ramai. Pasti bakal balik lagi ke sini.',
            'Suasananya tenang dan nyaman, cocok banget buat nugas atau kerja remote. Wifi kencang!',
            'Cappucino-nya mantap, foamnya pas. Harganya juga bersahabat di kantong mahasiswa.',
            'Tempatnya aesthetic parah, banyak spot foto bagus. Minuman Gula Arennya juara!',
            'Layanan cepat, kebersihan terjaga. Protokol kesehatan juga diterapkan dengan baik.',
            'Macha Milk-nya paling enak se-kota ini, tidak terlalu manis, pas banget di lidah.',
            'Wifi kencang dan banyak stop kontak. Surga buat yang hobi WFC (Work From Cafe).',
            'Meskipun tempatnya tidak terlalu luas, tapi vibe-nya cozy banget. Betah lama-lama di sini.',
            'Suka banget sama Vanila Latte-nya. Aroma kopinya harum dan tidak bikin deg-degan.',
        ];

        for ($i = 0; $i < 30; $i++) {
            DB::table('rewards')->insert([
                'name' => $customerNames[$i % count($customerNames)],
                'rating' => rand(4, 5),
                'description' => $descriptions[array_rand($descriptions)],
                'review_date' => Carbon::now()->subDays(rand(0, 30)),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
