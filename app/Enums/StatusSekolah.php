<?php

namespace App\Enums;

enum StatusSekolah: string
{
    case NEGERI = 'Negeri';
    case SWASTA = 'Swasta';

    public function label(): string
    {
        return match($this) {
            self::NEGERI => 'Negeri',
            self::SWASTA => 'Swasta',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NEGERI => 'blue',
            self::SWASTA => 'orange',
        };
    }
}
