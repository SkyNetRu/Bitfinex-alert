<?php

namespace App\Console\Commands;

use App\Models\Settings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class checkFundRate extends Command
{
    public $dictionary = [
        'rate' => 0,
        'period' => 1,
        'count' => 2,
        'amount' => 3
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkFundRate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check rate and submit order if needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = Settings::first();
        $book = $this->getRate()[0];
        $book[$this->dictionary['rate']] = 0.056;

        if ($book[$this->dictionary['rate']] < $settings->rate_alert) {
            return;
        }

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
                    sleep(1);
                    $orderSubmitResult = SendPostRequest($apiPath, $body)->object();

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
