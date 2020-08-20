<?php

namespace Caishni\Fawry\Tests\Unit;

use Caishni\Fawry\Card;
use Caishni\Fawry\Exceptions\CardTokenException;
use Caishni\Fawry\Facades\FawryPay;
use Caishni\Fawry\Item;
use Caishni\Fawry\Models\UserCard;
use Caishni\Fawry\Tests\Payment;
use Caishni\Fawry\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CardTokenizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
//    public function it_creates_card_token()
//    {
//        $digits = random_int(00, 99);
//
//        $card = new Card();
//        $card->number = '49876543210987' . $digits;
//        $card->expiryYear = '21';
//        $card->expiryMonth = '05';
//        $card->cvv = 123;
//
//        $response = FawryPay::createCardToken($this->testUser, $card);
//
//        $this->assertInstanceOf(UserCard::class, $response);
//
//        $response->remove();
//    }
//
//    /** @test */
//    public function it_throws_exception_if_card_already_exists()
//    {
//        $this->expectException(CardTokenException::cardAlreadyExists());
//
//        $card = new Card();
//        $card->number = '4987654321098724';
//        $card->expiryYear = '21';
//        $card->expiryMonth = '05';
//        $card->cvv = 123;
//
//        FawryPay::createCardToken($this->testUser, $card);
//        FawryPay::createCardToken($this->testUser, $card);
//    }
//
//    /** @test */
//    public function it_can_be_used_to_make_a_payment()
//    {
//        $card = new Card();
//        $card->number = '5123456789012346';
//        $card->expiryYear = '21';
//        $card->expiryMonth = '05';
//        $card->cvv = 123;
//
//        $this->testUser->id;
//        $this->testUser->save();
//
//        $token = FawryPay::createCardToken($this->testUser, $card);
//
//        $item = new Item();
//        $item->price = 580.55;
//        $item->referenceNumber = 123456789;
//        $item->id = 12355887;
//        $item->description = 'test item';
//
//        FawryPay::chargeCardToken($this->testUser, $token, $card->cvv, $item);
//    }

    /** @test */
    public function test()
    {
//        $cards = FawryPay::listCardTokens($this->testUser);
//        foreach ($cards->cards as $card) {
//            $this->testUser->cards()->create(['token' => $card->token, 'last_four_digits' => $card->lastFourDigits])->delete();
//        }
        Payment::create(['amount' => 257.2]);
        Payment::create(['amount' => 5154.21]);
        Payment::create(['amount' => 239.354456]);
        Payment::create(['amount' => 11]);

        $p = Payment::all();

        $card = new Card();
        $card->number = '5123456789012346';
        $card->expiryYear = '21';
        $card->expiryMonth = '05';
        $card->cvv = 12;

        $response = FawryPay::payAtFawry($this->testUser, $p, '123456985', 'test2', '');

        dd($response);


    }
}