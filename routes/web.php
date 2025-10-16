<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\Auth\RegisteredUserController; 
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Models\Application;
use App\Models\Tender;   


Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
| Simple admin panel routes. We keep them under "admin." route name prefix.
| Auth middleware is applied so only authenticated users can reach these.
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Admin dashboard route (named admin.dashboard)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

   // Admin tender details (show single tender)
Route::get('/tenders/{tender}', [\App\Http\Controllers\Admin\TenderController::class, 'show'])->name('tenders.show');


    // Optional: approve/disapprove endpoints (create controller below if you want these)
    Route::post('/tenders/{tender}/approve', [\App\Http\Controllers\Admin\TenderController::class, 'approve'])->name('tenders.approve');
    Route::post('/tenders/{tender}/disapprove', [\App\Http\Controllers\Admin\TenderController::class, 'disapprove'])->name('tenders.disapprove');
    Route::get('/tenders/{tender}/download', [TenderController::class, 'download'])->name('tenders.download');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

    // post admin "create user" action
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::get('/dashboard', [TenderController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/userdashboard', [TenderController::class, 'userDashboard'])
    ->middleware(['auth', 'verified'])
    ->name('user.dashboard');


// ✅ Tender routes (needed for create/store form in dashboard)
Route::middleware(['auth'])->group(function () {

    
    // Only Admins can access registration
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->middleware('can:register-users')
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('can:register-users');
    Route::get('/tenders/create', [TenderController::class, 'create'])->name('tenders.create');
    Route::post('/tenders', [TenderController::class, 'store'])->name('tenders.store');
    Route::get('/tenders', [TenderController::class, 'index'])->name('tenders.index');
    Route::get('/tenders/{tender}', [TenderController::class, 'show'])->name('tenders.show');
    Route::get('/tenders/{tender}/download', [TenderController::class, 'downloadDocument'])->name('tenders.download');
    Route::post('/tenders/{id}/update', [TenderController::class, 'updateTenderData'])->name('tenders.updateData');
    Route::post('/tenders/{id}/updateStage', [TenderController::class, 'updateTenderEmdData'])->name('tenders.updateDataStage');
    // ✅ Route for updating EMD-related details
    

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';