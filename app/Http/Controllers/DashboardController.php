<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class DashboardController extends Controller
{

    public function index()
    {
        if (user()->hasRole('Super Admin')) {
            return redirect(RouteServiceProvider::SUPERADMIN_HOME);
        }

        return view('dashboard.index');
    }

    public function superadmin()
    {
        // Check if onboarding steps are completed
        $smtpConfigured = (smtp_setting()->mail_driver == 'smtp' && smtp_setting()->verified) || smtp_setting()->mail_driver != 'smtp';
        $cronConfigured = global_setting()->hide_cron_job == 1;
        $appNameChanged = global_setting()->name != 'TableTrack'; // Assuming 'TableTrack' is the default name

        // If any of the onboarding steps are not completed, redirect to the onboarding page
        if ((!$smtpConfigured || !$cronConfigured || !$appNameChanged) && !app()->environment('development')) {
            return view('dashboard.onboarding', compact('smtpConfigured', 'cronConfigured', 'appNameChanged'));
        }

        return view('dashboard.superadmin');
    }

    public function beamAuth()
    {
        $userID = Str::slug(global_setting()->name) . '-' . auth()->id();
        $userIDInQueryParam = request()->user_id;

        if ($userID != $userIDInQueryParam) {
            return response('Inconsistent request', 401);
        } else {
            $beamsClient = new \Pusher\PushNotifications\PushNotifications([
                'instanceId' => pusherSettings()->instance_id,
                'secretKey' => pusherSettings()->beam_secret,
            ]);

            $beamsToken = $beamsClient->generateToken($userID);
            return response()->json($beamsToken);
        }
    }


    public function sendPushNotifications($usersIDs, $title, $body, $link)
    {
        if (App::environment('codecanyon') && pusherSettings()->beamer_status && count($usersIDs) > 0) {
            $beamsClient = new \Pusher\PushNotifications\PushNotifications([
                'instanceId' =>  pusherSettings()->instance_id,
                'secretKey' =>  pusherSettings()->beam_secret,
            ]);


            $pushIDs = [];

            foreach ($usersIDs[0] as $key => $uid) {
                $pushIDs[] = Str::slug(global_setting()->name) . '-' . $uid;
            }

            $publishResponse = $beamsClient->publishToUsers(
                $pushIDs,
                array(
                    'web' => array(
                        'notification' => array(
                            'title' => $title,
                            'body' => $body,
                            'deep_link' => $link,
                            'icon' => global_setting()->logo_url
                        )
                    )
                )
            );
        }
    }

    public function accountUnverified()
    {
        return view('dashboard.padding-approval');
    }
}
