<?php

namespace Caishni\Fawry;

use Illuminate\Support\Facades\Http;

class FawryPay
{
    protected $url;
    protected $merchantCode;
    protected $securityKey;
    protected $endpoints;
    protected $httpClient;

    const PAYATFAWRY = 'PAYATFAWRY';
    const CARD = 'CARD';

    public function __construct(array $config)
    {
        if ($config['environment'] === 'staging') {
            $this->url = $config['staging_url'];
        } elseif ($config['environment'] === 'production') {
            $this->url = $config['production_url'];
        } else {
            throw new \RuntimeException("Invalid FawryPay environment");
        }

        $this->merchantCode = $config['merchant_code'];

        $this->securityKey = $config['security_key'];

        $this->endpoints = $config['endpoints'];

        $this->httpClient = Http::baseUrl($this->url);
    }

    public function createCardToken($user, array $card)
    {
        $client = $this->httpClient->asJson()->acceptJson()->post(
            $this->endpoints['card_tokenization']['uri'], [
                "merchantCode" => $this->merchantCode,
                "customerProfileId" => $user->id,
                "customerMobile" => $user->phone,
                "customerEmail" => $user->email,
                "cardNumber" => $card['number'],
                "expiryYear" => $card['expiry_year'],
                "expiryMonth" => $card['expiry_month'],
                "cvv" => $card['cvv']
            ]
        );

        dd($client->object());
    }
}