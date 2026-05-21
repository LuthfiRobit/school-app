<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::CONFIRMED => 'Dikonfirmasi',
            self::REJECTED => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'bg-light-warning text-warning',
            self::CONFIRMED => 'bg-light-success text-success',
            self::REJECTED => 'bg-light-danger text-danger',
        };
    }
}
