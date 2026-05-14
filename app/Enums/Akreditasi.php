<?php

namespace App\Enums;

enum Akreditasi: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case UNACCREDITED = 'Belum Terakreditasi';
    case EXPIRED = 'Kadaluarsa';

    public function label(): string
    {
        return match($this) {
            self::A => 'Akreditasi A (Unggul)',
            self::B => 'Akreditasi B (Baik)',
            self::C => 'Akreditasi C (Cukup)',
            self::UNACCREDITED => 'Belum Terakreditasi',
            self::EXPIRED => 'Masa Berlaku Habis / Kadaluarsa',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::A => 'success',
            self::B => 'primary',
            self::C => 'warning',
            self::UNACCREDITED, self::EXPIRED => 'danger',
        };
    }
}
