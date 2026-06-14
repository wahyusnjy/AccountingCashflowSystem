<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GeneralJournalExport implements FromView, ShouldAutoSize
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
        $glQuery = Transaction::with(['journalEntries.account'])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc');

        if ($this->startDate && $this->endDate) {
            $glQuery->whereBetween('date', [$this->startDate, $this->endDate]);
        }

        if ($this->periodMonth) {
            $glQuery->where('period_month', $this->periodMonth);
        }

        $generalLedger = $glQuery->get();

        // Calculate sum of total debits and credits for the bottom row
        $totalDebitSum = 0;
        $totalCreditSum = 0;

        foreach ($generalLedger as $tx) {
            foreach ($tx->journalEntries as $entry) {
                $totalDebitSum += (float)$entry->debit;
                $totalCreditSum += (float)$entry->credit;
            }
        }

        return view('exports.excel_gl', [
            'generalLedger' => $generalLedger,
            'totalDebitSum' => $totalDebitSum,
            'totalCreditSum' => $totalCreditSum,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'periodMonth' => $this->periodMonth,
        ]);
    }
}
