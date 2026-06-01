<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('employee');
        return view('employee.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        auth()->user()->employee()->updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );

        return back()->with('success', 'Profile updated successfully.');
    }
}