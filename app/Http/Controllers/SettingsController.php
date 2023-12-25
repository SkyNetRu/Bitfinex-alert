<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    public function index()
    {
        $settings = Settings::first();

        return view('Settings')->with('settings', $settings);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'currency' => 'required',
            'rate_alert' => 'required',
        ]);

        Settings::updateOrCreate(
            ['id' => 1],
            [
                'currency' => $validatedData['currency'],
                'rate_alert' => $validatedData['rate_alert']
            ]
        );

        return redirect()->route('settings');
    }
}
