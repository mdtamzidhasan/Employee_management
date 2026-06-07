<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('employee');
        return view('employee.profile', compact('user'));
    }

    public function details()
    {
        $user = auth()->user()->load('employee');
        return view('employee.details', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        $user->employee()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return back()->with('success', 'Profile updated successfully.');
    }

    public function uploadPhoto(Request $request)
    {
        // ── Security Validation ────────────────────────────
        $request->validate([
            'profile_photo' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png',
                'max:4096',
                'dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000',
            ],
        ], [
            'profile_photo.image'      => 'File must be an image.',
            'profile_photo.mimes'      => 'Only JPG, JPEG, PNG files are allowed.',
            'profile_photo.max'        => 'File size must be less than 4MB.',
            'profile_photo.dimensions' => 'Image must be between 100x100 and 5000x5000 pixels.',
        ]);

        $file = $request->file('profile_photo');
        $user = auth()->user()->load('employee');

        // ── Extra Security Checks ──────────────────────────

        // ১. Real mime type check
        $realMime     = $file->getMimeType();
        $allowedMimes = ['image/jpeg', 'image/png'];

        if (!in_array($realMime, $allowedMimes)) {
            return back()->withErrors([
                'profile_photo' => 'Invalid file type. Only JPG and PNG are allowed.'
            ]);
        }

        // ২. File content check
        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo) {
            return back()->withErrors([
                'profile_photo' => 'File is not a valid image.'
            ]);
        }

        // ৩. Image type check
        $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imageInfo[2], $allowedImageTypes)) {
            return back()->withErrors([
                'profile_photo' => 'Only JPG and PNG images are allowed.'
            ]);
        }

        // ── পুরানো photo delete করো ───────────────────────
        if ($user->employee->profile_photo) {
            $oldPath = str_replace('/storage/', 'public/', $user->employee->profile_photo);
            if (Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }
        }

        // ── নতুন photo resize করে save করো ───────────────
        $filename  = 'profile_' . $user->id . '_' . Str::random(16) . '.jpg';
        $directory = storage_path('app/public/profiles');

        // Directory না থাকলে তৈরি করো
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // 400x400 square crop করে save করো
        $manager = new ImageManager(new Driver());
        $manager->read($file->getRealPath())
                ->cover(400, 400)
                ->save($directory . '/' . $filename);

        // Public URL তৈরি করো
        $photoUrl = '/storage/profiles/' . $filename;

        // Database update করো
        $user->employee()->update([
            'profile_photo' => $photoUrl,
        ]);

        return back()->with('success', 'Profile photo updated successfully.');
    }

    public function downloadPdf()
    {
        $user = auth()->user()->load('employee');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('employee.details-pdf', compact('user'))
                  ->setPaper('a4', 'portrait');

        $filename = 'employee-details-' . str_replace(' ', '-', strtolower($user->name)) . '.pdf';

        return $pdf->stream($filename);
    }
}