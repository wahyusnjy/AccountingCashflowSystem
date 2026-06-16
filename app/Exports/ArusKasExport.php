<?php

namespace App\Exports;

use App\Models\JournalEntry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ArusKasExport implements FromView, ShouldAutoSize
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
        // 1. Fetch cash entries
        $cashQuery = JournalEntry::where('account_id', '1.10.01.01')
            ->with(['transaction'])
            ->orderBy('created_at', 'desc');

        // 2. Fetch bank entries
        $bankQuery = JournalEntry::where('account_id', '1.10.02.01')
            ->with(['transaction'])
            ->orderBy('created_at', 'desc');

        if ($this->startDate && $this->endDate) {
            $cashQuery->whereHas('transaction', fn($q) => $q->whereBetween('date', [$this->startDate, $this->endDate]));
            $bankQuery->whereHas('transaction', fn($q) => $q->whereBetween('date', [$this->startDate, $this->endDate]));
        }

        if ($this->periodMonth) {
            $cashQuery->whereHas('transaction', fn($q) => $q->where('period_month', $this->periodMonth));
            $bankQuery->whereHas('transaction', fn($q) => $q->where('period_month', $this->periodMonth));
        }

        $cashLedgerEntries = $cashQuery->get();
        $bankLedgerEntries = $bankQuery->get();

        // 3. Calculate ending balances (as of the end date if filtered)
        $cashDebitQuery = JournalEntry::where('account_id', '1.10.01.01');
        $cashCreditQuery = JournalEntry::where('account_id', '1.10.01.01');
        $bankDebitQuery = JournalEntry::where('account_id', '1.10.02.01');
        $bankCreditQuery = JournalEntry::where('account_id', '1.10.02.01');

        if ($this->endDate) {
            $cashDebitQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $this->endDate));
            $cashCreditQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $this->endDate));
            $bankDebitQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $this->endDate));
            $bankCreditQuery->whereHas('transaction', fn($q) => $q->where('date', '<=', $this->endDate));
        }

        $globalCashBalance = (float)($cashDebitQuery->sum('debit') - $cashCreditQuery->sum('credit'));
        $globalBankBalance = (float)($bankDebitQuery->sum('debit') - $bankCreditQuery->sum('credit'));

        return view('exports.excel_lak', [
            'cashLedgerEntries' => $cashLedgerEntries,
            'bankLedgerEntries' => $bankLedgerEntries,
            'globalCashBalance' => $globalCashBalance,
            'globalBankBalance' => $globalBankBalance,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'periodMonth' => $this->periodMonth,
        ]);
    }
}
