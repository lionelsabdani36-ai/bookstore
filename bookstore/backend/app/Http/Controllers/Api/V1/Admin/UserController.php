<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() { return response()->json(['status' => 'success', 'data' => User::all()]); }
    public function show(User $user) { return response()->json(['status' => 'success', 'data' => $user]); }
    public function destroy(User $user) {
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}