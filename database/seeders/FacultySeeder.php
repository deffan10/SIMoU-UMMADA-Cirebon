<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            [
                'name' => 'Fakultas Teknik',
                'code' => 'FT',
                'programs' => [
                    ['name' => 'Teknik Informatika', 'code' => 'TI', 'level' => 'S1'],
                    ['name' => 'Teknik Sipil', 'code' => 'TS', 'level' => 'S1'],
                    ['name' => 'Teknik Mesin', 'code' => 'TM', 'level' => 'S1'],
                ],
            ],
            [
                'name' => 'Fakultas Ekonomi & Bisnis',
                'code' => 'FEB',
                'programs' => [
                    ['name' => 'Manajemen', 'code' => 'MN', 'level' => 'S1'],
                    ['name' => 'Akuntansi', 'code' => 'AK', 'level' => 'S1'],
                ],
            ],
            [
                'name' => 'Fakultas Keguruan & Ilmu Pendidikan',
                'code' => 'FKIP',
                'programs' => [
                    ['name' => 'Pendidikan Bahasa Inggris', 'code' => 'PBI', 'level' => 'S1'],
                    ['name' => 'Pendidikan Matematika', 'code' => 'PM', 'level' => 'S1'],
                    ['name' => 'Pendidikan Guru SD', 'code' => 'PGSD', 'level' => 'S1'],
                ],
            ],
            [
                'name' => 'Fakultas Agama Islam',
                'code' => 'FAI',
                'programs' => [
                    ['name' => 'Pendidikan Agama Islam', 'code' => 'PAI', 'level' => 'S1'],
                    ['name' => 'Hukum Ekonomi Syariah', 'code' => 'HES', 'level' => 'S1'],
                ],
            ],
            [
                'name' => 'Fakultas Ilmu Kesehatan',
                'code' => 'FIKES',
                'programs' => [
                    ['name' => 'Keperawatan', 'code' => 'KEP', 'level' => 'S1'],
                    ['name' => 'Farmasi', 'code' => 'FAR', 'level' => 'S1'],
                ],
            ],
        ];

        foreach ($faculties as $facultyData) {
            $programs = $facultyData['programs'];
            unset($facultyData['programs']);

            $faculty = Faculty::create($facultyData);

            foreach ($programs as $program) {
                StudyProgram::create(array_merge($program, [
                    'faculty_id' => $faculty->id,
                ]));
            }
        }
    }
}
