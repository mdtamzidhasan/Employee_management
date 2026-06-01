<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:20'],
            'position' => ['required', 'string', 'max:50'],
            'department' => ['required', 'string', 'max:50'],
            'salary' => ['required', 'numeric'],
            'joining_date' => ['required', 'date'],
            'address' => ['nullable', 'string'],
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
        $user->load('employee');
        return view('admin.employees.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, User $user)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone'        => ['nullable', 'string', 'max:20'],
            'department'   => ['nullable', 'string', 'max:100'],
            'position'     => ['nullable', 'string', 'max:100'],
            'salary'       => ['nullable', 'numeric', 'min:0'],
            'joining_date' => ['nullable', 'date'],
            'address'      => ['nullable', 'string'],
            'status'       => ['required', 'in:active,inactive'],
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
        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, User $user)
    {
        $user->delete();

        return redirect()->route('admin.employees.index')
        ->with('success', 'EM=mployee deleted successfully');
    }
}
