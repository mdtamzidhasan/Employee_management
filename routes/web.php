<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Employee\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PasswordConfigController;
use App\Http\Controllers\Admin\SecurityLogController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Employee\ObjectController;

Route::middleware('guest.custom')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');


    Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.verify');
    Route::post('/verify-otp', [OtpController::class, 'verify'])
         ->middleware('throttle:5,1')
         ->name('otp.verify.post');
    Route::post('/resend-otp', [OtpController::class, 'resend'])
         ->middleware('throttle:1,1')
         ->name('otp.resend');
});



Route::post('logout', [AuthController::class, 'logout'])->middleware('auth.custom')->name('logout');

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::resource('employees', EmployeeController::class);

    Route::get('/password-config', [PasswordConfigController::class, 'show'])->name('password.config');
    Route::put('/password-config', [PasswordConfigController::class, 'update'])->name('password.config.update');

    Route::get('/security-logs', [SecurityLogController::class, 'index'])->name('security.logs');

});


Route::middleware('auth.custom')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->middleware('throttle:3,1')->name('profile.photo');
    Route::get('/details', [ProfileController::class, 'details'])->name('details');
    Route::get('/details/download', [ProfileController::class, 'downloadPdf'])->name('details.download');


    // ── নতুন Object-based Routes ────────────────────────────
    Route::get('/object/{objectSlug}', [ObjectController::class, 'show'])->name('object.show');
    Route::get('/department/{dept}', [ObjectController::class, 'department'])->name('department');
    // Object operation AJAX route
Route::get('/employee/object/{objectSlug}/action', function (string $objectSlug) {
    $operation = request('operation', 'view');
    $user      = auth()->user()->load('employee');
    $userId    = $user->id;

    $rbac = app(\App\Services\RbacApiService::class);

    if ($user->isAdmin()) {
        $operations = ['view', 'create', 'edit', 'delete', 'export'];
        $objectMeta = ['object_type' => 'custom', 'department_name' => null];
        $objectName = ucfirst(str_replace('_', ' ', $objectSlug));
    } else {
        $rbacData   = $rbac->getUserRbacData($userId);
        $operations = [];
        $objectMeta = ['object_type' => 'custom', 'department_name' => null];
        $objectName = ucfirst(str_replace('_', ' ', $objectSlug));

        foreach ($rbacData['objects'] as $obj) {
            if ($obj['slug'] === $objectSlug) {
                $operations = $obj['operations'];
                $objectMeta = $obj;
                $objectName = $obj['name'];
                break;
            }
        }

        if (!in_array($operation, $operations)) {
            return response('Access denied.', 403);
        }
    }

    // ObjectController এর loadContentData logic এর মতো data বানাও
    $controller = app(\App\Http\Controllers\Employee\ObjectController::class);
    $contentData = (new \ReflectionClass($controller))
        ->getMethod('loadContentData')
        ->invoke($controller, $objectSlug, $objectMeta, $operations, $user);

    return view('employee.partials.object-content', array_merge(
        $contentData,
        compact('operation', 'objectSlug', 'operations')
    ));
})->name('object.action');

    // ── Salary Route ─────────────────────────────────────────
    Route::get('/salary', function () {
        $user = auth()->user()->load('employee');
        return view('employee.salary', compact('user'));
    })->name('salary');

    // ── Employee Manage Routes (HR Manager এর জন্য) ──────────
    Route::get('/manage', function () {
        $employees = \App\Models\User::with('employee')
                      ->where('role', 'employee')->get();
        return view('employee.manage.index', compact('employees'));
    })->name('manage.index');
});

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('employee.profile');
    }
    return redirect()->route('login');
});



