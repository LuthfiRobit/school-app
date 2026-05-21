<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::UNPAID => 'Belum Dibayar',
            self::PARTIAL => 'Dibayar Sebagian',
            self::PAID => 'Lunas',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::UNPAID => 'bg-light-danger text-danger',
            self::PARTIAL => 'bg-light-warning text-warning',
            self::PAID => 'bg-light-success text-success',
            self::CANCELLED => 'bg-light-secondary text-secondary',
        };
    }
}
