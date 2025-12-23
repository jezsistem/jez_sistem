<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\File;

class PhotoController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'img_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Check if the request has a file
        if ($request->hasFile('img_logo')) {
            $user = Auth::user();
            $userId = $user->id;
            $bucketName = config('filesystems.disks.s3.bucket');

            // Delete old file if exists
            if ($user->u_photo) {
                $oldPath = str_replace($bucketName . '/', '', $user->u_photo);
                Storage::disk('s3')->delete($oldPath);
            }

            // Get the file
            $photoFile = $request->file('img_logo');
            
            // Generate a unique file name
            $photoFileName = $userId . '_' . uniqid() . '.' . $photoFile->getClientOriginalExtension();
            
            // Store the file to S3
            $photoPath = $photoFile->storeAs('personal_data/foto', $photoFileName, 's3');
            
            // Update user photo path
            $user->u_photo = $bucketName . '/' . $photoPath;
            $change = $user->save();

            // If user is saved, display success notification, else error
            if ($change) {
                return back()->with('success', 'Photo has been changed :)')->with('path', $bucketName . '/' . $photoPath);
            } else {
                return back()->with('error', 'Ups photo is not save, Please try again!');
            }
        }

        // Return error if the file upload failed
        return back()->withErrors(['img_logo' => 'File upload failed.']);
    }
}
