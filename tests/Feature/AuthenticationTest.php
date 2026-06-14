<?php

use App\Models\User;
use App\Models\AuthLog;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('unauthenticated users are redirected to login', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');

    $response = $this->get('/pemasukan');
    $response->assertRedirect('/login');

    $response = $this->get('/pengeluaran');
    $response->assertRedirect('/login');
});

test('authenticated bendahara1 can access all routes', function () {
    $bendahara = User::where('email', 'bendahara1@fikra.com')->first();
    expect($bendahara)->not->toBeNull();

    $this->actingAs($bendahara);

    // Access all pages
    $this->get('/dashboard')->assertStatus(200);
    $this->get('/pemasukan')->assertStatus(200);
    $this->get('/pengeluaran')->assertStatus(200);
    $this->get('/accounting')->assertStatus(200);
    $this->get('/students')->assertStatus(200);
    $this->get('/accounts')->assertStatus(200);
    $this->get('/activity-logs')->assertStatus(200);
});

test('authenticated bendahara2 can access all routes', function () {
    $bendahara = User::where('email', 'bendahara2@fikra.com')->first();
    expect($bendahara)->not->toBeNull();

    $this->actingAs($bendahara);

    // Access all pages
    $this->get('/dashboard')->assertStatus(200);
    $this->get('/pemasukan')->assertStatus(200);
    $this->get('/pengeluaran')->assertStatus(200);
    $this->get('/accounting')->assertStatus(200);
    $this->get('/students')->assertStatus(200);
    $this->get('/accounts')->assertStatus(200);
    $this->get('/activity-logs')->assertStatus(200);
});

test('login controller registers session activity and parses device info', function () {
    // Perform Login
    $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    
    $response = $this->withHeaders([
        'User-Agent' => $userAgent,
    ])->post('/login', [
        'email' => 'bendahara1@fikra.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticated();

    $user = User::where('email', 'bendahara1@fikra.com')->first();

    // Verify Auth Log entry
    $authLog = AuthLog::where('user_id', $user->id)->first();
    expect($authLog)->not->toBeNull();
    expect($authLog->device)->toBe('Desktop');
    expect($authLog->platform)->toBe('macOS');
    expect($authLog->browser)->toBe('Chrome');
    expect($authLog->logout_at)->toBeNull();

    // Verify activity logs created
    $activityLog = ActivityLog::where('user_id', $user->id)
        ->where('activity', 'like', '%Login berhasil%')
        ->first();
    expect($activityLog)->not->toBeNull();
    expect($activityLog->activity)->toContain('Login berhasil menggunakan Chrome di macOS (Desktop)');

    // Perform Logout
    $logoutResponse = $this->post('/logout');
    $logoutResponse->assertRedirect('/login');
    $this->assertGuest();

    // Verify logout time updated
    $authLog->refresh();
    expect($authLog->logout_at)->not->toBeNull();
});

test('user agent parser correctly identifies devices', function () {
    $chromeMac = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    $safariIphone = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';
    
    $parsedMac = ActivityLogger::parseUserAgent($chromeMac);
    expect($parsedMac['browser'])->toBe('Chrome');
    expect($parsedMac['platform'])->toBe('macOS');
    expect($parsedMac['device'])->toBe('Desktop');

    $parsedIphone = ActivityLogger::parseUserAgent($safariIphone);
    expect($parsedIphone['browser'])->toBe('Safari');
    expect($parsedIphone['platform'])->toBe('iOS (iPhone)');
    expect($parsedIphone['device'])->toBe('Mobile');
});

test('forgot password flow and password reset workflow is fully functional', function () {
    $user = User::where('email', 'bendahara1@fikra.com')->first();

    // 1. Request password reset link
    $response = $this->post('/forgot-password', [
        'email' => 'bendahara1@fikra.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');
    $response->assertSessionHas('simulated_link');

    $simulatedLink = session('simulated_link');
    expect($simulatedLink)->toContain('/reset-password/');

    // Extract token from simulated link
    preg_match('/\/reset-password\/([a-zA-Z0-9]+)/', $simulatedLink, $matches);
    expect($matches)->toHaveCount(2);
    $token = $matches[1];

    // Verify token exists in database
    $dbRecord = DB::table('password_reset_tokens')->where('email', 'bendahara1@fikra.com')->first();
    expect($dbRecord)->not->toBeNull();
    expect(Hash::check($token, $dbRecord->token))->toBeTrue();

    // 2. Open reset page
    $resetPageResponse = $this->get("/reset-password/{$token}?email=" . urlencode('bendahara1@fikra.com'));
    $resetPageResponse->assertStatus(200);
    $resetPageResponse->assertViewIs('auth.reset-password');
    $resetPageResponse->assertViewHas('token', $token);
    $resetPageResponse->assertViewHas('email', 'bendahara1@fikra.com');

    // 3. Submit password reset form
    $submitResetResponse = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'bendahara1@fikra.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $submitResetResponse->assertRedirect('/login');
    $submitResetResponse->assertSessionHas('success');

    // Verify token deleted
    $dbRecordDeleted = DB::table('password_reset_tokens')->where('email', 'bendahara1@fikra.com')->first();
    expect($dbRecordDeleted)->toBeNull();

    // Verify password updated and can login with new credentials
    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();

    // Verify new login attempt works
    $loginResponse = $this->post('/login', [
        'email' => 'bendahara1@fikra.com',
        'password' => 'newpassword123',
    ]);
    $loginResponse->assertRedirect('/');
    $this->assertAuthenticated();
});
