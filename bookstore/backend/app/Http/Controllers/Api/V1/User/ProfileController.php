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
}