<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            ['name' => 'Budi Santoso', 'status' => 'aktif', 'phone' => '081234567890', 'program' => 'Reguler'],
            ['name' => 'Alice Wijaya', 'status' => 'aktif', 'phone' => '082198765432', 'program' => 'Intensif'],
            ['name' => 'Bob Pratama', 'status' => 'aktif', 'phone' => '085711223344', 'program' => 'Reguler'],
            ['name' => 'Charlie Chen', 'status' => 'aktif', 'phone' => '089988776655', 'program' => 'Intensif'],
            ['name' => 'Dewa Putra', 'status' => 'aktif', 'phone' => '081122334455', 'program' => 'Reguler'],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(
                ['name' => $student['name']],
                [
                    'status' => $student['status'],
                    'phone' => $student['phone'],
                    'program' => $student['program'],
                ]
            );
        }
    }
}
