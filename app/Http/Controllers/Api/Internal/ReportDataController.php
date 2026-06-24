<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ReportDataController extends Controller
{
    // ── সব Department এর List দাও ───────────────────────────
    public function departments()
    {
        $departments = User::where('role', 'employee')
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->whereNotNull('employees.department')
            ->distinct()
            ->pluck('employees.department')
            ->values();

        return response()->json([
            'departments' => $departments,
        ]);
    }

    // ── Employee List দাও (department filter সহ/ছাড়া) ──────
    public function employees(Request $request)
    {
        $query = User::with('employee')
                     ->where('role', 'employee');

        if ($request->filled('department')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $employees = $query->get()->map(function ($user) {
            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'phone'        => $user->employee?->phone,
                'department'   => $user->employee?->department,
                'position'     => $user->employee?->position,
                'salary'       => $user->employee?->salary,
                'joining_date' => $user->employee?->joining_date?->format('Y-m-d'),
                'address'      => $user->employee?->address,
                'status'       => $user->employee?->status,
            ];
        });

        return response()->json([
            'employees' => $employees,
        ]);
    }

    // ── একজন নির্দিষ্ট Employee এর Full Detail দাও ──────────
    public function employeeDetail(string $id)
    {
        $user = User::with('employee')
                     ->where('role', 'employee')
                     ->find($id);

        if (!$user) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json([
            'employee' => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'phone'        => $user->employee?->phone,
                'department'   => $user->employee?->department,
                'position'     => $user->employee?->position,
                'salary'       => $user->employee?->salary,
                'joining_date' => $user->employee?->joining_date?->format('Y-m-d'),
                'address'      => $user->employee?->address,
                'status'       => $user->employee?->status,
            ],
        ]);
    }


    //all Employee/User List for Rbac 
public function users()
{
    $users = User::with('employee')
                  ->where('role', '!=', 'admin')
                  ->get()
                  ->map(function ($user) {
                      return [
                          'id'         => $user->id,
                          'name'       => $user->name,
                          'email'      => $user->email,
                          'role'       => $user->role,
                          'department' => $user->employee?->department,
                          'position'   => $user->employee?->position,
                          'status'     => $user->employee?->status,
                      ];
                  });

    return response()->json(['users' => $users]);
}

//  One selected User's details 
public function userDetail(string $id)
{
    $user = User::with('employee')->find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json([
        'user' => [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'department' => $user->employee?->department,
            'position'   => $user->employee?->position,
            'status'     => $user->employee?->status,
        ],
    ]);
}
}