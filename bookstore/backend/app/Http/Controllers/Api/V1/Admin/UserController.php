<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() { return response()->json(['status' => 'success', 'data' => User::all()]); }
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,customer',
            'password' => 'required|string|min:8',
            'photo' => 'nullable|image',
        ]);
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }
        return response()->json(['status' => 'success', 'data' => User::create($validated)]);
    }
    public function show(User $user) { return response()->json(['status' => 'success', 'data' => $user]); }
    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,customer',
            'password' => 'nullable|string|min:8',
            'photo' => 'nullable|image',
        ]);
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }
        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        $user->update($validated);
        return response()->json(['status' => 'success', 'data' => $user]);
    }
    public function destroy(User $user) {
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}