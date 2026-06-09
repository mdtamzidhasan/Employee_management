<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\SecurityLog;
use App\Services\SecurityLogger;

class ProfileController extends Controller
{
    protected $logger;

    public function __construct(SecurityLogger $logger)
    {
        $this->logger = $logger;
    }

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
            'max:4096',
            'dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000',
        ],
    ], [
        'profile_photo.max'        => 'File size must be less than 4MB.',
        'profile_photo.dimensions' => 'Image must be between 100x100 and 5000x5000 pixels.',
    ]);

    $file = $request->file('profile_photo');
    $user = auth()->user()->load('employee');

    // Real Mime Type Check 
    $realMime     = $file->getMimeType();
    $allowedMimes = ['image/jpeg', 'image/png'];

    if (!in_array($realMime, $allowedMimes)) {
        $this->logger->critical(
            SecurityLog::EVENT_PHOTO_REJECTED,
            "Invalid mime type upload attempt by: {$user->email}",
            [
                'user_id'       => $user->id,
                'detected_mime' => $realMime,
                'filename'      => $file->getClientOriginalName(),
                'reason'        => 'invalid_mime_type',
            ]
        );
        return back()->withErrors([
            'profile_photo' => 'Invalid file type. Only JPG and PNG are allowed.'
        ]);
    }

    // Extension Check 
    $extension       = strtolower($file->getClientOriginalExtension());
    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (!in_array($extension, $allowedExtensions)) {
        $this->logger->critical(
            SecurityLog::EVENT_PHOTO_REJECTED,
            "Invalid file extension upload attempt by: {$user->email}",
            [
                'user_id'            => $user->id,
                'detected_extension' => $extension,
                'filename'           => $file->getClientOriginalName(),
                'reason'             => 'invalid_extension',
            ]
        );
        return back()->withErrors([
            'profile_photo' => 'JPG, JPEG, PNG files are allowed.'
        ]);
    }

    //Magic Bytes Check 
    $handle = fopen($file->getRealPath(), 'rb');
    $bytes  = fread($handle, 8);
    fclose($handle);

    $validSignatures = [
        "\xFF\xD8\xFF",
        "\x89PNG\r\n\x1a\n",
    ];

    $isValid = false;
    foreach ($validSignatures as $sig) {
        if (str_starts_with($bytes, $sig)) {
            $isValid = true;
            break;
        }
    }

    if (!$isValid) {
        $this->logger->critical(
            SecurityLog::EVENT_PHOTO_REJECTED,
            "Invalid magic bytes detected in upload by: {$user->email}",
            [
                'user_id'       => $user->id,
                'detected_mime' => $realMime,
                'filename'      => $file->getClientOriginalName(),
                'reason'        => 'magic_bytes_mismatch',
            ]
        );
        return back()->withErrors([
            'profile_photo' => 'Invalid image file. File signature does not match.'
        ]);
    }

    // PHP Image Content Check 
    $imageInfo = @getimagesize($file->getRealPath());
    if (!$imageInfo) {
        $this->logger->critical(
            SecurityLog::EVENT_PHOTO_REJECTED,
            "Invalid image content detected in upload by: {$user->email}",
            [
                'user_id'  => $user->id,
                'filename' => $file->getClientOriginalName(),
                'reason'   => 'invalid_image_content',
            ]
        );
        return back()->withErrors([
            'profile_photo' => 'File is not a valid image.'
        ]);
    }

    // Image Type Check 
    $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
    if (!in_array($imageInfo[2], $allowedImageTypes)) {
        $this->logger->critical(
            SecurityLog::EVENT_PHOTO_REJECTED,
            "Disallowed image type upload attempt by: {$user->email}",
            [
                'user_id'       => $user->id,
                'filename'      => $file->getClientOriginalName(),
                'detected_type' => $imageInfo[2],
                'reason'        => 'disallowed_image_type',
            ]
        );
        return back()->withErrors([
            'profile_photo' => 'JPG and PNG images are allowed.'
        ]);
    }

    // Image Bomb Check 
    $totalPixels = $imageInfo[0] * $imageInfo[1];
    $maxPixels   = 4000 * 4000;

    if ($totalPixels > $maxPixels) {
        $this->logger->critical(
            SecurityLog::EVENT_PHOTO_REJECTED,
            "Image bomb attempt detected from: {$user->email}",
            [
                'user_id'      => $user->id,
                'filename'     => $file->getClientOriginalName(),
                'width'        => $imageInfo[0],
                'height'       => $imageInfo[1],
                'total_pixels' => $totalPixels,
                'reason'       => 'image_bomb',
            ]
        );
        return back()->withErrors([
            'profile_photo' => 'Image resolution is too large. Maximum allowed is 4000x4000 pixels.'
        ]);
    }

    // photo delete 
    if ($user->employee->profile_photo) {
        $oldPath = str_replace('/storage/', 'public/', $user->employee->profile_photo);
        if (Storage::exists($oldPath)) {
            Storage::delete($oldPath);
        }
    }

    //photo save 
    $filename  = 'profile_' . $user->id . '_' . Str::random(16) . '.jpg';
    $directory = storage_path('app/public/profiles');

    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }

    $manager = new ImageManager(new Driver());
    $manager->read($file->getRealPath())
            ->cover(400, 400)
            ->save($directory . '/' . $filename);

    $photoUrl = '/storage/profiles/' . $filename;

    $user->employee()->update([
        'profile_photo' => $photoUrl,
    ]);

    $this->logger->info(
        SecurityLog::EVENT_PHOTO_UPLOADED,
        "Profile photo uploaded by: {$user->email}",
        [
            'user_id'  => $user->id,
            'filename' => $filename,
        ]
    );

    return back()->with('success', 'Profile photo updated successfully.');
}

    public function downloadPdf()
    {
        $user = auth()->user()->load('employee');
         $this->logger->info(
            SecurityLog::EVENT_PDF_DOWNLOADED,
            "Employee details PDF downloaded by: {$user->email}",
            [
                'user_id'   => $user->id,
                'user_name' => $user->name,
            ]
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('employee.details-pdf', compact('user'))
                  ->setPaper('a4', 'portrait');

        $filename = 'employee-details-' . str_replace(' ', '-', strtolower($user->name)) . '.pdf';

        return $pdf->stream($filename);
    }
}