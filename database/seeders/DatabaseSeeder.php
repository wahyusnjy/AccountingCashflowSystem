<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Bendahara 1',
            'email' => 'bendahara1@fikra.com',
            'password' => bcrypt('password'),
            'backup_email' => 'backup1@example.com',
        ]);

        User::create([
            'name' => 'Bendahara 2',
            'email' => 'bendahara2@fikra.com',
            'password' => bcrypt('password'),
            'backup_email' => 'wahyusnjy@gmail.com',
        ]);

        $this->call(AccountSeeder::class);
        $this->call(StudentSeeder::class);
    }
}
