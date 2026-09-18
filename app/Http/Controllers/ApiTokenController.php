<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiTokenController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string'], 'device_name' => ['required', 'string', 'max:100']]);
        $user = User::where('email', $credentials['email'])->first();
        abort_unless($user && Hash::check($credentials['password'], $user->password), 401, 'Invalid credentials.');

        return response()->json(['token' => $user->createToken($credentials['device_name'])->plainTextToken, 'user' => $user]);
    }

    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->noContent();
    }
}