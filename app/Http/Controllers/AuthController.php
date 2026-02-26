<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Admin login check
        if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin123') {
            session(['admin_logged_in' => true, 'admin_name' => 'Administrator']);
            return redirect()->route('dashboard');
        }

        // Driver login
        $driver = Driver::where('username', $credentials['username'])->first();

        if (!$driver) {
            return back()->with('error', 'Invalid username or password')->withInput();
        }

        // Try both plain text and hashed password (for compatibility)
        $passwordMatches = false;
        
        // First try plain text comparison
        if ($credentials['password'] === $driver->password) {
            $passwordMatches = true;
        }
        // Then try hashed password
        elseif (Hash::check($credentials['password'], $driver->password)) {
            $passwordMatches = true;
        }

        if (!$passwordMatches) {
            return back()->with('error', 'Invalid username or password')->withInput();
        }

        // Check driver status
        if ($driver->status === 'Inactive') {
            return back()->with('error', 'Your account is inactive. Please contact an administrator.')->withInput();
        }

        // Set session
        session([
            'driver_id' => $driver->driver_id,
            'driver_name' => trim($driver->fname . ' ' . $driver->lname)
        ]);

        return redirect()->route('driver.dashboard');
    }
}