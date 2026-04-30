<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\InviteeController;
use App\Http\Controllers\AdminManagerController;
use App\Http\Controllers\OwnerAdminController;
use App\Http\Controllers\ProfileController;
use App\Models\Invitee;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'owner'])->name('dashboard');

Route::middleware(['auth', 'verified', 'owner'])->group(function () {
    Route::get('/owner/manageadmins', [OwnerAdminController::class, 'index'])
        ->name('manageadmins');

    Route::post('/owner/manageadmins', [OwnerAdminController::class, 'store'])
        ->name('owner.admins.store');

    Route::get('/owner/user/{user}/edit', [OwnerAdminController::class, 'edit'])
        ->name('owner.admins.edit');

    Route::delete('/owner/user/{user}/delete', [OwnerAdminController::class, 'destroy'])
        ->name('owner.admins.destroy');

    Route::get('/owner/createusers', function () {
        return view('owner.createusers');
    })->name('createusers');
});

Route::get('/superadmin/dashboard', function () {
    return view('superadmin.dashboard');
})->middleware(['auth', 'verified'])->name('superadmin.dashboard');

Route::get('admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('admin.dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/manage/admin', [ProfileController::class, 'manageAdmin'])->middleware('admin')->name('admin.manage');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/manage/managers', [AdminManagerController::class, 'index'])->name('manage.managers');
    Route::post('/manage/managers', [AdminManagerController::class, 'store'])->name('manage.managers.store');
});



/*
|--------------------------------------------------------------------------
| Event Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
Route::get('event', [EventController::class, 'index'])->name('event.index');
Route::get('event/create', [EventController::class, 'create'])->name('event.create');
    Route::post('event/store', [EventController::class, 'store'])->name('event.store');
    Route::get('event/{id}', [EventController::class, 'show'])->name('event.show');
    Route::get('event/edit/{id}', [EventController::class, 'editEvent'])->name('event.edit');
    Route::post('event/destroy/{id}', [EventController::class, 'destroyEvent'])->name('event.destroy');
    Route::get('event/invitation/send', [EventController::class, 'sendInvitation'])->name('event.invitation.send');
    Route::get('event/invitation/send-details', [EventController::class, 'sendInvitationDetails'])->name('event.invitation.send-details');
    Route::get('event/invitation/card-upload', [EventController::class, 'eventCardUpload'])->name('event.invitation.card-upload');
    Route::get('event/invitation/verify', [EventController::class, 'verifyInvitation'])->name('event.invitation.verify');
    Route::get('event/invitee/current-invitation', [EventController::class, 'currentInvitation'])->name('event.invitee.current-invitation');
    
    // Card design routes
    Route::post('event/card-design/save', [EventController::class, 'saveCardDesign'])->name('design.create');

    //Invitees Show
    Route::get('/invitee/{code}', [InviteeController::class, 'show'])->name('invitee.show');

});


Route::middleware(['auth'])->group(function () {
    Route::get('event/invitees/create', [InviteeController::class, 'create'])->name('invitee.create');
    Route::post('/upload-invitees-excel', [InviteeController::class, 'uploadExcel'])->name('upload-invitees-excel');
    Route::post('/store-manual-invitees', [InviteeController::class, 'storeManual'])->name('store-manual-invitees');
    Route::post('/store-invitees', [InviteeController::class, 'store'])->name('store-invitees');
    Route::get('/download-template', [InviteeController::class, 'downloadTemplate'])->name('download-template');
    
});


require __DIR__.'/auth.php';
