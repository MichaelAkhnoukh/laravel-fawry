<?php

namespace Caishni\Fawry;

use Caishni\Fawry\Exceptions\CardTokenException;
use Caishni\Fawry\Models\UserCard;
use GuzzleHttp\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;

class FawryPay
{
    protected $url;
    protected $merchantCode;
    protected $securityKey;
    protected $endpoints;
    protected $httpClient;

    const PAYATFAWRY = 'PAYATFAWRY';
    const CARD = 'CARD';
    const UNPAID = 'UNPAID';
    const PAID = 'PAID';
    const CANCELLED = 'CANCELLED';
    const REFUNDED = 'REFUNDED';
    const EXPIRED = 'EXPIRED';
    const FAILED = 'FAILED';

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

    public function createCardToken($user, Card $card)
    {
        $client = $this->httpClient->asJson()->acceptJson()->post(
            $this->endpoints['card_tokenization']['uri'], [
                "merchantCode" => $this->merchantCode,
                "customerProfileId" => $user->id,
                "customerMobile" => $user->phone,
                "customerEmail" => $user->email,
                "cardNumber" => $card->number,
                "expiryYear" => $card->expiryYear,
                "expiryMonth" => $card->expiryMonth,
                "cvv" => $card->cvv
            ]
        );

        if ($client->status() == 200 && array_key_exists('card', $client->json())) {
            return $user->cards()->create(['token' => $client->object()->card->token, 'last_four_digits' => $client->object()->card->lastFourDigits]);
        } elseif ($client->status() == 200 && $client->object()->statusCode == 17003) {
            throw CardTokenException::cardAlreadyExists();
        } else {
            throw CardTokenException::error();
        }
    }

    public function listCardTokens($user)
    {
        $signature = hash('sha256', $this->merchantCode . $user->id . $this->securityKey);

        // overrides special character encoding int query parameters
        return $this->httpClient->withMiddleware(Middleware::mapRequest(function (RequestInterface $request) use ($user, $signature) {
            $uri = Uri::withQueryValues($request->getUri(), [
                'merchantCode' => $this->merchantCode,
                "customerProfileId" => $user->id,
                "signature" => $signature,
            ]);
            return $request->withUri($uri);
        }))->get($this->endpoints['card_tokenization']['uri'])->object();
    }

    public function deleteCardToken(UserCard $card)
    {
        $signature = hash('sha256', $this->merchantCode . $card->user->id . $card->token . $this->securityKey);

        // overrides special character encoding int query parameters
        return $this->httpClient->withMiddleware(Middleware::mapRequest(function (RequestInterface $request) use ($card, $signature) {
            $uri = Uri::withQueryValues($request->getUri(), [
                'merchantCode' => $this->merchantCode,
                "customerProfileId" => $card->user->id,
                "cardToken" => $card->token,
                "signature" => $signature,
            ]);
            return $request->withUri($uri);
        }))->delete($this->endpoints['card_tokenization']['uri'])->object();
    }

    public function chargeCardToken(UserCard $card, $cvv, Collection $items, string $referenceNumber, string $description)
    {
        $signature = hash('sha256', $this->merchantCode . $referenceNumber . (string)$card->user->id . FawryPay::CARD . $items->mapIntoItems()->sum('price') . $card->token . $cvv . $this->securityKey);


        $client = $this->httpClient->post(
            $this->endpoints['card_payment']['uri'], [
            'merchantCode' => $this->merchantCode,
            'merchantRefNum' => $referenceNumber,
            'customerProfileId' => (string)$card->user->id,
            'customerMobile' => $card->user->phone,
            'customerEmail' => $card->user->email,
            'cardToken' => $card->token,
            'cvv' => $cvv,
            'amount' => $items->mapIntoItems()->sum('price'),
            'paymentMethod' => FawryPay::CARD,
            'currencyCode' => 'EGP',
            'description' => $description,
            'chargeItems' => $items->mapIntoItems()->toArray(),
            'signature' => $signature,
        ]);
        return $client->object();
    }

    public function chargeCard($user, Card $card, Collection $items, string $referenceNumber, string $description)
    {
        $signature = hash('sha256', $this->merchantCode . $referenceNumber . $user->id . FawryPay::CARD . $items->mapIntoItems()->sum('price') . $card->number . $card->expiryYear . $card->expiryMonth . $card->cvv . $this->securityKey);

        $client = $this->httpClient->post(
            $this->endpoints['card_payment']['uri'], [
            'merchantCode' => $this->merchantCode,
            'customerMobile' => $user->phone,
            'customerEmail' => $user->email,
            'customerProfileId' => $user->id,
            'cardNumber' => $card->number,
            'cardExpiryYear' => $card->expiryYear,
            'cardExpiryMonth' => $card->expiryMonth,
            'cvv' => $card->cvv,
            'merchantRefNum' => $referenceNumber,
            'amount' => $items->mapIntoItems()->sum('price'),
            'paymentMethod' => FawryPay::CARD,
            'currencyCode' => 'EGP',
            'description' => $description,
            'chargeItems' => $items->mapIntoItems()->toArray(),
            'signature' => $signature,
        ]);

        return $client->object();
    }

    public function payAtFawry($user, Collection $items, string $referenceNumber, string $description, $paymentExpiry)
    {
        $price = number_format($items->mapIntoItems()->sum('price'), 2, '.', '');
        $signature = hash('sha256', $this->merchantCode . $referenceNumber . $user->id . FawryPay::PAYATFAWRY . $price . $this->securityKey);

        $client = $this->httpClient->post(
            $this->endpoints['card_payment']['uri'], [
            'merchantCode' => $this->merchantCode,
            'merchantRefNum' => $referenceNumber,
            'customerMobile' => $user->phone,
            'customerEmail' => $user->email,
            'customerProfileId' => $user->id,
            'paymentMethod' => FawryPay::PAYATFAWRY,
            'amount' => $items->mapIntoItems()->sum('price'),
            'currencyCode' => 'EGP',
            'description' => $description,
            'paymentExpiry' => $paymentExpiry,
            'chargeItems' => $items->mapIntoItems()->toArray(),
            'signature' => $signature,
        ]);

        return $client->object();
    }

    public function getPaymentStatus($referenceNumber)
    {
        $signature = hash('sha256', $this->merchantCode . $referenceNumber . $this->securityKey);
        return $this->httpClient->withMiddleware(Middleware::mapRequest(function (RequestInterface $request) use ($referenceNumber, $signature) {
            $uri = Uri::withQueryValues($request->getUri(), [
                'merchantCode' => $this->merchantCode,
                "merchantRefNumber" => $referenceNumber,
                "signature" => $signature,
            ]);
            return $request->withUri($uri);
        }))->get($this->endpoints['payment_status']['uri'])->object();
    }
}