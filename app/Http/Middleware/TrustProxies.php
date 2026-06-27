<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Trust all proxies (Railway, Render, etc.).
     *
     * @var string|array<int, string>|null
     */
    protected $proxies = '*';
}
