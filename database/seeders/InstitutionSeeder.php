<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            [
                'name' => 'Universitas Indonesia',
                'type' => 'universitas',
                'country' => 'Indonesia',
                'city' => 'Depok',
                'website' => 'https://ui.ac.id',
            ],
            [
                'name' => 'Institut Teknologi Bandung',
                'type' => 'universitas',
                'country' => 'Indonesia',
                'city' => 'Bandung',
                'website' => 'https://itb.ac.id',
            ],
            [
                'name' => 'Universitas Gadjah Mada',
                'type' => 'universitas',
                'country' => 'Indonesia',
                'city' => 'Yogyakarta',
                'website' => 'https://ugm.ac.id',
            ],
            [
                'name' => 'PT Telkom Indonesia',
                'type' => 'industri',
                'country' => 'Indonesia',
                'city' => 'Bandung',
                'website' => 'https://telkom.co.id',
            ],
            [
                'name' => 'Dinas Pendidikan Kota Cirebon',
                'type' => 'pemerintah',
                'country' => 'Indonesia',
                'city' => 'Cirebon',
            ],
            [
                'name' => 'Universiti Malaya',
                'type' => 'universitas',
                'country' => 'Malaysia',
                'city' => 'Kuala Lumpur',
                'website' => 'https://um.edu.my',
            ],
            [
                'name' => 'BPJS Kesehatan',
                'type' => 'pemerintah',
                'country' => 'Indonesia',
                'city' => 'Jakarta',
                'website' => 'https://bpjs-kesehatan.go.id',
            ],
            [
                'name' => 'Rumah Sakit Umum Daerah Cirebon',
                'type' => 'pemerintah',
                'country' => 'Indonesia',
                'city' => 'Cirebon',
            ],
            [
                'name' => 'Bank Syariah Indonesia',
                'type' => 'industri',
                'country' => 'Indonesia',
                'city' => 'Jakarta',
                'website' => 'https://bankbsi.co.id',
            ],
            [
                'name' => 'Yayasan Pendidikan Nusantara',
                'type' => 'ngo',
                'country' => 'Indonesia',
                'city' => 'Jakarta',
            ],
        ];

        foreach ($institutions as $institution) {
            Institution::create($institution);
        }
    }
}
