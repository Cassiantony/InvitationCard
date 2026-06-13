<?php

return [
    'cost_per_card_tsh' => (int) env('INVITATION_COST_TSH', 500),
    'cost_sms_tsh' => (int) env('INVITATION_SMS_COST_TSH', 500),
    'cost_whatsapp_resend_tsh' => (int) env('INVITATION_WHATSAPP_RESEND_COST_TSH', 500),
    'whatsapp_free_resend_hours' => (int) env('INVITATION_WHATSAPP_FREE_HOURS', 20),
    'currency' => 'Tsh',

    /** Simulate WhatsApp/SMS locally without external APIs. */
    'demo_messaging' => env('INVITATION_DEMO_MESSAGING', true),
];
