<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'user_type' => 'required'
        ]);

        if ($request->user_type === 'admin') {
            // Admin login - use existing admin auth
            return $this->adminLogin($request);
        } else {
            // Driver login
            return $this->driverLogin($request);
        }
    }

    private function adminLogin(Request $request)
{
    $username = $request->username;
    $password = $request->password;

    // Predefined admin credentials
    $adminUsername = 'admin';
    $adminPassword = 'admin123';

    if ($username === $adminUsername && $password === $adminPassword) {
        // Store admin login in session
        session(['admin_logged_in' => true, 'admin_name' => 'Administrator']);
        return redirect('/dashboard'); // Admin dashboard
    } else {
        return back()->withErrors(['login' => 'Invalid admin credentials'])->withInput();
    }
}


    private function driverLogin(Request $request)
{
    $driver = Driver::where('username', $request->username)->first();

    if (!$driver) {
        return back()->withErrors(['login' => 'Invalid username or password'])->withInput();
    }

    // Make sure password is hashed in DB
    if (!Hash::check($request->password, $driver->password)) {
        return back()->withErrors(['login' => 'Invalid username or password'])->withInput();
    }

    // Only block login if status is 'Inactive' - allow 'Active' and 'On Trip'
    if ($driver->status === 'Inactive') {
        return back()->withErrors(['login' => 'Your account is inactive. Please contact an administrator.'])->withInput();
    }

    // Store driver info in session
    session()->put('driver_id', $driver->driver_id);
    session()->put('driver_name', $driver->fname . ' ' . $driver->lname);

    return redirect()->route('driver.dashboard');
}

}