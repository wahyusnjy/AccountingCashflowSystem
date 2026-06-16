<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Models\Student;
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
    /**
     * Display Accounting Reports (GL, TB, LR, and Cash/Bank Reconciliation) with filters.
     */
    public function accounting(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periodMonth = $request->input('period_month');
        $student = $request->input('student');

        // 1. Fetch Jurnal Umum (GL)
        $glQuery = Transaction::with(['journalEntries.account'])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc');

        if ($startDate && $endDate) {
            $glQuery->whereBetween('date', [$startDate, $endDate]);
        }

        if ($periodMonth) {
            $glQuery->where('period_month', $periodMonth);
        }

        if ($student) {
            $glQuery->where('student_name', $student);
        }

        $generalLedger = $glQuery->get();

        // 2. Fetch Neraca Saldo (TB)
        $accounts = Account::with(['journalEntries' => function ($query) use ($startDate, $endDate, $periodMonth) {
            $query->whereHas('transaction', function ($q) use ($startDate, $endDate, $periodMonth) {
                if ($startDate && $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
                if ($periodMonth) {
                    $q->where('period_month', $periodMonth);
                }
            });
        }])->get();

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
        $cashQuery = JournalEntry::where('account_id', '1.10.01.01')
            ->with(['transaction'])
            ->orderBy('created_at', 'desc');

        // 5. Fetch Bank Ledger entries for reconciliation
        $bankQuery = JournalEntry::where('account_id', '1.10.02.01')
            ->with(['transaction'])
            ->orderBy('created_at', 'desc');

        if ($startDate && $endDate) {
            $cashQuery->whereHas('transaction', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));
            $bankQuery->whereHas('transaction', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));
        }

        if ($periodMonth) {
            $cashQuery->whereHas('transaction', fn($q) => $q->where('period_month', $periodMonth));
            $bankQuery->whereHas('transaction', fn($q) => $q->where('period_month', $periodMonth));
        }

        $cashLedgerEntries = $cashQuery->get();
        $bankLedgerEntries = $bankQuery->get();

        // 6. Global Balances
        $cashDebitQuery = JournalEntry::where('account_id', '1.10.01.01');
        $cashCreditQuery = JournalEntry::where('account_id', '1.10.01.01');
        $bankDebitQuery = JournalEntry::where('account_id', '1.10.02.01');
        $bankCreditQuery = JournalEntry::where('account_id', '1.10.02.01');

        if ($endDate) {
            $cashDebitQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $endDate));
            $cashCreditQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $endDate));
            $bankDebitQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $endDate));
            $bankCreditQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $endDate));
        }

        $globalCashBalance = (float)($cashDebitQuery->sum('debit') - $cashCreditQuery->sum('credit'));
        $globalBankBalance = (float)($bankDebitQuery->sum('debit') - $bankCreditQuery->sum('credit'));
        
        $students = Student::get('name');

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
            'availableMonths',
            'students'
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

    /**
     * Export General Ledger to PDF using Barryvdh DomPDF.
     */
    public function exportGLPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periodMonth = $request->input('period_month');
        $student = $request->input('student');

        $glQuery = Transaction::with(['journalEntries.account'])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc');

        if ($startDate && $endDate) {
            $glQuery->whereBetween('date', [$startDate, $endDate]);
        }

        if ($periodMonth) {
            $glQuery->where('period_month', $periodMonth);
        }

        if ($student) {
            $glQuery->where('student_name', $student);
        }

        $generalLedger = $glQuery->get();

        $totalDebitSum = 0;
        $totalCreditSum = 0;

        foreach ($generalLedger as $tx) {
            foreach ($tx->journalEntries as $entry) {
                $totalDebitSum += (float)$entry->debit;
                $totalCreditSum += (float)$entry->credit;
            }
        }

        $filename = 'general_ledger_';
        if ($periodMonth) {
            $filename .= strtolower($periodMonth) . '_';
        }
        $filename .= date('Ymd_His') . '.pdf';

        \App\Services\ActivityLogger::log("Mengekspor laporan jurnal umum ke PDF");

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf_gl', [
            'generalLedger' => $generalLedger,
            'totalDebitSum' => $totalDebitSum,
            'totalCreditSum' => $totalCreditSum,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodMonth' => $periodMonth,
        ]);

        return $pdf->download($filename);
    }

    /**
     * Export Trial Balance to Excel using Maatwebsite Excel.
     */
    public function exportTBExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periodMonth = $request->input('period_month');

        $filename = 'trial_balance_';
        if ($periodMonth) {
            $filename .= strtolower($periodMonth) . '_';
        }
        $filename .= date('Ymd_His') . '.xlsx';

        \App\Services\ActivityLogger::log("Mengekspor laporan neraca saldo ke Excel");

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TrialBalanceExport($startDate, $endDate, $periodMonth),
            $filename
        );
    }

    /**
     * Export Trial Balance to PDF using Barryvdh DomPDF.
     */
    public function exportTBPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periodMonth = $request->input('period_month');

        $accounts = Account::with(['journalEntries' => function ($query) use ($startDate, $endDate, $periodMonth) {
            $query->whereHas('transaction', function ($q) use ($startDate, $endDate, $periodMonth) {
                if ($startDate && $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
                if ($periodMonth) {
                    $q->where('period_month', $periodMonth);
                }
            });
        }])->get();

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

        $filename = 'trial_balance_';
        if ($periodMonth) {
            $filename .= strtolower($periodMonth) . '_';
        }
        $filename .= date('Ymd_His') . '.pdf';

        \App\Services\ActivityLogger::log("Mengekspor laporan neraca saldo ke PDF");

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf_tb', [
            'trialBalance' => $trialBalance,
            'tbTotalDebit' => $tbTotalDebit,
            'tbTotalCredit' => $tbTotalCredit,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodMonth' => $periodMonth,
        ]);

        return $pdf->download($filename);
    }

    /**
     * Export Laba Rugi to Excel using Maatwebsite Excel.
     */
    public function exportLRExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periodMonth = $request->input('period_month');

        $filename = 'laba_rugi_';
        if ($periodMonth) {
            $filename .= strtolower($periodMonth) . '_';
        }
        $filename .= date('Ymd_His') . '.xlsx';

        \App\Services\ActivityLogger::log("Mengekspor laporan laba rugi ke Excel");

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LabaRugiExport($startDate, $endDate, $periodMonth),
            $filename
        );
    }

    /**
     * Export Laba Rugi to PDF using Barryvdh DomPDF.
     */
    public function exportLRPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periodMonth = $request->input('period_month');

        $accounts = Account::with(['journalEntries' => function ($query) use ($startDate, $endDate, $periodMonth) {
            $query->whereHas('transaction', function ($q) use ($startDate, $endDate, $periodMonth) {
                if ($startDate && $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }
                if ($periodMonth) {
                    $q->where('period_month', $periodMonth);
                }
            });
        }])->get();

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

        $revenueAccounts = $trialBalance->filter(fn($acc) => $acc['type'] === 'revenue');
        $expenseAccounts = $trialBalance->filter(fn($acc) => $acc['type'] === 'expense');

        $totalRevenue = $revenueAccounts->sum('ending_balance');
        $totalExpense = $expenseAccounts->sum('ending_balance');
        $netProfitOrLoss = $totalRevenue - $totalExpense;

        $filename = 'laba_rugi_';
        if ($periodMonth) {
            $filename .= strtolower($periodMonth) . '_';
        }
        $filename .= date('Ymd_His') . '.pdf';

        \App\Services\ActivityLogger::log("Mengekspor laporan laba rugi ke PDF");

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf_lr', [
            'revenueAccounts' => $revenueAccounts,
            'expenseAccounts' => $expenseAccounts,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netProfitOrLoss' => $netProfitOrLoss,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodMonth' => $periodMonth,
        ]);

        return $pdf->download($filename);
    }

    /**
     * Export Laporan Arus Kas to Excel using Maatwebsite Excel.
     */
    public function exportLAKExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periodMonth = $request->input('period_month');

        $filename = 'arus_kas_';
        if ($periodMonth) {
            $filename .= strtolower($periodMonth) . '_';
        }
        $filename .= date('Ymd_His') . '.xlsx';

        \App\Services\ActivityLogger::log("Mengekspor laporan arus kas ke Excel");

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ArusKasExport($startDate, $endDate, $periodMonth),
            $filename
        );
    }

    /**
     * Export Laporan Arus Kas to PDF using Barryvdh DomPDF.
     */
    public function exportLAKPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $periodMonth = $request->input('period_month');

        $cashQuery = JournalEntry::where('account_id', '1.10.01.01')
            ->with(['transaction'])
            ->orderBy('created_at', 'desc');

        $bankQuery = JournalEntry::where('account_id', '1.10.02.01')
            ->with(['transaction'])
            ->orderBy('created_at', 'desc');

        if ($startDate && $endDate) {
            $cashQuery->whereHas('transaction', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));
            $bankQuery->whereHas('transaction', fn($q) => $q->whereBetween('date', [$startDate, $endDate]));
        }

        if ($periodMonth) {
            $cashQuery->whereHas('transaction', fn($q) => $q->where('period_month', $periodMonth));
            $bankQuery->whereHas('transaction', fn($q) => $q->where('period_month', $periodMonth));
        }

        $cashLedgerEntries = $cashQuery->get();
        $bankLedgerEntries = $bankQuery->get();

        $cashDebitQuery = JournalEntry::where('account_id', '1.10.01.01');
        $cashCreditQuery = JournalEntry::where('account_id', '1.10.01.01');
        $bankDebitQuery = JournalEntry::where('account_id', '1.10.02.01');
        $bankCreditQuery = JournalEntry::where('account_id', '1.10.02.01');

        if ($endDate) {
            $cashDebitQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $endDate));
            $cashCreditQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $endDate));
            $bankDebitQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $endDate));
            $bankCreditQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $endDate));
        }

        $globalCashBalance = (float)($cashDebitQuery->sum('debit') - $cashCreditQuery->sum('credit'));
        $globalBankBalance = (float)($bankDebitQuery->sum('debit') - $bankCreditQuery->sum('credit'));

        $filename = 'arus_kas_';
        if ($periodMonth) {
            $filename .= strtolower($periodMonth) . '_';
        }
        $filename .= date('Ymd_His') . '.pdf';

        \App\Services\ActivityLogger::log("Mengekspor laporan arus kas ke PDF");

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf_lak', [
            'cashLedgerEntries' => $cashLedgerEntries,
            'bankLedgerEntries' => $bankLedgerEntries,
            'globalCashBalance' => $globalCashBalance,
            'globalBankBalance' => $globalBankBalance,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodMonth' => $periodMonth,
        ]);

        return $pdf->download($filename);
    }

    /**
     * Export Salary Slip for a specific Teacher Salary transaction to PDF.
     */
    public function exportSlipGaji(Transaction $transaction)
    {
        $debitEntry = $transaction->journalEntries->where('debit', '>', 0)->first();
        $creditEntry = $transaction->journalEntries->where('credit', '>', 0)->first();
        
        $amount = $debitEntry ? (float)$debitEntry->debit : 0.0;
        $date = $transaction->date->format('d/m/Y');
        $ref_no = 'SLIP-' . str_pad($transaction->id, 5, '0', STR_PAD_LEFT);
        $description = $transaction->description;
        $payment_method = ($creditEntry && $creditEntry->account_id === '1.10.02.01') ? 'Transfer Bank' : 'Cash Kecil';
        $terbilang = self::terbilang($amount);

        \App\Services\ActivityLogger::log("Mengekspor Slip Gaji Pengajar (Ref: {$ref_no}) ke PDF");

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf_slip_gaji', [
            'ref_no' => $ref_no,
            'date' => $date,
            'amount' => $amount,
            'description' => $description,
            'payment_method' => $payment_method,
            'terbilang' => $terbilang,
        ]);

        return $pdf->download("slip_gaji_{$transaction->id}.pdf");
    }

    /**
     * Export a blank Salary Slip to PDF.
     */
    public function exportSlipGajiBlank()
    {
        \App\Services\ActivityLogger::log("Mengekspor Slip Gaji Pengajar Kosong ke PDF");

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.pdf_slip_gaji', [
            'ref_no' => '........................',
            'date' => '........................',
            'amount' => 0.0,
            'description' => 'Pembayaran Honorarium / Gaji Pengajar Bulan: ........................',
            'payment_method' => 'Cash / Transfer',
            'terbilang' => '....................................................................................................',
        ]);

        return $pdf->download("slip_gaji_kosong.pdf");
    }

    /**
     * Helper method to convert numbers to words in Indonesian.
     */
    public static function terbilang($angka) {
        $angka = abs($angka);
        $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $terbilang = "";
        
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = self::terbilang($angka - 10) . " Belas";
        } else if ($angka < 100) {
            $terbilang = self::terbilang(floor($angka / 10)) . " Puluh" . self::terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " Seratus" . self::terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = self::terbilang(floor($angka / 100)) . " Ratus" . self::terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " Seribu" . self::terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = self::terbilang(floor($angka / 1000)) . " Ribu" . self::terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = self::terbilang(floor($angka / 1000000)) . " Juta" . self::terbilang($angka % 1000000);
        } else if ($angka < 1000000000000) {
            $terbilang = self::terbilang(floor($angka / 1000000000)) . " Milyar" . self::terbilang(fmod($angka, 1000000000));
        }
        
        return trim($terbilang);
    }
}
