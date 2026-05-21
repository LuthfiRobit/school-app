<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case DRAFT = 'draft';
    case REGISTERED = 'registered';
    case WAITING_PAYMENT_REG = 'waiting_payment_reg';
    case VERIFIED_REG = 'verified_reg';
    case IN_REVIEW = 'in_review';
    case PASSED = 'passed';
    case WAITING_LIST = 'waiting_list';
    case REJECTED = 'rejected';
    case WAITING_PAYMENT_FINAL = 'waiting_payment_final';
    case SETTLED = 'settled';
    case PERMANENT_STUDENT = 'permanent_student';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::REGISTERED => 'Terdaftar',
            self::WAITING_PAYMENT_REG => 'Menunggu Pembayaran Formulir',
            self::VERIFIED_REG => 'Pembayaran Formulir Terverifikasi',
            self::IN_REVIEW => 'Sedang Dinilai',
            self::PASSED => 'Lulus Seleksi',
            self::WAITING_LIST => 'Cadangan (Waiting List)',
            self::REJECTED => 'Tidak Lulus',
            self::WAITING_PAYMENT_FINAL => 'Menunggu Pembayaran Daftar Ulang',
            self::SETTLED => 'Lunas Daftar Ulang',
            self::PERMANENT_STUDENT => 'Siswa Tetap',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::DRAFT => 'bg-light-secondary text-secondary',
            self::REGISTERED => 'bg-light-info text-info',
            self::WAITING_PAYMENT_REG => 'bg-light-warning text-warning',
            self::VERIFIED_REG => 'bg-light-success text-success',
            self::IN_REVIEW => 'bg-light-primary text-primary',
            self::PASSED => 'bg-light-success text-success',
            self::WAITING_LIST => 'bg-light-warning text-warning',
            self::REJECTED => 'bg-light-danger text-danger',
            self::WAITING_PAYMENT_FINAL => 'bg-light-warning text-warning',
            self::SETTLED => 'bg-light-success text-success',
            self::PERMANENT_STUDENT => 'bg-light-success text-success',
        };
    }
}
