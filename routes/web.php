<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetLoanController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CashDashboardController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResidentController;
use App\Models\Announcement;
use App\Models\CashTransaction;
use App\Models\Event;
use App\Models\FeeBill;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $totalBalance = CashTransaction::where('status', 'approved')
        ->selectRaw('sum(case when transaction_type = "income" then amount else -amount end) as total')
        ->value('total') ?? 0;

    $currentMonthIncome = CashTransaction::where('status', 'approved')
        ->where('transaction_type', 'income')
        ->whereYear('transaction_date', now()->year)
        ->whereMonth('transaction_date', now()->month)
        ->sum('amount');

    $currentMonthExpense = CashTransaction::where('status', 'approved')
        ->where('transaction_type', 'expense')
        ->whereYear('transaction_date', now()->year)
        ->whereMonth('transaction_date', now()->month)
        ->sum('amount');

    $transactions = CashTransaction::with(['cashAccount', 'category'])
        ->where('status', 'approved')
        ->latest('transaction_date')
        ->take(5)
        ->get();

    $announcements = Announcement::where(function ($q) {
        $q->whereNull('status')->orWhere('status', 'published');
    })
        ->latest('published_at')
        ->take(3)
        ->get();

    $agendas = Event::where('starts_at', '>=', now())
        ->orderBy('starts_at', 'asc')
        ->take(3)
        ->get();

    return view('welcome', compact('totalBalance', 'currentMonthIncome', 'currentMonthExpense', 'transactions', 'announcements', 'agendas'));
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('resident') && ! $user->hasRole('super-admin') && ! $user->hasRole('rw-admin') && ! $user->hasRole('rt-admin')) {
        $resident = $user->resident;

        $unpaidBillsCount = 0;
        $activeLettersCount = 0;

        if ($resident) {
            // Count bills that are unpaid
            $unpaidBillsCount = FeeBill::where('resident_id', $resident->id)
                ->where('status', 'unpaid')
                ->count();

            // Count letters that are active (submitted/in progress, not completed or rejected)
            $activeLettersCount = LetterRequest::where('resident_id', $resident->id)
                ->whereNotIn('status', ['completed', 'rejected'])
                ->count();
        }

        // Active published announcements
        $latestAnnouncementsCount = Announcement::where(function ($q) {
            $q->whereNull('status')->orWhere('status', 'published');
        })->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
        })->count();

        return view('dashboards.resident', compact('resident', 'unpaidBillsCount', 'activeLettersCount', 'latestAnnouncementsCount'));
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Regions
    Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
    Route::post('/regions/rt', [RegionController::class, 'storeRt'])->name('regions.rt.store');

    // Accounts
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/{user}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('/accounts/{user}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{user}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::get('/accounts/{user}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('/accounts/{user}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{user}', [AccountController::class, 'destroy'])->name('accounts.destroy');

    // Residents & Families
    Route::resource('residents', ResidentController::class);
    Route::get('/api/families/search', [ResidentController::class, 'searchFamily'])->name('families.search');
    Route::post('/families', [ResidentController::class, 'storeFamily'])->name('families.store');
    Route::put('/families/{family}', [ResidentController::class, 'updateFamily'])->name('families.update');
    Route::delete('/families/{family}', [ResidentController::class, 'destroyFamily'])->name('families.destroy');

    // Fees & Payments
    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::post('/fees/types', [FeeController::class, 'storeType'])->name('fees.types.store');
    Route::post('/fees/bills/generate', [FeeController::class, 'generateBills'])->name('fees.bills.generate');
    Route::post('/fees/pay', [FeeController::class, 'pay'])->name('fees.pay');
    Route::get('/fees/verifications', [FeeController::class, 'verifications'])->name('fees.verifications');
    Route::post('/fees/verifications/{payment}', [FeeController::class, 'verifyPayment'])->name('fees.verify');

    // Letters
    Route::get('/letters', [LetterController::class, 'index'])->name('letters.index');
    Route::get('/letters/create', [LetterController::class, 'create'])->name('letters.create');
    Route::post('/letters', [LetterController::class, 'store'])->name('letters.store');
    Route::post('/letters/types', [LetterController::class, 'storeType'])->name('letters.types.store');
    Route::put('/letters/types/{letterType}', [LetterController::class, 'updateType'])->name('letters.types.update');
    Route::patch('/letters/types/{letterType}/toggle', [LetterController::class, 'toggleType'])->name('letters.types.toggle');
    Route::get('/letters/{letter}', [LetterController::class, 'show'])->name('letters.show');
    Route::get('/letters/{letter}/download', [LetterController::class, 'download'])->name('letters.download');
    Route::post('/letters/{letter}/approve', [LetterController::class, 'approve'])->name('letters.approve');

    // Cash & Transactions
    Route::get('/cash-dashboard', [CashDashboardController::class, 'index'])->name('cash_dashboard');
    Route::get('/cash-transactions', [CashTransactionController::class, 'index'])->name('cash_transactions.index');
    Route::post('/cash-transactions', [CashTransactionController::class, 'store'])->name('cash_transactions.store');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');

    // Inventory Control Module
    Route::prefix('admin/inventaris')->name('inventories.')->group(function () {
        Route::get('/', [InventoryController::class, 'dashboard'])->name('dashboard');
        Route::get('/barang', [InventoryController::class, 'index'])->name('index');
        Route::get('/barang/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/barang', [InventoryController::class, 'store'])->name('store');
        Route::post('/kategori', [InventoryController::class, 'storeCategory'])->name('categories.store');
        Route::get('/barang/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/barang/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/barang/{inventory}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/barang/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');

        Route::get('/peminjaman', [InventoryController::class, 'loansIndex'])->name('loans');
        Route::post('/peminjaman', [InventoryController::class, 'loansStore'])->name('loans.store');
        Route::patch('/peminjaman/{loan}/status', [InventoryController::class, 'loanUpdateStatus'])->name('loans.status');

        Route::get('/pengembalian', [InventoryController::class, 'returnsIndex'])->name('returns');
        Route::post('/pengembalian/{loan}', [InventoryController::class, 'returnsStore'])->name('returns.store');

        Route::get('/riwayat', [InventoryController::class, 'historiesIndex'])->name('histories');
        Route::get('/laporan', [InventoryController::class, 'reportsIndex'])->name('reports');
        Route::get('/laporan/excel', [InventoryController::class, 'exportExcel'])->name('reports.excel');
    });
    Route::resource('announcements', AnnouncementController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('events', EventController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::post('/events/{event}/cancel', [EventController::class, 'cancelRegistration'])->name('events.cancel');
    Route::post('/events/{event}/session', [EventController::class, 'startSession'])->name('events.session');
    Route::post('/events/{event}/complete', [EventController::class, 'complete'])->name('events.complete');

    // Asset Management
    Route::resource('assets', AssetController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('asset_loans', AssetLoanController::class)->only(['index', 'create', 'store']);
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Reports & Recaps
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
});

require __DIR__.'/auth.php';
