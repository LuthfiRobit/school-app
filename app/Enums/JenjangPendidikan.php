<?php

namespace App\Enums;

enum JenjangPendidikan: string
{
    case TK = 'TK';
    case KB = 'KB';
    case TPA = 'TPA';
    case SPS = 'SPS';
    case SD = 'SD';
    case SMP = 'SMP';
    case SMA = 'SMA';
    case SMK = 'SMK';
    case SLB = 'SLB';
    case PKBM = 'PKBM';
    case SKB = 'SKB';

    public function label(): string
    {
        return match($this) {
            self::TK => 'Taman Kanak-Kanak',
            self::KB => 'Kelompok Bermain',
            self::TPA => 'Taman Penitipan Anak',
            self::SPS => 'Satuan PAUD Sejenis',
            self::SD => 'Sekolah Dasar',
            self::SMP => 'Sekolah Menengah Pertama',
            self::SMA => 'Sekolah Menengah Atas',
            self::SMK => 'Sekolah Menengah Kejuruan',
            self::SLB => 'Sekolah Luar Biasa',
            self::PKBM => 'PKBM',
            self::SKB => 'SKB',
        };
    }
}
