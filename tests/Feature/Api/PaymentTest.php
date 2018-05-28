<?php
namespace UniSharp\Cart\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use UniSharp\Cart\Tests\TestCase;
use UniSharp\Cart\Enums\Payment;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function testShowPayments()
    {
        $response = $this->get('/api/v1/payments');
        $this->assertEquals(Payment::choices(), $response->json());
    }
}
