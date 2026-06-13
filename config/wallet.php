<?php

return [
    'currency' => 'Tsh',

    /** When true in local env, top-ups complete instantly (for testing without Stripe). */
    'demo_instant_topup' => env('WALLET_DEMO_INSTANT_TOPUP', true),

    'min_topup_tsh' => (int) env('WALLET_MIN_TOPUP_TSH', 1000),

    'preset_amounts_tsh' => [5000, 10000, 25000, 50000, 100000],

    'payment_methods' => [
        'mixx_by_yas' => [
            'label' => 'Mixx by YAS',
            'icon' => 'fa-mobile-alt',
            'stripe_type' => 'mobile_money',
            'provider' => 'mixx_by_yas',
        ],
        'airtel_money' => [
            'label' => 'Airtel Money',
            'icon' => 'fa-mobile-alt',
            'stripe_type' => 'mobile_money',
            'provider' => 'airtel_money',
        ],
        'mpesa' => [
            'label' => 'M-Pesa',
            'icon' => 'fa-mobile-alt',
            'stripe_type' => 'mobile_money',
            'provider' => 'mpesa',
        ],
        'halopesa' => [
            'label' => 'Halopesa',
            'icon' => 'fa-mobile-alt',
            'stripe_type' => 'mobile_money',
            'provider' => 'halopesa',
        ],
        'card' => [
            'label' => 'Card (Visa / Mastercard)',
            'icon' => 'fa-credit-card',
            'stripe_type' => 'card',
            'provider' => 'card',
        ],
    ],
];
