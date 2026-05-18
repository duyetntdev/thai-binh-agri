<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a user by their email address.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Count users by role.
     */
    public function countByRole(UserRole $role): int;

    /**
     * Get paginated users, optionally filtered by role or search term.
     *
     * @param array{role?: string, search?: string} $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
}
