<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Exception;

class StudentController extends Controller
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
     * Display a listing of Fikra Academy students.
     */
    public function index(Request $request)
    {
        list($globalCashBalance, $globalBankBalance) = $this->getGlobalBalances();

        $query = Student::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        $students = $query->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('pages.students.index', compact(
            'globalCashBalance',
            'globalBankBalance',
            'students'
        ));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:students,name',
            'status' => 'required|in:aktif,non_aktif',
            'phone' => 'nullable|string|max:20',
            'program' => 'required|in:Reguler,Intensif',
        ]);

        try {
            Student::create([
                'name' => $request->name,
                'status' => $request->status,
                'phone' => $request->phone,
                'program' => $request->program,
            ]);

            // Log activity
            \App\Services\ActivityLogger::log("Menambahkan siswa baru: {$request->name} (Program: {$request->program})");

            return back()->with('success', 'Data siswa berhasil ditambahkan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan data siswa: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:students,name,' . $student->id,
            'status' => 'required|in:aktif,non_aktif',
            'phone' => 'nullable|string|max:20',
            'program' => 'required|in:Reguler,Intensif',
        ]);

        try {
            $student->update([
                'name' => $request->name,
                'status' => $request->status,
                'phone' => $request->phone,
                'program' => $request->program,
            ]);

            // Log activity
            \App\Services\ActivityLogger::log("Memperbarui data siswa: {$request->name} (Program: {$request->program}, Status: {$request->status})");

            return back()->with('success', 'Data siswa berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data siswa: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        try {
            $studentName = $student->name;
            $student->delete();

            // Log activity
            \App\Services\ActivityLogger::log("Menghapus data siswa: {$studentName}");

            return back()->with('success', 'Data siswa berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus data siswa: ' . $e->getMessage());
        }
    }
}
