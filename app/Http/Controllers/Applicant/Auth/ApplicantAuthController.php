<?php

namespace App\Http\Controllers\Applicant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\RegisterRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\ApplicantRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ApplicantAuthController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected ApplicantRepositoryInterface $applicantRepo
    ) {}

    /**
     * Tampilkan form registrasi.
     */
    public function showRegister(): View
    {
        return view('portal.auth.register');
    }

    /**
     * Proses registrasi akun calon siswa baru.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            // Generate unique username based on email
            $baseUsername = preg_replace('/[^a-zA-Z0-9]/', '', strstr($request->email, '@', true));
            if (empty($baseUsername)) {
                $baseUsername = 'applicant';
            }
            $username = $baseUsername;
            $counter = 1;
            while ($this->userRepo->usernameExists($username)) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $user = $this->userRepo->create([
                'name' => $request->full_name,
                'email' => $request->email,
                'username' => $username,
                'password' => Hash::make($request->password),
            ]);

            // Assign role
            $user->assignRole('Applicant');

            // Create applicant profile
            $this->applicantRepo->create([
                'user_id' => $user->id,
                'full_name' => $request->full_name,
                'place_of_birth' => $request->place_of_birth,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'phone' => $request->phone,
                'nisn' => $request->nisn,
                'nik' => $request->nik,
                'citizenship' => $request->citizenship ?? 'WNI',
                'created_by' => $user->id,
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('portal.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di portal pendaftaran.');
    }

    /**
     * Tampilkan form login.
     */
    public function showLogin(): View
    {
        return view('portal.auth.login');
    }

    /**
     * Proses autentikasi login calon siswa.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $this->ensureIsNotRateLimited($request);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Memastikan user memiliki role Applicant
            if (!$user->hasRole('Applicant')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                RateLimiter::hit($this->throttleKey($request));

                throw ValidationException::withMessages([
                    'login' => ['Akun Anda tidak memiliki akses ke portal calon siswa.'],
                ]);
            }

            RateLimiter::clear($this->throttleKey($request));
            $request->session()->regenerate();

            return redirect()->intended(route('portal.dashboard'));
        }

        RateLimiter::hit($this->throttleKey($request));

        throw ValidationException::withMessages([
            'login' => [trans('auth.failed')],
        ]);
    }

    /**
     * Proses logout calon siswa.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('applicant.login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Tampilkan form lupa password.
     */
    public function showForgotPassword(): View
    {
        return view('portal.auth.forgot-password');
    }

    /**
     * Kirim email link reset password.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', trans($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    /**
     * Tampilkan form reset password baru.
     */
    public function showResetForm(Request $request, $token = null): View
    {
        return view('portal.auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Simpan password baru setelah direset.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            // Callback standard laravel untuk menyimpan password yang direset
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('applicant.login')
                ->with('success', trans($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    /**
     * Memastikan request tidak melebihi batas percobaan login (5 kali).
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'login' => [
                trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ])
            ],
        ]);
    }

    /**
     * Mendapatkan key unik throttle berdasarkan input login dan IP address.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('login')) . '|' . $request->ip());
    }
}
