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
        $validated = $request->validate([
            'img_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Check if the request has a file
        if ($request->hasFile('img_logo')) {
            // Get the file
            $file = $request->file('img_logo');

            // Define the path to store the file in public/photos
            $destinationPath = public_path('photos');

            // Ensure the directory exists
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Generate a unique file name (to prevent overwriting)
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

            // Move the file to the 'public/photos' directory
            $file->move($destinationPath, $fileName);

            // Store the file path in the database
            $user = Auth::user();
            $user->u_photo = 'photos/' . $fileName; // Save the relative path
            $change = $user->save(); // Save to the database

            // If user is active, display success notification, else error
            if ($change) {
                return back()->with('success', 'Photo has been changed :)')->with('path', 'photos/' . $fileName);
            } else {
                return back()->with('error', 'Ups photo is not save, Please try again!');
            }
        }

        // Return error if the file upload failed
        return back()->withErrors(['img_logo' => 'File upload failed.']);
    }
}
