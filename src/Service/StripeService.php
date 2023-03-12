<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService
{
    private string $stripePublicKey;
    private string $stripeSecretKey;

    public function __construct(string $stripePublicKey, string $stripeSecretKey)
    {
        $this->stripePublicKey = $stripePublicKey;
        $this->stripeSecretKey = $stripeSecretKey;

        Stripe::setApiKey($stripeSecretKey);
    }

    public function createPaymentIntent(int $amount, string $currency, array $metadata = []): array
    {
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => $metadata,
        ]);

        return [
            'clientSecret' => $paymentIntent->client_secret,
        ];
    }

   
}