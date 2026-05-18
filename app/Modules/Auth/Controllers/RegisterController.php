<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function showForm(): View
    {
        return view('auth::register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $this->authService->register($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Đăng ký thành công! Chào mừng bạn đến với Nông Sản Thái Bình.');
    }
}
