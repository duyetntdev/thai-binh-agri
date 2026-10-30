<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DatabaseTransaction
{
    /**
     * Run a database-backed request atomically.
     *
     * @throws Throwable The exception is rethrown after Laravel rolls back the transaction.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return DB::transaction(fn () => $next($request));
    }
}
