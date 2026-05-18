<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function showForm(): View
    {
        return view('auth::login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $user = $this->authService->login(
            email: $request->email,
            password: $request->password,
            remember: $request->boolean('remember'),
        );

        return redirect()->intended(
            $user->isAdmin() ? route('admin.dashboard') : route('products.index')
        );
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('login')->with('success', 'Đã đăng xuất thành công.');
    }
}
