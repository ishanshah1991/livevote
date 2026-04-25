<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exceptions\DuplicateEmailException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\AdminRegisterRequest;
use App\Services\Auth\AdminAuthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Thin HTTP layer for admin auth — all real work is delegated to {@see AdminAuthService}.
 */
final class AuthController extends Controller
{
    /**
     * @param  AdminAuthService  $authService
     */
    public function __construct(private readonly AdminAuthService $authService)
    {
    }

    /**
     * Render the registration form.
     *
     * @return View
     */
    public function showRegister(): View
    {
        return view('admin.auth.register');
    }

    /**
     * Handle the registration submission.
     *
     * @param  AdminRegisterRequest  $request
     * @return RedirectResponse
     */
    public function register(AdminRegisterRequest $request): RedirectResponse
    {
        try {
            $this->authService->register($request->validated());
        } catch (DuplicateEmailException $e) {
            return back()
                ->withErrors(['email' => $e->getMessage()])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        return redirect()
            ->route('admin.register')
            ->with('status', 'Registration successful. Please log in.');
    }

    /**
     * Render the login form.
     *
     * @return View
     */
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Handle the login submission.
     *
     * @param  AdminLoginRequest  $request
     * @return RedirectResponse
     */
    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! $this->authService->attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Invalid credentials'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.register'));
    }
}
