<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['role', 'search']);
        $users = $this->userRepository->paginate($filters, 20);
        $roles = UserRole::cases();

        return view('admin::users.index', compact('users', 'filters', 'roles'));
    }

    public function show(User $user): View
    {
        $user->load('orders');

        return view('admin::users.show', compact('user'));
    }
}
