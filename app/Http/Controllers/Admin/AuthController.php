<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exceptions\DuplicateEmailException;
use App\Http\Controllers\Controller;
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
}
