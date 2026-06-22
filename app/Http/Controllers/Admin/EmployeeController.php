<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\SecurityLog;
use App\Services\SecurityLogger;

class EmployeeController extends Controller
{
    protected $logger;

    public function __construct(SecurityLogger $logger)
    {
        $this->logger = $logger;
    }


    public function index(Request $request)
    {
        $query = User::with('employee')
               ->where('role', 'employee');


        if($request->filled('search')) {
            $query->where(function($q)use($request) {
                $q->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('email', 'like', '%' . request('search') . '%')
                  ->orWhereHas('employee', function($q2) {
                      $q2->where('phone', 'like', '%' . request('search') . '%');
                  });
            });
        }

        if($request->filled('department')){
            $query->whereHas('employee', function($q)use($request) {
                $q->where('department', $request->department);
            });
        }

        $employees = $query->paginate(10);
        $departments = Employee::distinct()->pluck('department')->filter();

        return view('admin.employees.index', compact('employees', 'departments'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'min:2', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'
                        //'email:rfc,dns'
                        ],
            'password' => ['required', 'string', 'min:8'],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                 Rule::unique('employees', 'phone')->ignore($user->employee->id),
                'regex:/^(?:\+880|880|0)?1[3-9]\d{8}$/',
            ],
            'position' => ['required', 'string', 'max:50'],
            'department' => ['required', 'string', 'max:50'],
            'salary' => ['required', 'numeric'],
            'joining_date' => ['required', 'date', 'after_or_equal:2000-01-01', // company শুরুর তারিখ
                                'before_or_equal:today',],
            'address' => ['nullable', 'string', 'max:300', 'min:5'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
        ]);

        $user->employee()->create([
            'phone' => $validated['phone'] ?? null,
            'position' => $validated['position'] ?? null,
            'department' => $validated['department'] ?? null,
            'salary' => $validated['salary'] ?? null,
            'joining_date' => $validated['joining_date'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        $this->logger->info(
            SecurityLog::EVENT_EMPLOYEE_CREATED,
            "New employee created by admin: {$user->email}",
            [
                'created_user_id'    => $user->id,
                'created_user_name'  => $user->name,
                'created_user_email' => $user->email,
                'admin_id'           => auth()->id(),
                'admin_email'        => auth()->user()->email,
            ]
        );

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('employee')->findOrFail($id);
        return view('admin.employees.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('employee')->findOrFail($id);
        return view('admin.employees.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::with('employee')->findOrFail($id);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100', 'min:2', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)
                            //'email:rfc,dns'
                            ],
            'password'  => ['nullable', 'string', 'min:8'],
            'phone'     => [
                            'nullable',
                            'string',
                            'max:20',
                            Rule::unique('employees', 'phone')->ignore($user->employee->id),
                            'regex:/^(?:\+880|880|0)?1[3-9]\d{8}$/',
            ],
            'position'  => ['required', 'string', 'max:50'],
            'department' => ['required', 'string', 'max:50'],
            'salary'    => ['required', 'numeric'],
            'joining_date' => ['required', 'date', 'after_or_equal:2000-01-01', // company শুরুর তারিখ
                                'before_or_equal:today',],
            'address'    => ['nullable', 'string', 'max:300', 'min:5'],
            'status'     => ['required', 'in:active,inactive'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->employee()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $validated['phone'],
                'department' => $validated['department'],
                'position' => $validated['position'],
                'salary' => $validated['salary'],
                'joining_date' => $validated['joining_date'],
                'address' => $validated['address'],
                'status' => $validated['status'],
            ]
        );
        $this->logger->info(
            SecurityLog::EVENT_EMPLOYEE_UPDATED,
            "Employee updated by admin: {$user->email}",
            [
                'updated_user_id'    => $user->id,
                'updated_user_name'  => $user->name,
                'updated_user_email' => $user->email,
                'admin_id'           => auth()->id(),
                'admin_email'        => auth()->user()->email,
            ]
        );  


        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::with('employee')->findOrFail($id);
        $user->delete();
         $this->logger->critical(
        SecurityLog::EVENT_EMPLOYEE_DELETED,
        "Employee deleted by admin: {$user->email}",
        [
            'deleted_user_id'    => $user->id,
            'deleted_user_name'  => $user->name,
            'deleted_user_email' => $user->email,
            'admin_id'           => auth()->id(),
            'admin_email'        => auth()->user()->email,
        ]
    );

        return redirect()->route('admin.employees.index')
        ->with('success', 'Employee deleted successfully');
    }
}
