<?php

namespace App\Enums;

enum PaymentMode: string
{
    case PRE_PAYMENT = 'PRE_PAYMENT';
    case POST_PAYMENT = 'POST_PAYMENT';

    public function label(): string
    {
        return match($this) {
            self::PRE_PAYMENT => 'Bayar Sebelum Tes (Pre-Payment)',
            self::POST_PAYMENT => 'Bayar Setelah Lulus (Post-Payment)',
        };
    }
}
