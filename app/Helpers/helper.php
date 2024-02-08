<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('SendPostRequest')) {
    function SendPostRequest($apiPath, $body = [])
    {

        $baseUrl = config('bitfinex.authBaseUrl');
        $apiKey = config('bitfinex.apiKey');
        $apiSecret = config('bitfinex.apiSecret');

        $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES);
        $nonce = (string) (time() * 1000 * 1000); // epoch in ms * 1000
        $sigPayload = "/api/{$apiPath}{$nonce}{$bodyJson}";
        $sig = hash_hmac('sha384', $sigPayload, $apiSecret);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'bfx-nonce' => $nonce,
            'bfx-apikey' => $apiKey,
            'bfx-signature' => $sig,
        ];

        return Http::withHeaders($headers)
            ->withBody($bodyJson)
            ->post($baseUrl.'/'.$apiPath);
    }
}

if (!function_exists('SendGetRequest')) {

    function SendGetRequest($apiPath)
    {
        $baseUrl = config('bitfinex.authBaseUrl');
        return Http::accept('application/json')->get($baseUrl . '/' . $apiPath)->json();
    }
}
