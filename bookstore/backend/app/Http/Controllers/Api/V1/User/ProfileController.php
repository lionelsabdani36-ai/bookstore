<?php
namespace App\Http\Controllers\Api\V1\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $request->user(),
                'about_us' => 'Bookstore API V1',
                'contact' => 'support@bookstore.test'
            ]
        ]);
    }

    public function update(Request $request) {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'whatsapp_number' => 'nullable|string|max:20',
            'photo' => 'nullable|image',
            'password' => 'nullable|string|min:6',
        ]);
        
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json(['status' => 'success', 'data' => ['user' => $user]]);
    }
}