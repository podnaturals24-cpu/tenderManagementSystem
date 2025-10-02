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

// Route::get('/dashboard', function () {
//     $userId = auth()->id();

//     $applications = Application::with('tender')
//         ->where('user_id', $userId)
//         ->get();

//     // tenders created by the user
//     $myTenders = Tender::where('created_by', $userId)
//         ->orderBy('created_at', 'desc')
//         ->get();
    

//     // determine admin status - adjust check if you use a different field (is_admin, type, etc.)
//   $user = auth()->user();

// // $role = $user->role ?? null;

// // dd($user->role === 'admin');

//     $isAdmin = $user->role === 'admin';

    

//     if ($isAdmin) {
//         // admin sees everything
//         $tenders = Tender::orderBy('created_at', 'desc')->get();
//         //dd($tenders);
//     } else {
//         // build list of tender ids from applications (safe: returns a Collection)
//         $tenderIds = $applications->pluck('tender_id')->unique()->filter()->values();

//         $tenders = Tender::where('user_id', $userId)
//         ->orderBy('created_at', 'desc')
//         ->get();
//         //dd($tenderIds);

//         if ($tenderIds->isEmpty()) {
//             // OPTION A (recommended): show all tenders except Disapproved for normal users
//             $tenders = Tender::where('user_id', $userId)
//             ->orderBy('created_at', 'desc')
//             ->get();
           

//             // OPTION B (alternative): show no tenders when user hasn't applied to any:
//             // $tenders = collect();
//         } else {
//             // show only tenders the user applied for AND not disapproved
//             $tenders = Tender::whereIn('id', $tenderIds)
//                 ->where('status', '!=', 'Disapproved')
//                 ->orderBy('created_at', 'desc')
//                 ->get();

            
//         }
//     }

//     return view('dashboard', compact('applications', 'myTenders', 'tenders'));
// })->middleware(['auth', 'verified'])->name('dashboard');









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

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
