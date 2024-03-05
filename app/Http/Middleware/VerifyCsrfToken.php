<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/admin/login',
        '/register',
        '/admin/authenticate',
        '/admin/dashboard',
        '/account/login',
        '/account/register',
        '/account/process-register',
        '/process-checkout',
    ];
}
