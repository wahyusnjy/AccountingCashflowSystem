<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Exception;

class AccountController extends Controller
{
    /**
     * Shared helper to retrieve ending cash wallet and bank account balances.
     */
    protected function getGlobalBalances()
    {
        $cashDebit = JournalEntry::where('account_id', '1.10.01.01')->sum('debit');
        $cashCredit = JournalEntry::where('account_id', '1.10.01.01')->sum('credit');
        $cashBalance = (float)($cashDebit - $cashCredit);

        $bankDebit = JournalEntry::where('account_id', '1.10.02.01')->sum('debit');
        $bankCredit = JournalEntry::where('account_id', '1.10.02.01')->sum('credit');
        $bankBalance = (float)($bankDebit - $bankCredit);

        return [$cashBalance, $bankBalance];
    }

    /**
     * Display a listing of Chart of Accounts (CoA).
     */
    public function index(Request $request)
    {
        list($globalCashBalance, $globalBankBalance) = $this->getGlobalBalances();

        $query = Account::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('id', 'like', '%' . $request->search . '%')
                  ->orWhere('account_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $accounts = $query->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('pages.accounts.index', compact(
            'globalCashBalance',
            'globalBankBalance',
            'accounts'
        ));
    }

    /**
     * Store a newly created account in Chart of Accounts.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:50|unique:accounts,id|regex:/^[0-9\.]+$/',
            'account_name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'required|in:debit,credit',
        ]);

        try {
            Account::create([
                'id' => trim($request->id),
                'account_name' => $request->account_name,
                'type' => $request->type,
                'normal_balance' => $request->normal_balance,
            ]);

            // Log activity
            \App\Services\ActivityLogger::log("Menambahkan akun CoA baru: " . trim($request->id) . " - {$request->account_name}");

            return back()->with('success', 'Akun master baru berhasil ditambahkan ke CoA.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan akun CoA: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified account in Chart of Accounts.
     */
    public function update(Request $request, Account $account)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'required|in:debit,credit',
        ]);

        try {
            $account->update([
                'account_name' => $request->account_name,
                'type' => $request->type,
                'normal_balance' => $request->normal_balance,
            ]);

            // Log activity
            \App\Services\ActivityLogger::log("Memperbarui akun CoA: {$account->id} (Nama: {$request->account_name}, Tipe: {$request->type})");

            return back()->with('success', 'Akun master CoA berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui akun CoA: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified account from Chart of Accounts.
     */
    public function destroy(Account $account)
    {
        try {
            // Bookkeeping Rule: Block deletion if this account has journal transaction logs!
            if ($account->journalEntries()->exists()) {
                return back()->with('error', 'Akun master tidak dapat dihapus karena telah digunakan dalam riwayat jurnal ledger keuangan.');
            }

            // Block deletion of critical Kas and Bank accounts
            if (in_array($account->id, ['1.10.01.01', '1.10.02.01'])) {
                return back()->with('error', 'Akun Kas Utama / Dana Bank tidak boleh dihapus karena merupakan pondasi sistem.');
            }

            $accountId = $account->id;
            $account->delete();

            // Log activity
            \App\Services\ActivityLogger::log("Menghapus akun CoA: {$accountId}");

            return back()->with('success', 'Akun master CoA berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus akun CoA: ' . $e->getMessage());
        }
    }
}
