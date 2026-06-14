<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingService
{
    /**
     * Map month names to their official Chart of Accounts postfix codes.
     */
    protected const MONTH_POSTFIXES = [
        'September' => '01',
        'Oktober'   => '02',
        'November'  => '03',
        'Desember'  => '04',
        'Januari'   => '05',
        'Februari'  => '06',
        'Maret'     => '07',
        'April'     => '08',
    ];

    /**
     * Automate journal entries for cash/transfer student payments (Pemasukan).
     *
     * @param string $date Date of transaction (YYYY-MM-DD)
     * @param string $studentName Name of the student
     * @param float|string $amount Amount paid
     * @param string $paymentMethod 'Cash' or 'Transfer'
     * @param string $typeSpp 'Reguler' or 'Intensif'
     * @param string $month Month period for the payment (e.g. 'September')
     * @return Transaction
     * @throws Exception
     */
    public function recordPemasukanLangsung(
        string $date,
        string $studentName,
        $amount,
        string $paymentMethod,
        string $typeSpp,
        string $month
    ): Transaction {
        // 1. Resolve month postfix
        $monthKey = trim($month);
        if (!isset(self::MONTH_POSTFIXES[$monthKey])) {
            throw new Exception("Bulan periode tidak valid: {$month}. Harus salah satu dari September - April.");
        }
        $postfix = self::MONTH_POSTFIXES[$monthKey];

        // 2. Resolve Debit Account based on Payment Method
        // - 'Transfer' -> 1.10.02.01 (Dana)
        // - 'Cash' -> 1.10.01.01 (Kas Kecil H.O)
        $paymentMethodLower = strtolower(trim($paymentMethod));
        if ($paymentMethodLower === 'transfer') {
            $debitAccount = '1.10.02.01'; // Dana
        } else {
            $debitAccount = '1.10.01.01'; // Kas Kecil H.O
        }

        // 3. Resolve Credit Account based on SPP Type
        // - 'Reguler' -> 4.15.01.[postfix] (Pendapatan SPP Reguler)
        // - 'Intensif' -> 4.15.02.[postfix] (Pendapatan SPP Intensif)
        $typeSppLower = strtolower(trim($typeSpp));
        if ($typeSppLower === 'reguler' || $typeSppLower === 'spp_reguler') {
            $creditAccount = "4.15.01.{$postfix}";
            $referenceType = 'spp_reguler';
            $sppName = 'Reguler';
        } elseif ($typeSppLower === 'intensif' || $typeSppLower === 'spp_intensif') {
            $creditAccount = "4.15.02.{$postfix}";
            $referenceType = 'spp_intensif';
            $sppName = 'Intensif';
        } else {
            throw new Exception("Tipe SPP tidak valid: {$typeSpp}. Harus 'Reguler' atau 'Intensif'.");
        }

        // 4. Verify that target accounts exist in the CoA database
        $this->verifyAccountExists($debitAccount);
        $this->verifyAccountExists($creditAccount);

        // 5. Wrap in database transaction
        return DB::transaction(function () use ($date, $studentName, $amount, $debitAccount, $creditAccount, $referenceType, $sppName, $month) {
            // Create the primary transaction record
            $transaction = Transaction::create([
                'date' => $date,
                'description' => "Pemasukan SPP {$sppName} Bulan {$month} - {$studentName}",
                'reference_type' => $referenceType,
                'student_name' => $studentName,
                'period_month' => $month,
            ]);

            // Create Journal Entry (Debit)
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $debitAccount,
                'debit' => $amount,
                'credit' => 0.00,
            ]);

            // Create Journal Entry (Credit)
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $creditAccount,
                'debit' => 0.00,
                'credit' => $amount,
            ]);

            return $transaction;
        });
    }

    /**
     * Automate journal entries for operational school expenses (Pengeluaran).
     *
     * @param string $date Date of transaction (YYYY-MM-DD)
     * @param string $coaExpenseId Account ID for the expense (must belong to head 5.16)
     * @param float|string $amount Expense amount
     * @param string $paymentMethod 'Cash' or 'Transfer'
     * @param string $description Description of the expense
     * @return Transaction
     * @throws Exception
     */
    public function recordPengeluaran(
        string $date,
        string $coaExpenseId,
        $amount,
        string $paymentMethod,
        string $description
    ): Transaction {
        // 1. Validate expense account prefix (must be under head 5.16)
        if (strpos($coaExpenseId, '5.16.') !== 0) {
            throw new Exception("Akun pengeluaran harus memiliki prefix '5.16.' (Beban Usaha).");
        }

        // 2. Resolve Credit Account based on Payment Method
        // - 'Transfer' -> 1.10.02.01 (Dana)
        // - 'Cash' -> 1.10.01.01 (Kas Kecil H.O)
        $paymentMethodLower = strtolower(trim($paymentMethod));
        if ($paymentMethodLower === 'transfer') {
            $creditAccount = '1.10.02.01'; // Dana
        } else {
            $creditAccount = '1.10.01.01'; // Kas Kecil H.O
        }

        // 3. Verify that target accounts exist in the CoA database
        $this->verifyAccountExists($coaExpenseId);
        $this->verifyAccountExists($creditAccount);

        // 4. Wrap in database transaction
        return DB::transaction(function () use ($date, $coaExpenseId, $amount, $creditAccount, $description) {
            // Create the primary transaction record
            $transaction = Transaction::create([
                'date' => $date,
                'description' => $description,
                'reference_type' => 'pengeluaran',
                'student_name' => null,
                'period_month' => null,
            ]);

            // Create Journal Entry (Debit the expense account)
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $coaExpenseId,
                'debit' => $amount,
                'credit' => 0.00,
            ]);

            // Create Journal Entry (Credit the cash/bank account)
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $creditAccount,
                'debit' => 0.00,
                'credit' => $amount,
            ]);

            return $transaction;
        });
    }

    /**
     * Helper to verify if an account exists in the database.
     *
     * @param string $accountId
     * @throws Exception
     */
    protected function verifyAccountExists(string $accountId): void
    {
        if (!Account::where('id', $accountId)->exists()) {
            throw new Exception("Akun dengan kode '{$accountId}' tidak ditemukan di database. Jalankan seeder Chart of Accounts.");
        }
    }
}
