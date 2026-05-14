<?php

namespace App\Enums;

enum RoleScope: string
{
    case SYSTEM = 'system';
    case PIMPINAN = 'pimpinan';
    case MANAJEMEN = 'manajemen';
    case GURU = 'guru';
    case TENDIK = 'tendik';
    case SISWA = 'siswa';

    public function label(): string
    {
        return match($this) {
            self::SYSTEM => 'System',
            self::PIMPINAN => 'Pimpinan',
            self::MANAJEMEN => 'Manajemen',
            self::GURU => 'Guru',
            self::TENDIK => 'Tendik',
            self::SISWA => 'Siswa',
        };
    }
}
