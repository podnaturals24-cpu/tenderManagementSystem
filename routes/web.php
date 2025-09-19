<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenderController;
use Illuminate\Support\Facades\Route;
use App\Models\Application;
use App\Models\Tender;   


Route::get('/', function () {
    return view('welcome');
});

// ✅ Dashboard (user’s applications)
Route::get('/dashboard', function () {
    $applications = Application::with('tender')
        ->where('user_id', auth()->id())
        ->get();

    // ✅ define tenders so compact() won’t break
    $tenders = Tender::orderBy('created_at', 'desc')->get();

    return view('dashboard', compact('applications', 'tenders'));
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ Tender routes (needed for create/store form in dashboard)
Route::middleware(['auth'])->group(function () {
    Route::get('/tenders/create', [TenderController::class, 'create'])->name('tenders.create');
    Route::post('/tenders', [TenderController::class, 'store'])->name('tenders.store');
    Route::get('/tenders', [TenderController::class, 'index'])->name('tenders.index');
    Route::get('/tenders/{tender}', [TenderController::class, 'show'])->name('tenders.show');
    Route::get('/tenders/{tender}/download', [TenderController::class, 'downloadDocument'])->name('tenders.download');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
