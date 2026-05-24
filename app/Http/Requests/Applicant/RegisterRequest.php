<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
            'full_name' => ['required', 'string', 'max:255'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:L,P'],
            'religion' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:20'],
            'nisn' => ['nullable', 'string', 'size:10', 'unique:applicants,nisn'],
            'nik' => ['nullable', 'string', 'size:16', 'unique:applicants,nik'],
            'citizenship' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'password' => 'Password',
            'full_name' => 'Nama Lengkap',
            'place_of_birth' => 'Tempat Lahir',
            'date_of_birth' => 'Tanggal Lahir',
            'gender' => 'Jenis Kelamin',
            'religion' => 'Agama',
            'phone' => 'Nomor HP/Telepon',
            'nisn' => 'NISN',
            'nik' => 'NIK',
            'citizenship' => 'Kewarganegaraan',
        ];
    }
}
