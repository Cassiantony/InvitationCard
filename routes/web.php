<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminManagerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventViewerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InviteeController;
use App\Http\Controllers\OwnerAdminController;
use App\Http\Controllers\ProfileController;
use App\Models\Event;
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

Route::get('/home', HomeController::class)->middleware('auth')->name('home');

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
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('superadmin.dashboard');

Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/manage/admin', [ProfileController::class, 'manageAdmin'])->middleware('admin')->name('admin.manage');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'owner_or_admin'])->group(function () {
    Route::get('/manage/managers', [AdminManagerController::class, 'index'])->name('manage.managers');
    Route::post('/manage/managers', [AdminManagerController::class, 'store'])->name('manage.managers.store');
    Route::delete('/manage/managers/{user}', [AdminManagerController::class, 'destroy'])->name('manage.managers.destroy');
});


Route::get('/manager/dashboard', function () {
    $userId = auth()->id();
    $events = Event::where('user_id', $userId)->orderBy('date', 'desc')->get();
    $totalEvents = Event::where('user_id', $userId)->count();
    $upcomingEvents = Event::where('user_id', $userId)->where('date', '>', now())->count();
    $pastEvents = Event::where('user_id', $userId)->where('date', '<', now())->count();
    $todayEvents = Event::where('user_id', $userId)->whereDate('date', today())->count();

    return view('manager.dashboard', compact(
        'events',
        'totalEvents',
        'upcomingEvents',
        'pastEvents',
        'todayEvents'
    ));
})->name('manager.dashboard')->middleware(['auth', 'verified', 'manager']);

Route::get('/viewer/dashboard', function () {
    return view('viewer.dashboard');
})->middleware(['auth', 'verified', 'viewer'])->name('viewer.dashboard');




/*
|--------------------------------------------------------------------------
| Event Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'not_viewer'])->group(function () {
    Route::get('event', [EventController::class, 'index'])->name('event.index');
    Route::get('event/create', [EventController::class, 'create'])->name('event.create');
    Route::post('event/store', [EventController::class, 'store'])->name('event.store');
    Route::get('event/{id}', [EventController::class, 'show'])->name('event.show');
    Route::get('event/edit/{id}', [EventController::class, 'editEvent'])->name('event.edit');
    Route::delete('event/destroy/{id}', [EventController::class, 'destroyEvent'])->name('event.destroy');
    Route::get('event/invitation/send', [EventController::class, 'sendInvitation'])->name('event.invitation.send');
    Route::get('event/invitation/send-details', [EventController::class, 'sendInvitationDetails'])->name('event.invitation.send-details');
    Route::get('event/invitation/card-upload', [EventController::class, 'eventCardUpload'])->name('event.invitation.card-upload');
    Route::get('event/{event}/viewers', [EventViewerController::class, 'index'])->name('event.viewers.index');
    Route::post('event/{event}/viewers', [EventViewerController::class, 'store'])->name('event.viewers.store');
    Route::delete('event/{event}/viewers/{user}', [EventViewerController::class, 'destroy'])->name('event.viewers.destroy');
    Route::get('event/{event}/invitees.json', [EventController::class, 'inviteesJson'])->name('event.invitees.json');
    Route::get('event/{event}/invitation-card/{invitee}', [EventController::class, 'downloadInvitationCard'])->name('event.invitation-card.download');
    Route::get('event/invitee/current-invitation', [EventController::class, 'currentInvitation'])->name('event.invitee.current-invitation');

    Route::post('event/card-design/save', [EventController::class, 'saveCardDesign'])->name('design.create');

    Route::get('/invitee/{code}', [InviteeController::class, 'show'])->name('invitee.show');

    Route::get('event/invitees/create/{event}', [InviteeController::class, 'create'])->name('invitee.create');
    Route::post('/upload-invitees-excel', [InviteeController::class, 'uploadExcel'])->name('upload-invitees-excel');
    Route::post('/store-manual-invitees', [InviteeController::class, 'storeManual'])->name('store-manual-invitees');
    Route::post('/store-invitees', [InviteeController::class, 'store'])->name('store-invitees');
    Route::get('/download-template', [InviteeController::class, 'downloadTemplate'])->name('download-template');
});

Route::middleware('auth')->group(function () {
    Route::get('event/invitation/verify', [EventController::class, 'verifyInvitation'])->name('event.invitation.verify');
    Route::post('event/invitation/verify-lookup', [EventController::class, 'verifyScanLookup'])->name('event.invitation.verify-lookup');
    Route::post('event/invitation/check-in', [EventController::class, 'verifyScanCheckIn'])->name('event.invitation.check-in');
});
require __DIR__.'/auth.php';
