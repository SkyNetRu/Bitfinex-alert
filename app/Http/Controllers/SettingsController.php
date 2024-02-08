<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{

    public $dictionary = [
        'rate' => 0,
        'period' => 1,
        'count' => 2,
        'amount' => 3
    ];
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

        return redirect()->route('settings')->with(['status' => 'Settings updated.']);
    }

    public function submitOrder (Request $request)
    {
        return redirect()->route('settings');

        $validatedData = $request->validate([
            'currency' => 'required',
            'price' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'isSell' => 'required',
        ]);

        $apiPath = 'v2/auth/w/order/submit';
        $amount = $validatedData['isSell'] ? '-'.$validatedData['amount'] : $validatedData['amount'];

        $body = [
            'type' => $validatedData['type'],
            'symbol' => $validatedData['currency'],
            'price' => $validatedData['price'],
            'amount' => $amount,
            'meta' => (object)['aff_code' => 'GlIjBvbOC'],
        ];

        $response = SendPostRequest($apiPath, $body);

        if ($response->object()[0] == 'error') {
            return redirect()->route('settings')->withErrors(['error' => $response->object()[2]]);
        }

        return redirect()->route('settings')->with(['status' => $response->object()[6]]);
    }
}
