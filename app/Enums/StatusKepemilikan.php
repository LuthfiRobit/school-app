<?php

namespace App\Enums;

enum StatusKepemilikan: string
{
    case PEMERINTAH_PUSAT = 'Pemerintah Pusat';
    case PEMERINTAH_DAERAH = 'Pemerintah Daerah';
    case YAYASAN = 'Yayasan';
    case MASYARAKAT = 'Masyarakat';
    case LAINNYA = 'Lainnya';

    public function label(): string
    {
        return match($this) {
            self::PEMERINTAH_PUSAT => 'Pemerintah Pusat',
            self::PEMERINTAH_DAERAH => 'Pemerintah Daerah (Pemda)',
            self::YAYASAN => 'Yayasan / Badan Hukum',
            self::MASYARAKAT => 'Masyarakat / Komunitas',
            self::LAINNYA => 'Lainnya',
        };
    }
}
