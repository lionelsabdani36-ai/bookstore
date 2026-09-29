<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken
            ]
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials']]);
        }
        
        $expiresAt = $request->remember ? now()->addDays(30) : null;
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => ['user' => $request->user()]
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'type' => 'required|in:email,whatsapp'
        ]);

        $user = $request->user();
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        \Illuminate\Support\Facades\Cache::put('otp_' . $request->type . '_' . $user->id, $otp, now()->addMinutes(10));

        if ($request->type === 'email') {
            \Illuminate\Support\Facades\Mail::raw('Your verification code is: ' . $otp, function ($message) use ($user) {
                $message->to($user->email)->subject('Verification Code');
            });
        } else {
            if (!$user->whatsapp_number) {
                return response()->json(['status' => 'error', 'message' => 'WhatsApp number is not registered'], 400);
            }
            // Send WhatsApp OTP via Fonnte API
            $fonnteToken = env('FONNTE_API_TOKEN');
            if ($fonnteToken) {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $fonnteToken
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $user->whatsapp_number,
                    'message' => 'Your bookstore verification code is: ' . $otp
                ]);
            } else {
                \Illuminate\Support\Facades\Log::warning("FONNTE_API_TOKEN is not set in .env");
            }
            \Illuminate\Support\Facades\Log::info("WhatsApp OTP for {$user->whatsapp_number}: {$otp}");
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully'
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'type' => 'required|in:email,whatsapp',
            'code' => 'required|string'
        ]);

        $user = $request->user();
        $cachedOtp = \Illuminate\Support\Facades\Cache::get('otp_' . $request->type . '_' . $user->id);

        if (!$cachedOtp || $cachedOtp !== $request->code) {
            throw ValidationException::withMessages(['code' => ['Invalid or expired OTP']]);
        }

        \Illuminate\Support\Facades\Cache::forget('otp_' . $request->type . '_' . $user->id);
        
        if ($request->type === 'email') {
            $user->email_verified_at = now();
        } else {
            $user->whatsapp_verified_at = now();
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => ucfirst($request->type) . ' verified successfully',
            'data' => ['user' => $user]
        ]);
    }
}