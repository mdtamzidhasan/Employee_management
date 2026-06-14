<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\SecurityLog;
use App\Models\User;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function __construct(protected SecurityLogger $logger) {}

    // ── Same logic as Web EmployeeController@index ────────
    public function index(Request $request)
    {
        $query = User::with('employee')
                     ->where('role', 'employee');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhereHas('employee', function ($q2) use ($request) {
                      $q2->where('phone', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('department')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $employees = $query->latest()->paginate(10);

        return EmployeeResource::collection($employees);
    }

    // ── Same logic as Web EmployeeController@store ────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'phone'        => ['required', 'string', 'max:20'],
            'position'     => ['required', 'string', 'max:50'],
            'department'   => ['required', 'string', 'max:50'],
            'salary'       => ['required', 'numeric'],
            'joining_date' => ['required', 'date'],
            'address'      => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'employee',
        ]);

        $user->employee()->create([
            'phone'        => $validated['phone'],
            'position'     => $validated['position'],
            'department'   => $validated['department'],
            'salary'       => $validated['salary'],
            'joining_date' => $validated['joining_date'],
            'address'      => $validated['address'] ?? null,
        ]);

        $this->logger->info(
            SecurityLog::EVENT_EMPLOYEE_CREATED,
            "New employee created by admin via API: {$user->email}",
            [
                'created_user_id'    => $user->id,
                'created_user_email' => $user->email,
                'admin_id'           => $request->user()->id,
                'admin_email'        => $request->user()->email,
            ]
        );

        return response()->json([
            'message' => 'Employee created successfully.',
            'user'    => new EmployeeResource($user->load('employee')),
        ], 201);
    }

    // ── Same logic as Web EmployeeController@show ─────────
    public function show(string $id)
    {
        $user = User::with('employee')->findOrFail($id);
        return new EmployeeResource($user);
    }

    // ── Same logic as Web EmployeeController@update ──────
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

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
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->employee()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone'        => $validated['phone'],
                'department'   => $validated['department'],
                'position'     => $validated['position'],
                'salary'       => $validated['salary'],
                'joining_date' => $validated['joining_date'],
                'address'      => $validated['address'],
                'status'       => $validated['status'],
            ]
        );

        $this->logger->info(
            SecurityLog::EVENT_EMPLOYEE_UPDATED,
            "Employee updated by admin via API: {$user->email}",
            [
                'updated_user_id' => $user->id,
                'admin_id'        => $request->user()->id,
                'admin_email'     => $request->user()->email,
            ]
        );

        return response()->json([
            'message' => 'Employee updated successfully.',
            'user'    => new EmployeeResource($user->load('employee')),
        ]);
    }

    // ── Same logic as Web EmployeeController@destroy ──────
    public function destroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $this->logger->critical(
            SecurityLog::EVENT_EMPLOYEE_DELETED,
            "Employee deleted by admin via API: {$user->email}",
            [
                'deleted_user_id'    => $user->id,
                'deleted_user_email' => $user->email,
                'admin_id'           => $request->user()->id,
                'admin_email'        => $request->user()->email,
            ]
        );

        $user->delete();

        return response()->json([
            'message' => 'Employee deleted successfully.',
        ]);
    }
}