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
    // Basic Validation
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

    //  Real Mime Type Check 
    $realMime     = $file->getMimeType();
    $allowedMimes = ['image/jpeg', 'image/png'];

    if (!in_array($realMime, $allowedMimes)) {
        return back()->withErrors([
            'profile_photo' => 'Invalid file type. Only JPG and PNG are allowed.'
        ]);
    }

    // Magic Bytes Check
    $handle = fopen($file->getRealPath(), 'rb');
    $bytes  = fread($handle, 8);
    fclose($handle);

    $validSignatures = [
        "\xFF\xD8\xFF",        // JPEG magic bytes
        "\x89PNG\r\n\x1a\n",  // PNG magic bytes
    ];

    $isValid = false;
    foreach ($validSignatures as $sig) {
        if (str_starts_with($bytes, $sig)) {
            $isValid = true;
            break;
        }
    }

    if (!$isValid) {
        return back()->withErrors([
            'profile_photo' => 'Invalid image file. File signature does not match.'
        ]);
    }

    // PHP Image Content Check 
    $imageInfo = @getimagesize($file->getRealPath());
    if (!$imageInfo) {
        return back()->withErrors([
            'profile_photo' => 'File is not a valid image.'
        ]);
    }

    // Image Type Check 
    $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
    if (!in_array($imageInfo[2], $allowedImageTypes)) {
        return back()->withErrors([
            'profile_photo' => 'Only JPG and PNG images are allowed.'
        ]);
    }

    //Image Bomb Check 
    // Pixel count check — decompression attack check
    $totalPixels = $imageInfo[0] * $imageInfo[1];
    $maxPixels   = 4000 * 4000; 

    if ($totalPixels > $maxPixels) {
        return back()->withErrors([
            'profile_photo' => 'Image resolution is too large. Maximum allowed is 4000x4000 pixels.'
        ]);
    }

    //delete old photo if exists
    if ($user->employee->profile_photo) {
        $oldPath = str_replace('/storage/', 'public/', $user->employee->profile_photo);
        if (Storage::exists($oldPath)) {
            Storage::delete($oldPath);
        }
    }

    // photo save with resize  
    // Random filename — path traversal attack check
    $filename  = 'profile_' . $user->id . '_' . Str::random(16) . '.jpg';
    $directory = storage_path('app/public/profiles');

    // If there is a no directory, create new directory
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }

    // 400x400 square crop — EXIF/metadata automatically strip 
    $manager = new ImageManager(new Driver());
    $manager->read($file->getRealPath())
            ->cover(400, 400)
            ->save($directory . '/' . $filename);

    // Public URL create
    $photoUrl = '/storage/profiles/' . $filename;

    // Database update 
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