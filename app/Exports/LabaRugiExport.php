<?php

namespace App\Exports;

use App\Models\Account;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LabaRugiExport implements FromView, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $periodMonth;

    /**
     * Create a new export instance with filters.
     */
    public function __construct($startDate = null, $endDate = null, $periodMonth = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->periodMonth = $periodMonth;
    }

    /**
     * Return the View to be parsed into the Excel spreadsheet.
     */
    public function view(): View
    {
        $accounts = Account::with(['journalEntries' => function ($query) {
            $query->whereHas('transaction', function ($q) {
                if ($this->startDate && $this->endDate) {
                    $q->whereBetween('date', [$this->startDate, $this->endDate]);
                }
                if ($this->periodMonth) {
                    $q->where('period_month', $this->periodMonth);
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

        return view('exports.excel_lr', [
            'revenueAccounts' => $revenueAccounts,
            'expenseAccounts' => $expenseAccounts,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netProfitOrLoss' => $netProfitOrLoss,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'periodMonth' => $this->periodMonth,
        ]);
    }
}
