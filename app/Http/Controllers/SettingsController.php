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

    public function test() {
        $settings = Settings::first();
        $book = $this->getRate()[0];
        $book[$this->dictionary['rate']] = 0.056;
        dump('$book', $book);

        if ($book[$this->dictionary['rate']] < $settings->rate_alert) {
            return;
        }


        dump('$book test');
        $this->cancelAllFundingOrders();
        $wallets = $this->getWallets();


        if ($wallets[0] !== 'error') {
            foreach ($wallets as $wallet) {
                if ($wallet[0] == 'funding' && $wallet[1] == 'USD') {
                    $apiPath = 'v2/auth/w/funding/offer/submit';
                    $body = [
                        'type' => 'LIMIT',
                        'symbol' => 'fUSD',
                        'amount' => (string)$wallet[4],
                        'rate' => (string)$book[$this->dictionary['rate']],
                        'period' => $book[$this->dictionary['period']]
                    ];
                    dump('$body', $body);
                    sleep(1);
                    $orderSubmitResult = SendPostRequest($apiPath, $body)->object();

                    dump('$orderSubmitResult', $orderSubmitResult);

                    if ($orderSubmitResult[0] == 'error') {
                        Mail::send('emails.rate-alert-error', ['settings' => $settings, 'book' => $book, 'body' => $body, 'orderSubmitResult' => $orderSubmitResult], function ($message) {
                            $message->to(config('bitfinex.alert_email'), config('bitfinex.alert_name'))
                                ->subject('Bitfinex submit order error');
                        });

                        return;
                    }

                    Mail::send('emails.rate-alert-notify', ['settings' => $settings, 'book' => $book, 'orderSubmitResult' => $orderSubmitResult], function ($message) {
                        $message->to(config('bitfinex.alert_email'), config('bitfinex.alert_name'))
                            ->subject('Bitfinex Funding Rate Alert');
                    });
                }
            }
        }
    }

    public function getRate()
    {
        $settings = Settings::first();
        $apiPath = 'v2/book/' . $settings->currency . '/P0?len=1';
        return SendGetRequest($apiPath);
    }

    public function cancelAllFundingOrders()
    {
        $apiPath = 'v2/auth/w/funding/offer/cancel/all';
        sleep(1);
        SendPostRequest($apiPath);
    }

    public function getWallets()
    {
        $apiPath = 'v2/auth/r/wallets';
        sleep(1);
        return SendPostRequest($apiPath)->object();
    }
}
