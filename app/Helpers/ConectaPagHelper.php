<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ConectaPagHelper
{
    public $baseUrl;
    public $clientId;
    public $clientSecret;

    public function __construct()
    {
        $this->baseUrl = env('CONECTAPAG_URL');
        $this->clientId = env('CONECTAPAG_CLIENT_ID');
        $this->clientSecret = env('CONECTAPAG_CLIENT_SECRET');
    }

    /**
     * Method responsible for get access token
     */
    private function getAccessToken()
    {
        // verify cache expired token
        if (!Cache::has('conectapag_access_token')) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->withOptions(['verify' => env('SSL_VERIFY')])->post($this->baseUrl . '/token', [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret
                    ]);

            $responseData = $response->object();

            // if error
            if (!$response->successful()) {
                Log::error('Access token error');
                Log::error(json_encode($responseData));

                return ['success' => false, 'error' => $responseData->error];
            }

            // set cache token
            Cache::put('conectapag_access_token', $responseData->token, $responseData->expires_in);
        }

        return Cache::get('conectapag_access_token');
    }

    public function getPaymentMethods()
    {
        try {
            if (!Cache::has('conectapag_payment_methods')) {
                $response = Http::withOptions(['verify' => false])->withToken($this->getAccessToken())
                    ->withHeaders([
                        'Accept' => 'application/json',
                    ])
                    ->get($this->baseUrl . '/settings/payment-methods');

                $responseData = $response->object();

                if (!$response->successful()) {
                    Log::error('Conecta Pag error');
                    Log::error(json_encode($responseData));

                    return ['success' => false, 'error' => $responseData->error];
                }

                // formatting
                $data = [];
                foreach ($responseData->data as $key => $item) {
                    foreach ($item as $method) {
                        $data[$key][] = [
                            'slug' => $method->slug,
                            'coin' => $method->coin,
                            'hash' => $method->hash,
                        ];
                    }
                }

                Cache::put('conectapag_payment_methods', $data, 1440);
            }

            return Cache::get('conectapag_payment_methods');
        } catch (Exception $e) {
            Log::error("{$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");

            return [];
        }
    }

    public function getHashByCoin(string $coin)
    {
        $paymentMethods = $this->getPaymentMethods();
        $hash = null;

        foreach ($paymentMethods as $items) {
            if (is_array($items)) {
                foreach ($items as $item) {
                    if ($item->coin === $coin) {
                        return $item->hash;
                    }
                }
            }
        }

        return $hash;
    }

    public function sendTransaction(array $args)
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->withToken($this->getAccessToken())
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post($this->baseUrl . '/transaction', [
                    'amount' => intval($args['amount'] * 100),
                    'payment_method' => $args['payment_method'],
                    'client' => [
                        'identifier' => $args['client']['identifier'],
                        'document' => $args['client']['document'],
                        'name' => $args['client']['name'],
                        'email' => $args['client']['email'],
                    ]
                ]);

            Log::info(json_encode($args));
            Log::info(json_encode([
                'amount' => intval($args['amount'] * 100),
                'payment_method' => $args['payment_method'],
                'client' => [
                    'identifier' => $args['client']['identifier'],
                    'document' => $args['client']['document'],
                    'name' => $args['client']['name'],
                    'email' => $args['client']['email'],
                ]
            ]));

            if ($response->status() === 401)
                return ['success' => false, 'error' => 'Falha de autorização com o gateway de pagamento.'];

            $responseData = $response->object();

            if (!$response->successful()) {
                Log::error('Conecta Pag error');
                Log::error(json_encode($responseData));

                return ['success' => false, 'error' => $responseData->error];
            }

            return [
                'success' => true,
                'data' => $responseData->data
            ];
        } catch (Exception $e) {
            Log::error("{$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendWithdraw(array $args)
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->withToken($this->getAccessToken())
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post($this->baseUrl . '/withdraw', [
                    'amount' => (int) $args['amount'] * 100,
                    'hash' => $args['hash'],
                    'receiver' => [
                        'identifier' => $args['receiver']['identifier'],
                        'name' => $args['receiver']['name'],
                        'email' => $args['receiver']['email'],
                    ],
                    'dict' => [
                        'key' => $args['pix_type'],
                        'value' => $args['pix_key']
                    ],
                    'crypto' => [
                        'wallet_address' => null,
                        'currency' => null
                    ]
                ]);

            $responseData = $response->object();

            if (!$response->successful()) {
                Log::error('Conecta Pag error');
                Log::error(json_encode($responseData));

                return ['success' => false, 'error' => $responseData->error];
            }

            return [
                'success' => true,
                'data' => $responseData->data
            ];
        } catch (Exception $e) {
            Log::error("{$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getStatus($referenceCode)
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->withToken($this->getAccessToken())
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post($this->baseUrl . '/transaction/status', [
                    'reference_code' => $referenceCode
                ]);

            if ($response->status() === 401)
                return ['success' => false, 'error' => 'Falha de autorização com o gateway de pagamento.'];

            $responseData = $response->object();

            if (!$response->successful()) {
                Log::error('Conecta Pag error');
                Log::error(json_encode($responseData));

                return ['success' => false, 'error' => $responseData->error];
            }

            return [
                'success' => true,
                'data' => $responseData->data
            ];
        } catch (Exception $e) {
            Log::error("{$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}