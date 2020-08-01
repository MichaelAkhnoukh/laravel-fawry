<?php

namespace Caishni\Fawry\Tests\Unit;

use Caishni\Fawry\Facades\FawryPay;
use Caishni\Fawry\Tests\TestCase;

class CardTokenizationTest extends TestCase
{
    /** @test */
    public function it_creates_card_token()
    {
        $card = [
            'number' => '4987654321098769',
            'expiry_year' => '21',
            'expiry_month' => '05',
            'cvv' => 123
        ];

        $response = FawryPay::createCardToken($this->testUser, $card);

        dd($response->json());
    }
}