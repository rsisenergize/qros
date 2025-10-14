<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Otp;
use App\Events\SendOtpEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OtpLoginController extends Controller
{
    /**
     * Show OTP login form
     */
    public function showOtpLoginForm()
    {
        return view('auth.otp-login');
    }

    /**
     * Send OTP to user's email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => __('messages.noUserFoundWithThisEmailAddress')]);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in database with expiry
        Otp::updateOrCreate(
            ['identifier' => $request->email, 'type' => 'login'],
            [
                'token' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
                'used' => false
            ]
        );

        // Dispatch event to send OTP notification
        event(new SendOtpEvent($user, $otp, 'login'));

        $statusMessage = __('messages.otpSentToYourEmailAddress');
        $packageModules = $user->restaurant->package->modules->pluck('name')->toArray();

        if (in_array('Sms', $packageModules) && $user->phone_code && $user->phone_number) {
            $maskedPhone = str_repeat('*', max(0, strlen($user->phone_number) - 3)) . substr($user->phone_number, -3);
            $statusMessage = __('messages.otpSentToYourPhoneNumber', [
                'phone' => '(' . $user->phone_code . ' ' . $maskedPhone . ')'
            ]);
        }

        return back()->with('status', $statusMessage)
                ->with('otp_sent', true)
                ->with('email', $request->email);
    }

    /**
     * Verify OTP and login user
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_combined' => 'required|string|size:6'
        ]);

        $otpRecord = Otp::where('identifier', $request->email)
                       ->where('token', $request->otp_combined)
                       ->where('type', 'login')
                       ->where('used', false)
                       ->where('expires_at', '>', Carbon::now())
                       ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => __('messages.invalidOrExpiredOtp')])
                        ->with('otp_sent', true)
                        ->with('email', $request->email);
        }

        // Mark OTP as used
        $otpRecord->update(['used' => true]);

        // Find and login user
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            Auth::login($user);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => __('messages.userNotFound')]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => __('messages.userNotFound')]);
        }

        // Generate new 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update OTP in database
        Otp::updateOrCreate(
            ['identifier' => $request->email, 'type' => 'login'],
            [
                'token' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
                'used' => false
            ]
        );

        // Dispatch event to send OTP notification
        event(new SendOtpEvent($user, $otp, 'login'));

        return response()->json(['success' => true, 'message' => __('messages.otpResentSuccessfully')]);
    }
} 