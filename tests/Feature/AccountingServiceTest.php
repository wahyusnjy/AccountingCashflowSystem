<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Run seeders to populate Chart of Accounts and Students master data
    $this->seed();

    // Authenticate as seeded user to bypass global auth restrictions in tests
    $user = \App\Models\User::first();
    if ($user) {
        $this->actingAs($user);
    }
});

test('AccountingService records Pemasukan Langsung with Cash method and Reguler SPP correctly', function () {
    $service = new AccountingService();

    // September -> postfix '01'
    // typeSpp Reguler -> credit account prefix '4.15.01.' -> final account '4.15.01.01'
    // paymentMethod Cash -> debit account '1.10.01.01'
    $date = '2026-06-14';
    $studentName = 'Alice';
    $amount = 550000;
    $paymentMethod = 'Cash';
    $typeSpp = 'Reguler';
    $month = 'September';

    $transaction = $service->recordPemasukanLangsung($date, $studentName, $amount, $paymentMethod, $typeSpp, $month);

    // Verify transaction record
    expect($transaction)->not->toBeNull();
    expect($transaction->student_name)->toBe('Alice');
    expect($transaction->reference_type)->toBe('spp_reguler');
    expect($transaction->period_month)->toBe('September');
    expect($transaction->description)->toContain('Pemasukan SPP Reguler Bulan September - Alice');

    // Verify journal entries
    $journalEntries = JournalEntry::where('transaction_id', $transaction->id)->get();
    expect($journalEntries)->toHaveCount(2);

    $debitEntry = $journalEntries->where('debit', '>', 0)->first();
    expect($debitEntry->account_id)->toBe('1.10.01.01'); // Kas Kecil H.O
    expect((float)$debitEntry->debit)->toBe(550000.00);
    expect((float)$debitEntry->credit)->toBe(0.00);

    $creditEntry = $journalEntries->where('credit', '>', 0)->first();
    expect($creditEntry->account_id)->toBe('4.15.01.01'); // Pendapatan SPP Reguler - September
    expect((float)$creditEntry->debit)->toBe(0.00);
    expect((float)$creditEntry->credit)->toBe(550000.00);
});

test('AccountingService records Pemasukan Langsung with Transfer method and Intensif SPP correctly', function () {
    $service = new AccountingService();

    // Oktober -> postfix '02'
    // typeSpp Intensif -> credit account prefix '4.15.02.' -> final account '4.15.02.02'
    // paymentMethod Transfer -> debit account '1.10.02.01'
    $date = '2026-06-14';
    $studentName = 'Bob';
    $amount = 750000;
    $paymentMethod = 'Transfer';
    $typeSpp = 'Intensif';
    $month = 'Oktober';

    $transaction = $service->recordPemasukanLangsung($date, $studentName, $amount, $paymentMethod, $typeSpp, $month);

    // Verify transaction record
    expect($transaction->reference_type)->toBe('spp_intensif');
    expect($transaction->period_month)->toBe('Oktober');

    // Verify journal entries
    $journalEntries = JournalEntry::where('transaction_id', $transaction->id)->get();
    expect($journalEntries)->toHaveCount(2);

    $debitEntry = $journalEntries->where('debit', '>', 0)->first();
    expect($debitEntry->account_id)->toBe('1.10.02.01'); // Dana
    expect((float)$debitEntry->debit)->toBe(750000.00);

    $creditEntry = $journalEntries->where('credit', '>', 0)->first();
    expect($creditEntry->account_id)->toBe('4.15.02.02'); // Pendapatan SPP Intensif - Oktober
    expect((float)$creditEntry->credit)->toBe(750000.00);
});

test('AccountingService throws exception for invalid months or SPP types', function () {
    $service = new AccountingService();

    // Invalid Month
    expect(fn () => $service->recordPemasukanLangsung('2026-06-14', 'Charlie', 500000, 'Cash', 'Reguler', 'Mei'))
        ->toThrow(Exception::class);

    // Invalid SPP Type
    expect(fn () => $service->recordPemasukanLangsung('2026-06-14', 'Charlie', 500000, 'Cash', 'Privat', 'September'))
        ->toThrow(Exception::class);
});

test('AccountingService records Pengeluaran with Cash and Transfer methods correctly', function () {
    $service = new AccountingService();

    // Listrik -> 5.16.01.02
    // paymentMethod Cash -> credit account '1.10.01.01'
    $date = '2026-06-14';
    $coaExpenseId = '5.16.01.02';
    $amount = 120000;
    $paymentMethod = 'Cash';
    $description = 'Bayar tagihan listrik kantor';

    $transaction = $service->recordPengeluaran($date, $coaExpenseId, $amount, $paymentMethod, $description);

    expect($transaction->reference_type)->toBe('pengeluaran');
    expect($transaction->student_name)->toBeNull();

    $journalEntries = JournalEntry::where('transaction_id', $transaction->id)->get();
    expect($journalEntries)->toHaveCount(2);

    $debitEntry = $journalEntries->where('debit', '>', 0)->first();
    expect($debitEntry->account_id)->toBe('5.16.01.02'); // Listrik
    expect((float)$debitEntry->debit)->toBe(120000.00);

    $creditEntry = $journalEntries->where('credit', '>', 0)->first();
    expect($creditEntry->account_id)->toBe('1.10.01.01'); // Kas Kecil H.O
    expect((float)$creditEntry->credit)->toBe(120000.00);
});

test('AccountingService throws exception if expense account does not belong to head 5.16', function () {
    $service = new AccountingService();

    // Attempt to debit Kas Kecil (1.10.01.01) as expense - should fail validation
    expect(fn () => $service->recordPengeluaran('2026-06-14', '1.10.01.01', 50000, 'Cash', 'Salah Akun'))
        ->toThrow(Exception::class);
});

test('ReportController dashboard loads correctly and displays metrics', function () {
    $service = new AccountingService();
    
    // Inject some transactions
    $service->recordPemasukanLangsung('2026-09-10', 'Student A', 600000, 'Transfer', 'Reguler', 'September');
    $service->recordPengeluaran('2026-09-15', '5.16.01.03', 150000, 'Cash', 'Internet VPS');

    // Visit dashboard
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertViewIs('pages.dashboard');
    $response->assertViewHas('totalRevenue');
    $response->assertViewHas('totalExpense');
    $response->assertViewHas('netProfitOrLoss');
    $response->assertViewHas('activeStudents');
    $response->assertViewHas('recentTransactions');

    // Total Revenue = 600000
    // Total Expense = 150000
    // Net Profit = 450000
    expect((float)$response->viewData('totalRevenue'))->toBe(600000.00);
    expect((float)$response->viewData('totalExpense'))->toBe(150000.00);
    expect((float)$response->viewData('netProfitOrLoss'))->toBe(450000.00);
    expect($response->viewData('activeStudents'))->toBe(5); // 5 seeded active students
});

test('ReportController pemasukan page loads and filters correctly', function () {
    $service = new AccountingService();
    $service->recordPemasukanLangsung('2026-09-10', 'Student A', 600000, 'Transfer', 'Reguler', 'September');

    $response = $this->get('/pemasukan');
    $response->assertStatus(200);
    $response->assertViewIs('pages.pemasukan');
    $response->assertViewHas('pemasukanRecords');
    expect($response->viewData('pemasukanRecords'))->toHaveCount(1);
});

test('ReportController pengeluaran page loads and filters correctly', function () {
    $service = new AccountingService();
    $service->recordPengeluaran('2026-09-15', '5.16.01.03', 150000, 'Cash', 'Internet VPS');

    $response = $this->get('/pengeluaran');
    $response->assertStatus(200);
    $response->assertViewIs('pages.pengeluaran');
    $response->assertViewHas('pengeluaranRecords');
    expect($response->viewData('pengeluaranRecords'))->toHaveCount(1);
});

test('ReportController accounting page loads and computes reports', function () {
    $service = new AccountingService();
    
    // Inject some transactions
    $service->recordPemasukanLangsung('2026-09-10', 'Student A', 600000, 'Transfer', 'Reguler', 'September');
    $service->recordPengeluaran('2026-09-15', '5.16.01.03', 150000, 'Cash', 'Internet VPS');

    $response = $this->get('/accounting');

    $response->assertStatus(200);
    $response->assertViewIs('pages.accounting');
    $response->assertViewHas('generalLedger');
    $response->assertViewHas('trialBalance');
    $response->assertViewHas('tbTotalDebit');
    $response->assertViewHas('tbTotalCredit');
    $response->assertViewHas('cashLedgerEntries');
    $response->assertViewHas('bankLedgerEntries');

    expect((float)$response->viewData('tbTotalDebit'))->toBe(750000.00); // 600000 (Bank) + 150000 (VPS Expense)
    expect((float)$response->viewData('tbTotalCredit'))->toBe(750000.00); // 600000 (Revenue) + 150000 (Cash)
});

test('Excel export route triggers a file download', function () {
    $response = $this->get('/export-excel');
    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
    expect($response->headers->get('content-disposition'))->toContain('attachment; filename=general_ledger_');
});

test('Student CRUD is fully functional', function () {
    // Index
    $response = $this->get('/students');
    $response->assertStatus(200);
    $response->assertViewIs('pages.students.index');
    $response->assertViewHas('students');

    // Store
    $response = $this->post('/students', [
        'name' => 'Fiona Gallagher',
        'status' => 'aktif',
        'phone' => '087766554433',
        'program' => 'Reguler',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('students', ['name' => 'Fiona Gallagher', 'program' => 'Reguler']);

    // Update
    $student = \App\Models\Student::where('name', 'Fiona Gallagher')->first();
    $response = $this->put("/students/{$student->id}", [
        'name' => 'Fiona Gallagher Refactored',
        'status' => 'non_aktif',
        'phone' => '087766554433',
        'program' => 'Intensif',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('students', ['name' => 'Fiona Gallagher Refactored', 'status' => 'non_aktif', 'program' => 'Intensif']);

    // Destroy
    $response = $this->delete("/students/{$student->id}");
    $response->assertRedirect();
    $this->assertSoftDeleted('students', ['id' => $student->id]);
});

test('Account CRUD is fully functional and protects ledger references', function () {
    // Index
    $response = $this->get('/accounts');
    $response->assertStatus(200);
    $response->assertViewIs('pages.accounts.index');
    $response->assertViewHas('accounts');

    // Store
    $response = $this->post('/accounts', [
        'id' => '1.12.01.01',
        'account_name' => 'Piutang Test',
        'type' => 'asset',
        'normal_balance' => 'debit',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('accounts', ['id' => '1.12.01.01', 'account_name' => 'Piutang Test']);

    // Update
    $response = $this->put("/accounts/1.12.01.01", [
        'account_name' => 'Piutang Test Edited',
        'type' => 'asset',
        'normal_balance' => 'debit',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('accounts', ['id' => '1.12.01.01', 'account_name' => 'Piutang Test Edited']);

    // Destroy of unreferenced account
    $response = $this->delete("/accounts/1.12.01.01");
    $response->assertRedirect();
    $this->assertSoftDeleted('accounts', ['id' => '1.12.01.01']);

    // Attempt to delete Kas Kecil (1.10.01.01) which is critical
    $response = $this->delete("/accounts/1.10.01.01");
    $response->assertRedirect();
    expect(session('error'))->toContain('tidak boleh dihapus');
    $this->assertDatabaseHas('accounts', ['id' => '1.10.01.01']);
});

test('Transaction deletion cascades soft delete to journal entries', function () {
    $service = new AccountingService();
    $transaction = $service->recordPemasukanLangsung('2026-09-10', 'Student A', 600000, 'Transfer', 'Reguler', 'September');

    // Verify initially present
    $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    $this->assertDatabaseHas('journal_entries', ['transaction_id' => $transaction->id]);

    // Delete transaction
    $transaction->delete();

    // Verify soft-deleted
    $this->assertSoftDeleted('transactions', ['id' => $transaction->id]);
    $this->assertSoftDeleted('journal_entries', ['transaction_id' => $transaction->id]);

    // Restore transaction
    $transaction->restore();

    // Verify restored
    $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'deleted_at' => null]);
    $this->assertDatabaseHas('journal_entries', ['transaction_id' => $transaction->id, 'deleted_at' => null]);
});
