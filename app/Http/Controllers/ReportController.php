<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Exception;

class ReportController extends Controller
{
    /**
     * Shared helper to retrieve ending cash wallet and bank account balances.
     */
    protected function getGlobalBalances()
    {
        // Kas Kecil H.O: 1.10.01.01 (Asset: Debit - Credit)
        $cashDebit = JournalEntry::where('account_id', '1.10.01.01')->sum('debit');
        $cashCredit = JournalEntry::where('account_id', '1.10.01.01')->sum('credit');
        $cashBalance = (float)($cashDebit - $cashCredit);

        // Dana di Bank: 1.10.02.01 (Asset: Debit - Credit)
        $bankDebit = JournalEntry::where('account_id', '1.10.02.01')->sum('debit');
        $bankCredit = JournalEntry::where('account_id', '1.10.02.01')->sum('credit');
        $bankBalance = (float)($bankDebit - $bankCredit);

        return [$cashBalance, $bankBalance];
    }

    /**
     * Display the Main Dashboard showing Fikra Academy overview.
     */
    public function dashboard()
    {
        list($globalCashBalance, $globalBankBalance) = $this->getGlobalBalances();

        // 1. Calculate key metrics
        $accounts = Account::with(['journalEntries'])->get();
        $trialBalance = $accounts->map(function ($account) {
            $totalDebit = $account->journalEntries->sum('debit');
            $totalCredit = $account->journalEntries->sum('credit');
            if ($account->normal_balance === 'debit') {
                $endingBalance = $totalDebit - $totalCredit;
            } else {
                $endingBalance = $totalCredit - $totalDebit;
            }
            return [
                'type' => $account->type,
                'ending_balance' => (float)$endingBalance,
            ];
        });

        $totalRevenue = $trialBalance->filter(fn($acc) => $acc['type'] === 'revenue')->sum('ending_balance');
        $totalExpense = $trialBalance->filter(fn($acc) => $acc['type'] === 'expense')->sum('ending_balance');
        $netProfitOrLoss = $totalRevenue - $totalExpense;

        // Count active unique students from master data
        $activeStudents = \App\Models\Student::where('status', 'aktif')->count();

        // Recent transactions (last 5 entries)
        $recentTransactions = Transaction::with(['journalEntries.account'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // 2. Prepare Monthly Cashflow Trend line-chart data (September - April)
        $chartLabels = ['Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr'];
        $months = ['September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April'];
        
        $chartInflows = [];
        $chartOutflows = [];

        $monthMap = [
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4
        ];

        foreach ($months as $month) {
            // Inflow: sum of credit of revenue accounts for this billing month
            $inflow = JournalEntry::whereHas('transaction', function ($q) use ($month) {
                $q->where('period_month', $month);
            })->whereHas('account', function ($q) {
                $q->where('type', 'revenue');
            })->sum('credit');

            // Outflow: sum of debit of expense accounts falling in this calendar month
            $monthNum = $monthMap[$month];
            $outflow = JournalEntry::whereHas('transaction', function ($q) use ($monthNum) {
                $q->whereMonth('date', $monthNum);
            })->whereHas('account', function ($q) {
                $q->where('type', 'expense');
            })->sum('debit');

            $chartInflows[] = (float)$inflow;
            $chartOutflows[] = (float)$outflow;
        }

        return view('pages.dashboard', compact(
            'globalCashBalance',
            'globalBankBalance',
            'totalRevenue',
            'totalExpense',
            'netProfitOrLoss',
            'activeStudents',
            'recentTransactions',
            'chartLabels',
            'chartInflows',
            'chartOutflows'
        ));
    }

    /**
     * Display Pemasukan SPP List & Form panel.
     */
    public function pemasukan(Request $request)
    {
        list($globalCashBalance, $globalBankBalance) = $this->getGlobalBalances();

        $query = Transaction::whereIn('reference_type', ['spp_reguler', 'spp_intensif']);

        if ($request->filled('period_month')) {
            $query->where('period_month', $request->period_month);
        }

        if ($request->filled('type_spp')) {
            $query->where('reference_type', strtolower($request->type_spp) === 'reguler' ? 'spp_reguler' : 'spp_intensif');
        }

        if ($request->filled('search')) {
            $query->where('student_name', 'like', '%' . $request->search . '%');
        }

        $pemasukanRecords = $query->with(['journalEntries.account'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $availableMonths = [
            'September', 'Oktober', 'November', 'Desember',
            'Januari', 'Februari', 'Maret', 'April'
        ];

        $activeStudentsList = \App\Models\Student::where('status', 'aktif')->orderBy('name', 'asc')->get();

        return view('pages.pemasukan', compact(
            'globalCashBalance',
            'globalBankBalance',
            'pemasukanRecords',
            'availableMonths',
            'activeStudentsList'
        ));
    }

    /**
     * Display Pengeluaran List & Form panel.
     */
    public function pengeluaran(Request $request)
    {
        list($globalCashBalance, $globalBankBalance) = $this->getGlobalBalances();

        $query = Transaction::where('reference_type', 'pengeluaran');

        if ($request->filled('coa_expense_id')) {
            $query->whereHas('journalEntries', function ($q) use ($request) {
                $q->where('account_id', $request->coa_expense_id)->where('debit', '>', 0);
            });
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $pengeluaranRecords = $query->with(['journalEntries.account'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $expenseAccountsList = Account::where('type', 'expense')->get();

        return view('pages.pengeluaran', compact(
            'globalCashBalance',
            'globalBankBalance',
            'pengeluaranRecords',
            'expenseAccountsList'
        ));
    }

    /**
     * Display Accounting Reports (GL, TB, LR, and Cash/Bank Reconciliation).
     */
    public function accounting(Request $request)
    {
        list($globalCashBalance, $globalBankBalance) = $this->getGlobalBalances();

        // 1. Fetch Jurnal Umum (GL)
        $glQuery = Transaction::with(['journalEntries.account'])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $glQuery->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('period_month')) {
            $glQuery->where('period_month', $request->period_month);
        }

        $generalLedger = $glQuery->get();

        // 2. Fetch Neraca Saldo (TB)
        $accounts = Account::with(['journalEntries'])->get();
        $trialBalance = $accounts->map(function ($account) {
            $totalDebit = $account->journalEntries->sum('debit');
            $totalCredit = $account->journalEntries->sum('credit');
            if ($account->normal_balance === 'debit') {
                $endingBalance = $totalDebit - $totalCredit;
            } else {
                $endingBalance = $totalCredit - $totalDebit;
            }
            return [
                'id' => $account->id,
                'account_name' => $account->account_name,
                'type' => $account->type,
                'normal_balance' => $account->normal_balance,
                'total_debit' => (float)$totalDebit,
                'total_credit' => (float)$totalCredit,
                'ending_balance' => (float)$endingBalance,
            ];
        });

        $tbTotalDebit = $trialBalance->sum('total_debit');
        $tbTotalCredit = $trialBalance->sum('total_credit');

        // 3. Fetch Laba Rugi (LR)
        $revenueAccounts = $trialBalance->filter(fn($acc) => $acc['type'] === 'revenue');
        $expenseAccounts = $trialBalance->filter(fn($acc) => $acc['type'] === 'expense');

        $totalRevenue = $revenueAccounts->sum('ending_balance');
        $totalExpense = $expenseAccounts->sum('ending_balance');
        $netProfitOrLoss = $totalRevenue - $totalExpense;

        // 4. Fetch Cash Ledger entries for reconciliation
        $cashLedgerEntries = JournalEntry::where('account_id', '1.10.01.01')
            ->with(['transaction'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 5. Fetch Bank Ledger entries for reconciliation
        $bankLedgerEntries = JournalEntry::where('account_id', '1.10.02.01')
            ->with(['transaction'])
            ->orderBy('created_at', 'desc')
            ->get();

        $availableMonths = [
            'September', 'Oktober', 'November', 'Desember',
            'Januari', 'Februari', 'Maret', 'April'
        ];

        return view('pages.accounting', compact(
            'globalCashBalance',
            'globalBankBalance',
            'generalLedger',
            'trialBalance',
            'tbTotalDebit',
            'tbTotalCredit',
            'revenueAccounts',
            'expenseAccounts',
            'totalRevenue',
            'totalExpense',
            'netProfitOrLoss',
            'cashLedgerEntries',
            'bankLedgerEntries',
            'availableMonths'
        ));
    }

    /**
     * Submit simulated student payment income (Pemasukan).
     */
    public function storePemasukan(Request $request, AccountingService $accountingService)
    {
        $request->validate([
            'date' => 'required|date',
            'student_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Transfer',
            'type_spp' => 'required|in:Reguler,Intensif',
            'period_month' => 'required|string',
        ]);

        try {
            $accountingService->recordPemasukanLangsung(
                $request->date,
                $request->student_name,
                $request->amount,
                $request->payment_method,
                $request->type_spp,
                $request->period_month
            );

            // Log activity
            \App\Services\ActivityLogger::log("Mencatat pemasukan SPP siswa {$request->student_name} sebesar Rp " . number_format($request->amount, 0, ',', '.') . " untuk bulan {$request->period_month} ({$request->type_spp})");

            return back()->with('success', 'Pemasukan SPP berhasil dicatat dan dijurnal otomatis.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal mencatat pemasukan: ' . $e->getMessage());
        }
    }

    /**
     * Submit simulated expense payment (Pengeluaran).
     */
    public function storePengeluaran(Request $request, AccountingService $accountingService)
    {
        $request->validate([
            'date' => 'required|date',
            'coa_expense_id' => 'required|string|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Transfer',
            'description' => 'required|string|max:1000',
        ]);

        try {
            $accountingService->recordPengeluaran(
                $request->date,
                $request->coa_expense_id,
                $request->amount,
                $request->payment_method,
                $request->description
            );

            // Log activity
            $acc = Account::find($request->coa_expense_id);
            $accName = $acc ? $acc->account_name : $request->coa_expense_id;
            \App\Services\ActivityLogger::log("Mencatat pengeluaran: {$request->description} ({$accName}) sebesar Rp " . number_format($request->amount, 0, ',', '.'));

            return back()->with('success', 'Pengeluaran operasional berhasil dicatat dan dijurnal otomatis.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal mencatat pengeluaran: ' . $e->getMessage());
        }
    }

    /**
     * Export General Ledger to Excel using Maatwebsite Excel.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periodMonth = $request->input('period_month');

        $filename = 'general_ledger_';
        if ($periodMonth) {
            $filename .= strtolower($periodMonth) . '_';
        }
        $filename .= date('Ymd_His') . '.xlsx';

        // Log activity
        \App\Services\ActivityLogger::log("Mengekspor laporan jurnal umum ke Excel");

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\GeneralJournalExport($startDate, $endDate, $periodMonth),
            $filename
        );
    }
}
