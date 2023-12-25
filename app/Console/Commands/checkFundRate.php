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
    protected $description = 'Check rate and send email if it is above alert rate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = Settings::first();
        $baseUrl = config('bitfinex.baseUrl');
        $apiPath = 'v2/book/'.$settings->currency.'/P0?len=1';
        $response = Http::accept('application/json')->get($baseUrl.'/'.$apiPath)->json();
        $rate = $response[0][$this->dictionary['rate']] * 100;

        if ($rate >= $settings->rate_alert) {
            Mail::send('emails.rate-alert-notify', ['settings' => $settings, 'rate' => $rate], function($message)
            {
                $message->to(config('bitfinex.alert_email'), config('bitfinex.alert_name'))
                    ->subject('Bitfinex Funding Rate Alert');
            });
        }
    }
}
