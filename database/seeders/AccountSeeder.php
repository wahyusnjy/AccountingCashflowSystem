<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // KAS
            ['id' => '1.10.01.01', 'account_name' => 'Kas Kecil H.O', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.10.02.01', 'account_name' => 'Dana', 'type' => 'asset', 'normal_balance' => 'debit'],
            
            // PIUTANG SPP REGULER
            ['id' => '1.11.01.01', 'account_name' => 'Piutang SPP Reguler - September', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.01.02', 'account_name' => 'Piutang SPP Reguler - Oktober', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.01.03', 'account_name' => 'Piutang SPP Reguler - November', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.01.04', 'account_name' => 'Piutang SPP Reguler - Desember', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.01.05', 'account_name' => 'Piutang SPP Reguler - Januari', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.01.06', 'account_name' => 'Piutang SPP Reguler - Februari', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.01.07', 'account_name' => 'Piutang SPP Reguler - Maret', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.01.08', 'account_name' => 'Piutang SPP Reguler - April', 'type' => 'asset', 'normal_balance' => 'debit'],
            
            // PIUTANG SPP INTENSIF
            ['id' => '1.11.02.01', 'account_name' => 'Piutang SPP Intensif - September', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.02.02', 'account_name' => 'Piutang SPP Intensif - Oktober', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.02.03', 'account_name' => 'Piutang SPP Intensif - November', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.02.04', 'account_name' => 'Piutang SPP Intensif - Desember', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.02.05', 'account_name' => 'Piutang SPP Intensif - Januari', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.02.06', 'account_name' => 'Piutang SPP Intensif - Februari', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.03.07', 'account_name' => 'Piutang SPP Intensif - Maret', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['id' => '1.11.04.08', 'account_name' => 'Piutang SPP Intensif - April', 'type' => 'asset', 'normal_balance' => 'debit'],

            // EKUITAS
            ['id' => '3.14.01.01', 'account_name' => 'Saldo Laba Tahun Berjalan', 'type' => 'equity', 'normal_balance' => 'credit'],

            // PENDAPATAN SPP REGULER
            ['id' => '4.15.01.01', 'account_name' => 'Pendapatan SPP Reguler - September', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.01.02', 'account_name' => 'Pendapatan SPP Reguler - Oktober', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.01.03', 'account_name' => 'Pendapatan SPP Reguler - November', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.01.04', 'account_name' => 'Pendapatan SPP Reguler - Desember', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.01.05', 'account_name' => 'Pendapatan SPP Reguler - Januari', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.01.06', 'account_name' => 'Pendapatan SPP Reguler - Februari', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.01.07', 'account_name' => 'Pendapatan SPP Reguler - Maret', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.01.08', 'account_name' => 'Pendapatan SPP Reguler - April', 'type' => 'revenue', 'normal_balance' => 'credit'],

            // PENDAPATAN SPP INTENSIF
            ['id' => '4.15.02.01', 'account_name' => 'Pendapatan SPP Intensif - September', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.02.02', 'account_name' => 'Pendapatan SPP Intensif - Oktober', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.02.03', 'account_name' => 'Pendapatan SPP Intensif - November', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.02.04', 'account_name' => 'Pendapatan SPP Intensif - Desember', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.02.05', 'account_name' => 'Pendapatan SPP Intensif - Januari', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.02.06', 'account_name' => 'Pendapatan SPP Intensif - Februari', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.02.07', 'account_name' => 'Pendapatan SPP Intensif - Maret', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['id' => '4.15.02.08', 'account_name' => 'Pendapatan SPP Intensif - April', 'type' => 'revenue', 'normal_balance' => 'credit'],

            // BEBAN USAHA
            ['id' => '5.16.01.01', 'account_name' => 'Gaji Pengajar', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['id' => '5.16.01.02', 'account_name' => 'Listrik', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['id' => '5.16.01.03', 'account_name' => 'Internet/VPS', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['id' => '5.16.01.04', 'account_name' => 'Konsumsi', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['id' => '5.16.01.05', 'account_name' => 'Kegiatan', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['id' => '5.16.01.06', 'account_name' => 'Promosi', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['id' => '5.16.01.07', 'account_name' => 'ATK dan Perlengkapan', 'type' => 'expense', 'normal_balance' => 'debit'],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['id' => $account['id']],
                [
                    'account_name' => $account['account_name'],
                    'type' => $account['type'],
                    'normal_balance' => $account['normal_balance'],
                ]
            );
        }
    }
}
