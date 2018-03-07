<?php

namespace RicoGoh\Timezones;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TimezonesController extends Controller
{
    public function index($timezone = null)
    {
        $location = [];
        $current_time = '';

        try {
            $current_time = ($timezone)
                ? Carbon::now(str_replace('-', '/', $timezone))
                : Carbon::now();
        } catch (\Exception $e) {
            abort(404);
        }

        if ($timezone) {
            if (strpos($timezone, '-') !== false) {
                $location = explode('-', ucwords(str_replace('_', ' ', $timezone)));
            } else {
                $location = [strtoupper($timezone)];
            }
        }
        
        return view('ricogoh/timezones::time', compact('current_time', 'location'));
    }

    public function readme()
    {
        return view('ricogoh/timezones::readme');
    }
}
