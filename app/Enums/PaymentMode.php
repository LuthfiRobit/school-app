<?php

namespace App\Enums;

enum PaymentMode: string
{
    case PRE_PAYMENT = 'pre_payment';
    case POST_PAYMENT = 'post_payment';

    public function label(): string
    {
        return match($this) {
            self::PRE_PAYMENT => 'Bayar Sebelum Tes (Pre-Payment)',
            self::POST_PAYMENT => 'Bayar Setelah Lulus (Post-Payment)',
        };
    }
}
