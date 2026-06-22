<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\SecurityLog;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function __construct(protected SecurityLogger $logger) {}

    // Same logic as Web ProfileController@show 
    public function show(Request $request)
    {
        $user = $request->user()->load('employee');
        return new UserResource($user);
    }

    // Same logic as Web ProfileController@update 
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        $user->employee()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user'    => new UserResource($user->load('employee')),
        ]);
    }

    // ── Same logic as Web ProfileController@uploadPhoto ───
    public function uploadPhoto(Request $request)
    {
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
        $user = $request->user()->load('employee');

        // Layer 2: Real Mime Type Check
        $realMime     = $file->getMimeType();
        $allowedMimes = ['image/jpeg', 'image/png'];

        if (!in_array($realMime, $allowedMimes)) {
            $this->logger->critical(
                SecurityLog::EVENT_PHOTO_REJECTED,
                "Invalid mime type upload attempt via API by: {$user->email}",
                [
                    'user_id'       => $user->id,
                    'detected_mime' => $realMime,
                    'filename'      => $file->getClientOriginalName(),
                ]
            );
            return response()->json([
                'message' => 'Invalid file type. Only JPG and PNG are allowed.'
            ], 422);
        }

        // Layer 3: Extension Check
        $extension          = strtolower($file->getClientOriginalExtension());
        $allowedExtensions  = ['jpg', 'jpeg', 'png'];

        if (!in_array($extension, $allowedExtensions)) {
            $this->logger->critical(
                SecurityLog::EVENT_PHOTO_REJECTED,
                "Invalid file extension upload attempt via API by: {$user->email}",
                [
                    'user_id'            => $user->id,
                    'detected_extension' => $extension,
                    'filename'           => $file->getClientOriginalName(),
                ]
            );
            return response()->json([
                'message' => 'Only JPG, JPEG, PNG files are allowed.'
            ], 422);
        }

        // Layer 4: Magic Bytes Check
        $handle = fopen($file->getRealPath(), 'rb');
        $bytes  = fread($handle, 8);
        fclose($handle);

        $validSignatures = ["\xFF\xD8\xFF", "\x89PNG\r\n\x1a\n"];
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
                "Invalid magic bytes detected in API upload by: {$user->email}",
                ['user_id' => $user->id, 'filename' => $file->getClientOriginalName()]
            );
            return response()->json([
                'message' => 'Invalid image file. File signature does not match.'
            ], 422);
        }

        // Layer 5: PHP Image Content Check
        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo) {
            $this->logger->critical(
                SecurityLog::EVENT_PHOTO_REJECTED,
                "Invalid image content detected in API upload by: {$user->email}",
                ['user_id' => $user->id, 'filename' => $file->getClientOriginalName()]
            );
            return response()->json([
                'message' => 'File is not a valid image.'
            ], 422);
        }

        // Layer 6: Image Type Check
        $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imageInfo[2], $allowedImageTypes)) {
            $this->logger->critical(
                SecurityLog::EVENT_PHOTO_REJECTED,
                "Disallowed image type API upload attempt by: {$user->email}",
                ['user_id' => $user->id, 'detected_type' => $imageInfo[2]]
            );
            return response()->json([
                'message' => 'Only JPG and PNG images are allowed.'
            ], 422);
        }

        // Layer 7: Image Bomb Check
        $totalPixels = $imageInfo[0] * $imageInfo[1];
        $maxPixels   = 4000 * 4000;

        if ($totalPixels > $maxPixels) {
            $this->logger->critical(
                SecurityLog::EVENT_PHOTO_REJECTED,
                "Image bomb attempt detected via API from: {$user->email}",
                ['user_id' => $user->id, 'total_pixels' => $totalPixels]
            );
            return response()->json([
                'message' => 'Image resolution is too large. Maximum allowed is 4000x4000 pixels.'
            ], 422);
        }

        //  photo delete
        if ($user->employee->profile_photo) {
            $oldPath = str_replace('/storage/', 'public/', $user->employee->profile_photo);
            if (Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }
        }

        // নতুন photo save
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
            "Profile photo uploaded via API by: {$user->email}",
            ['user_id' => $user->id, 'filename' => $filename]
        );

        return response()->json([
            'message' => 'Profile photo updated successfully.',
            'user'    => new UserResource($user->fresh()->load('employee')),
        ]);
    }

    // ── Same logic as Web ProfileController@details ───────
    public function details(Request $request)
    {
        $user = $request->user()->load('employee');
        return new UserResource($user);
    }

    // ── Same logic as Web ProfileController@downloadPdf ───
    public function downloadPdf(Request $request)
    {
        $user = $request->user()->load('employee');

        $this->logger->info(
            SecurityLog::EVENT_PDF_DOWNLOADED,
            "Employee details PDF downloaded via API by: {$user->email}",
            ['user_id' => $user->id, 'user_name' => $user->name]
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('employee.details-pdf', compact('user'))
                  ->setPaper('a4', 'portrait');

        $filename = 'employee-details-' . str_replace(' ', '-', strtolower($user->name)) . '.pdf';

        return $pdf->download($filename);
    }
}