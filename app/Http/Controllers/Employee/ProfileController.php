<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('employee');
        return view('employee.profile', compact('user'));
    }

    public function update(Request $request)
    {        
        $user = auth()->user();
        $validated = $request->validate([
            'phone' => [
                'nullable',
                'string',
                'max:20',
                 Rule::unique('employees', 'phone')->ignore($user->employee->id),
                'regex:/^(?:\+880|880|0)?1[3-9]\d{8}$/',
            ],
            'address' => ['nullable', 'string', 'min:10', 'max:300'],
        ]);

        $user->employee()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return back()->with('success', 'Profile updated successfully.');
    }


    public function details()
    {
         $user = auth()->user()->load('employee');
         return view('employee.details', compact('user'));
    }
}