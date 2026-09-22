<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    /**
     * Send OTP to user via email or WhatsApp.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => 'required|string',
            'channel' => 'required|in:email,whatsapp',
        ]);

        $channel = $request->channel;
        $identifier = $request->identifier;

        // Find user by email or phone
        if ($channel === 'email') {
            $user = User::where('email', $identifier)->first();
        } else {
            $user = User::where('phone', $identifier)->first();
        }

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => $channel === 'email'
                    ? 'No account found with this email address.'
                    : 'No account found with this phone number.',
            ], 404);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete any existing OTPs for this identifier/channel
        PasswordResetOtp::where('identifier', $identifier)
            ->where('channel', $channel)
            ->delete();

        // Store OTP (expires in 10 minutes)
        PasswordResetOtp::create([
            'identifier' => $identifier,
            'channel' => $channel,
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via chosen channel
        if ($channel === 'email') {
            $this->sendOtpViaEmail($user, $otp);
        } else {
            $this->sendOtpViaWhatsApp($user, $otp);
        }

        return response()->json([
            'status' => 'success',
            'message' => $channel === 'email'
                ? 'OTP has been sent to your email address.'
                : 'OTP has been sent to your WhatsApp number.',
        ]);
    }

    /**
     * Verify the OTP code.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => 'required|string',
            'channel' => 'required|in:email,whatsapp',
            'otp' => 'required|string|size:6',
        ]);

        $otpRecord = PasswordResetOtp::where('identifier', $request->identifier)
            ->where('channel', $request->channel)
            ->where('verified', false)
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => 'error',
                'message' => 'No OTP request found. Please request a new OTP.',
            ], 404);
        }

        if ($otpRecord->isExpired()) {
            $otpRecord->delete();
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new one.',
            ], 422);
        }

        if (!Hash::check($request->otp, $otpRecord->otp)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code.',
            ], 422);
        }

        // Mark as verified
        $otpRecord->update(['verified' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully.',
        ]);
    }

    /**
     * Reset the password after OTP verification.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => 'required|string',
            'channel' => 'required|in:email,whatsapp',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Find verified OTP record
        $otpRecord = PasswordResetOtp::where('identifier', $request->identifier)
            ->where('channel', $request->channel)
            ->where('verified', true)
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP not verified. Please verify your OTP first.',
            ], 422);
        }

        if ($otpRecord->isExpired()) {
            $otpRecord->delete();
            return response()->json([
                'status' => 'error',
                'message' => 'OTP session has expired. Please start over.',
            ], 422);
        }

        // Re-verify OTP hash for security
        if (!Hash::check($request->otp, $otpRecord->otp)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP.',
            ], 422);
        }

        // Find user and update password
        if ($request->channel === 'email') {
            $user = User::where('email', $request->identifier)->first();
        } else {
            $user = User::where('phone', $request->identifier)->first();
        }

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Clean up all OTPs for this identifier
        PasswordResetOtp::where('identifier', $request->identifier)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password has been reset successfully.',
        ]);
    }

    /**
     * Send OTP via email.
     */
    private function sendOtpViaEmail(User $user, string $otp): void
    {
        try {
            Mail::raw(
                "Hello {$user->name},\n\nYour password reset OTP code is: {$otp}\n\nThis code will expire in 10 minutes.\n\nIf you did not request a password reset, please ignore this message.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Password Reset OTP - Bookstore');
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
        }
    }

    /**
     * Send OTP via WhatsApp using Fonnte API.
     *
     * Register at https://fonnte.com and add your API token to .env as FONNTE_API_TOKEN.
     */
    private function sendOtpViaWhatsApp(User $user, string $otp): void
    {
        $token = config('services.fonnte.token');

        if (!$token) {
            Log::warning('Fonnte API token not configured. OTP not sent via WhatsApp.');
            return;
        }

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $token,
                ],
                CURLOPT_POSTFIELDS => [
                    'target' => $user->phone,
                    'message' => "Hello {$user->name},\n\nYour password reset OTP code is: *{$otp}*\n\nThis code will expire in 10 minutes.\n\nIf you did not request this, please ignore this message.\n\n- Bookstore",
                ],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                Log::error('Fonnte WhatsApp API error', [
                    'http_code' => $httpCode,
                    'response' => $response,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send OTP via WhatsApp: ' . $e->getMessage());
        }
    }
}
