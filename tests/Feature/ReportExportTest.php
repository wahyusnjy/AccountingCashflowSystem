<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('unauthenticated users cannot access report exports', function () {
    $this->get(route('export.gl.pdf'))->assertRedirect(route('login'));
    $this->get(route('export.tb.excel'))->assertRedirect(route('login'));
    $this->get(route('export.tb.pdf'))->assertRedirect(route('login'));
    $this->get(route('export.lr.excel'))->assertRedirect(route('login'));
    $this->get(route('export.lr.pdf'))->assertRedirect(route('login'));
    $this->get(route('export.lak.excel'))->assertRedirect(route('login'));
    $this->get(route('export.lak.pdf'))->assertRedirect(route('login'));
});

test('authenticated bendahara can export GL to PDF', function () {
    $user = User::where('email', 'bendahara1@fikra.com')->first();
    $this->actingAs($user);

    $response = $this->get(route('export.gl.pdf'));
    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});

test('authenticated bendahara can export TB to Excel and PDF', function () {
    $user = User::where('email', 'bendahara1@fikra.com')->first();
    $this->actingAs($user);

    // Excel export
    $excelResponse = $this->get(route('export.tb.excel'));
    $excelResponse->assertStatus(200);
    $excelResponse->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // PDF export
    $pdfResponse = $this->get(route('export.tb.pdf'));
    $pdfResponse->assertStatus(200);
    $pdfResponse->assertHeader('content-type', 'application/pdf');
});

test('authenticated bendahara can export Laba Rugi to Excel and PDF', function () {
    $user = User::where('email', 'bendahara1@fikra.com')->first();
    $this->actingAs($user);

    // Excel export
    $excelResponse = $this->get(route('export.lr.excel'));
    $excelResponse->assertStatus(200);
    $excelResponse->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // PDF export
    $pdfResponse = $this->get(route('export.lr.pdf'));
    $pdfResponse->assertStatus(200);
    $pdfResponse->assertHeader('content-type', 'application/pdf');
});

test('authenticated bendahara can export Arus Kas to Excel and PDF', function () {
    $user = User::where('email', 'bendahara1@fikra.com')->first();
    $this->actingAs($user);

    // Excel export
    $excelResponse = $this->get(route('export.lak.excel'));
    $excelResponse->assertStatus(200);
    $excelResponse->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // PDF export
    $pdfResponse = $this->get(route('export.lak.pdf'));
    $pdfResponse->assertStatus(200);
    $pdfResponse->assertHeader('content-type', 'application/pdf');
});

test('exports accept month and date filter parameters', function () {
    $user = User::where('email', 'bendahara1@fikra.com')->first();
    $this->actingAs($user);

    $params = [
        'period_month' => 'September',
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-30',
    ];

    $this->get(route('export.gl.pdf', $params))->assertStatus(200)->assertHeader('content-type', 'application/pdf');
    $this->get(route('export.tb.excel', $params))->assertStatus(200);
    $this->get(route('export.tb.pdf', $params))->assertStatus(200)->assertHeader('content-type', 'application/pdf');
    $this->get(route('export.lr.excel', $params))->assertStatus(200);
    $this->get(route('export.lr.pdf', $params))->assertStatus(200)->assertHeader('content-type', 'application/pdf');
    $this->get(route('export.lak.excel', $params))->assertStatus(200);
    $this->get(route('export.lak.pdf', $params))->assertStatus(200)->assertHeader('content-type', 'application/pdf');
});

test('authenticated bendahara can export salary slip (specific and blank) to PDF', function () {
    $user = User::where('email', 'bendahara1@fikra.com')->first();
    $this->actingAs($user);

    // 1. Export blank slip
    $blankResponse = $this->get(route('export.slip.gaji.blank'));
    $blankResponse->assertStatus(200);
    $blankResponse->assertHeader('content-type', 'application/pdf');

    // 2. Export specific slip
    // Find or create a teacher salary transaction
    $transaction = \App\Models\Transaction::whereHas('journalEntries', function ($q) {
        $q->where('account_id', '5.16.01.01');
    })->first();

    if (!$transaction) {
        $transaction = \App\Models\Transaction::create([
            'date' => now(),
            'description' => 'Gaji Pengajar Tentor Fisika',
            'reference_type' => 'pengeluaran',
        ]);
        $transaction->journalEntries()->create([
            'account_id' => '5.16.01.01',
            'debit' => 1500000,
            'credit' => 0,
        ]);
        $transaction->journalEntries()->create([
            'account_id' => '1.10.01.01',
            'debit' => 0,
            'credit' => 1500000,
        ]);
    }

    $response = $this->get(route('export.slip.gaji', $transaction->id));
    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});
