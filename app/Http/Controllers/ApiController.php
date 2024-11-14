<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function checkSecretCode(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            '_u_secret_code' => 'required|string',
        ]);

        $u_secret_code = $request->_u_secret_code;

        // Here, you can check the secret code logic (for example, checking against a database)
        // For demonstration, we'll assume the secret code is "secret123"
        if ($u_secret_code === 'secret123') {
            // If the secret code is valid, you can proceed to call the external API
            $nohp = '085649888272'; // Replace with the actual phone number if needed
            $pesan = 'aksjhdjkahsljkdhajkshkjahsdak'; // Replace with the actual message if needed

            // Call the external API
            $response = Http::get('http://jezdb.com:3000/api', [
                'nohp' => $nohp,
                'pesan' => $pesan,
            ]);

            // Return the response from the API
            return response()->json(['status' => 200, 'api_response' => $response->json()]);
        }

        // If the secret code is invalid, return an error response
        return response()->json(['status' => 400, 'message' => 'Invalid secret code'], 400);
    }
}