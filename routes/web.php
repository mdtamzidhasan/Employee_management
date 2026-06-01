<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Employee\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest.custom')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});



Route::post('logout', [AuthController::class, 'logout'])->middleware('auth.custom')->name('logout');

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::resource('employees', EmployeeController::class);
});


Route::middleware('auth.custom')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('employee.profile');
    }
    return redirect()->route('login');
});
